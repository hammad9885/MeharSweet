<?php

namespace App\Http\Controllers;

use App\Models\ConsumptionMaster;
use App\Models\Module;
use App\Models\ProductionMaster;
use App\Models\ProductionDetail;
use App\Models\ProductionConsumed;
use App\Models\ItemMaster;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductionController extends Controller
{
    // 📜 Show all productions
    public function index(Request $request)
    {
        $cid = auth()->user()->cid;
        $module = Module::where('name', 'Production')->first();
        if ($module) {
            $perm = UserPermission::where('module_id', $module->id)->where('cid', $cid)->where('userid', auth()->user()->id)->where('read', 1);
            if (!$perm->exists()) {
                return redirect()->back()->with('error', 'You have not Permission. Please Contact with your admin');
            }
        }

        $query = ProductionMaster::with(['details' => function ($query) use ($cid) {
                $query->where('cid', $cid);
            }])
            ->where('cid', $cid);

        // ✅ Apply filters
        if ($request->filled('start_date')) {
            $query->whereDate('v_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('v_date', '<=', $request->end_date);
        }

        if ($request->filled('v_no')) {
            $query->where('v_no', $request->v_no);
        }

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        // ✅ Get unique items used in production
        $items = ItemMaster::whereIn('id', function ($query) use ($cid) {
                $query->select('item_id')
                    ->from('production_detail')
                    ->where('cid', $cid);
            })
            ->distinct()
            ->get();

        $productions = $query->orderByDesc('v_date')->get();

        // ✅ Distinct voucher numbers
        $vNoList = ProductionMaster::where('cid', $cid)
            ->select('v_no')
            ->distinct()
            ->orderBy('v_no', 'desc')
            ->pluck('v_no');

        return \Inertia\Inertia::render('Manufacturing/Production/Index', [
            'productions' => $productions,
            'vNoList' => $vNoList,
            'items' => $items,
            'filters' => $request->all(),
        ]);
    }

    // 🆕 Show create form
    public function create()
    {
        $cid = auth()->user()->cid;
        $module = Module::where('name', 'Production')->first();
        if ($module) {
            $perm = UserPermission::where('module_id', $module->id)->where('cid', $cid)->where('userid', auth()->user()->id)->where('write', 1);
            if (!$perm->exists()) {
                return redirect()->back()->with('error', 'You have not Permission. Please Contact with your admin');
            }
        }
        $items = ItemMaster::where('cid', $cid)
            ->where('item_type', 'fp')
            ->get();

        return \Inertia\Inertia::render('Manufacturing/Production/CreateEdit', compact('items'));
    }

    // 💾 Store new production
    public function store(Request $request)
    {
        $request->validate([
            'v_date' => 'required|date',
            'descr' => 'nullable|string',
            'items.*.item_id' => 'required',
            'items.*.qty' => 'required|numeric',
            'items.*.rate' => 'required|numeric',
        ]);

        $cid = auth()->user()->cid;

        DB::transaction(function () use ($request, $cid) {
            // Generate next voucher number per company
            $max = ProductionMaster::where('cid', $cid)->max('v_no');
            $vno = ($max ?? 0) + 1;
            $total = 0;

            foreach ($request->items as $item) {
                $itemId = $item['item_id'];
                $qty = $item['qty'];
                $rate = $item['rate'];
                $amount = $qty * $rate;

                // ✅ Insert details
                ProductionDetail::create([
                    'v_no' => $vno,
                    'item_id' => $itemId,
                    'qty' => $qty,
                    'rate' => $rate,
                    'amount' => $amount,
                    'cid' => $cid
                ]);

                $total += $amount;

                // ✅ Fetch related consumption masters
                $fpMasters = ConsumptionMaster::where('cid', $cid)
                    ->where('item_id', $itemId)
                    ->where('is_expired', 0)
                    ->with(['details' => function ($query) use ($cid) {
                        $query->where('cid', $cid);
                    }])
                    ->get();

                foreach ($fpMasters as $fp) {
                    foreach ($fp->details as $rp) {
                        ProductionConsumed::create([
                            'v_no' => $vno,
                            'fp_item_id' => $fp->item_id,
                            'rp_item_id' => $rp->item_id,
                            'qty' => $qty * ($rp->qty / $fp->qty),
                            'rate' => $rp->rate,
                            'cid' => $cid
                        ]);
                    }
                }
            }

            // ✅ Create master record
            ProductionMaster::create([
                'v_no' => $vno,
                'v_date' => $request->v_date,
                'descr' => $request->descr,
                'amount' => $total,
                'cid' => $cid,
            ]);
        });

        return redirect()->route('production.index')->with('success', 'Production record created successfully.');
    }

    // ✏️ Edit
    public function edit($id)
    {
        $cid = auth()->user()->cid;
        $module = Module::where('name', 'Production')->first();
        if ($module) {
            $perm = UserPermission::where('module_id', $module->id)->where('cid', $cid)->where('userid', auth()->user()->id)->where('update', 1);
            if (!$perm->exists()) {
                return redirect()->back()->with('error', 'You have not Permission. Please Contact with your admin');
            }
        }

        $production = ProductionMaster::where('id', $id)
            ->where('cid', $cid)
            ->with(['details' => function ($query) use ($cid) {
                $query->where('cid', $cid);
            }])
            ->firstOrFail();

        $items = ItemMaster::where('cid', $cid)
            ->where('item_type', 'fp')
            ->get();

        return \Inertia\Inertia::render('Manufacturing/Production/CreateEdit', [
            'isEdit' => true,
            'production' => $production,
            'items' => $items,
        ]);
    }

    // 🔄 Update
    public function update(Request $request, $id)
    {
        $request->validate([
            'v_date' => 'required|date',
            'details.*.item_id' => 'required|integer',
            'details.*.qty' => 'required|numeric|min:1',
            'details.*.rate' => 'required|numeric|min:0',
        ]);

        $cid = auth()->user()->cid;

        DB::transaction(function () use ($request, $id, $cid) {
            // ✅ Fetch only if belongs to same company
            $master = ProductionMaster::where('id', $id)
                ->where('cid', $cid)
                ->firstOrFail();

            // ✅ Delete old details safely
            ProductionDetail::where('v_no', $master->v_no)
                ->where('cid', $cid)
                ->delete();

            // ✅ Recalculate total
            $total = 0;
            foreach ($request->details as $detail) {
                $total += $detail['qty'] * $detail['rate'];
            }

            // ✅ Update master
            $master->update([
                'v_date' => $request->v_date,
                'descr' => $request->descr,
                'amount' => $total,
            ]);

            // ✅ Insert new details
            foreach ($request->details as $detail) {
                ProductionDetail::create([
                    'v_no' => $master->v_no,
                    'item_id' => $detail['item_id'],
                    'qty' => $detail['qty'],
                    'cid' => $cid,
                    'rate' => $detail['rate'],
                    'amount' => $detail['qty'] * $detail['rate'],
                ]);
            }
        });

        // ✅ Handle AJAX and normal form
        if ($request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Production updated successfully']);
        }

        return redirect()->route('production.index')->with('success', 'Production record updated successfully.');
    }

    // ❌ Delete
    public function destroy($id)
    {
        $cid = auth()->user()->cid;

        $master = ProductionMaster::where('id', $id)
            ->where('cid', $cid)
            ->firstOrFail();

        // ✅ Delete related records for same company only
        ProductionDetail::where('v_no', $master->v_no)
            ->where('cid', $cid)
            ->delete();

        ProductionConsumed::where('v_no', $master->v_no)
            ->where('cid', $cid)
            ->delete();

        $master->delete();

        return redirect()->route('production.index')
            ->with('success', 'Production record deleted successfully.');
    }
    public function print($id)
{
    $cid = auth()->user()->cid;

    // ✅ Fetch production master + details for same company only
    $production = ProductionMaster::where('id', $id)
        ->where('cid', $cid)
        ->with([
            'details' => function ($q) use ($cid) {
                $q->where('cid', $cid)
                  ->with(['item' => function ($i) use ($cid) {
                      $i->where('cid', $cid);
                  }]);
            },
        ])
        ->firstOrFail();

    return view('production.print', compact('production'));
}

}
