<?php

namespace App\Http\Controllers;

use App\Models\AccountMaster;
use App\Models\ConsumptionMaster;
use App\Models\ConsumptionDetail;
use App\Models\ErpParam;
use App\Models\ExpenseDetail;
use App\Models\ExpenseMaster;
use App\Models\Item;
use App\Models\ItemMaster;
use App\Models\Level2;
use App\Models\Module;
use App\Models\Mold;
use App\Models\ProductMaster;
use App\Models\PurchaseInvoice;
use App\Models\UserPermission;
use Illuminate\Http\Request;

class Consumption2Controller extends Controller
{
    public function index(Request $request)
    {
        $module = Module::where('name', 'Consumption')->first();
        if ($module) {
            $perm = UserPermission::where('module_id', $module->id)->where('cid', auth()->user()->cid)->where('userid', auth()->user()->id)->where('read', 1);
            if (!$perm->exists()) {
                return redirect()->back()->with('error', 'You have not Permission. Please Contact with your admin');
            }
        }
        $query = ConsumptionMaster::with(['details' => function ($q) {
            $q->where('cid', auth()->user()->cid);
        }])->where('cid', auth()->user()->cid);

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        $consumptions = $query->orderByDesc('date')->get();

        $products = ProductMaster::whereIn('id', function ($query) {
            $query->select('item_id')
                  ->from('consumption_masters')
                  ->where('cid', auth()->user()->cid);
        })->distinct()->get()->keyBy('id');

        return \Inertia\Inertia::render('Manufacturing/Consumption2/Index', [
            'consumptions' => $consumptions,
            'products' => $products,
            'filters' => $request->all(),
        ]);
    }

    public function create()
{
    $cid = auth()->user()->cid;
    $module = Module::where('name', 'Consumption')->first();
    if ($module) {
        $perm = UserPermission::where('module_id', $module->id)->where('cid', $cid)->where('userid', auth()->user()->id)->where('write', 1);
        if (!$perm->exists()) {
            return redirect()->back()->with('error', 'You have not Permission. Please Contact with your admin');
        }
    }

    // Finished products
    $items = ItemMaster::where('cid', $cid)->where('item_type', 'fp')->get();
    $productMaster =ProductMaster::where('cid',auth()->user()->cid)->get();

    // Raw materials
    $raw = ItemMaster::with('itemunits')->where('cid', $cid)->get();

    // Expense accounts (level2 linked to group 5)
    $expenseAccounts = AccountMaster::whereIn('level2_id', function ($query) use ($cid) {
        $query->select('id')
              ->from('level2s')
              ->where('cid', $cid)
              ->whereIn('level1_id', function ($subquery) use ($cid) {
                  $subquery->select('id')
                           ->from('level1s')
                           ->where('cid', $cid)
                           ->whereIn('group_id', [5]);
              });
    })->get();

    // Molds for dropdown
    $molds = Mold::where('cid', $cid)->get();

    // Latest purchase invoice rates by item
    $latestRates = PurchaseInvoice::where('purchase_invoices.cid', $cid)
        ->whereNotNull('purchase_invoices.rate')
        ->where('purchase_invoices.rate', '>', 0)
        ->leftJoin('purchase_master', function ($join) use ($cid) {
            $join->on('purchase_invoices.v_no', '=', 'purchase_master.v_no')
                 ->where('purchase_master.cid', '=', $cid);
        })
        ->orderByDesc('purchase_master.v_date')
        ->orderByDesc('purchase_invoices.v_no')
        ->orderByDesc('purchase_invoices.id')
        ->get(['purchase_invoices.item_id', 'purchase_invoices.rate'])
        ->groupBy('item_id')
        ->map(function ($group) {
            return (float) $group->first()->rate;
        });

    // Contractors – only accounts under Level2 title 'Contractor'
    $contractorLevel = Level2::where('cid', $cid)->where('title', 'Contractor')->value('id');
    $contractors = AccountMaster::where('cid', $cid);
    if ($contractorLevel) {
        $contractors = $contractors->where('level2_id', $contractorLevel);
    }
    $contractors = $contractors->get();

    return \Inertia\Inertia::render('Manufacturing/Consumption2/CreateEdit', compact('items', 'raw', 'expenseAccounts', 'molds', 'contractors','productMaster', 'latestRates'));
}

    public function store(Request $request)
    {
        $request->validate([
            'date'           => 'required|date',
            'descr'          => 'nullable|string',
            'lot_no'         => 'nullable|string|max:255',
            'cost_price'     => 'nullable|string',
            'product'        => 'required|exists:product_masters,id',
            'product_qty'    => 'required|string|min:1',
            'details'        => 'required|array|min:1',
            'qty_per_carton' => 'nullable|numeric|min:0',
            'cartons_in_lot' => 'nullable|numeric|min:0',
            'lot_qty'        => 'nullable|numeric|min:0',
            'target_qty'     => 'nullable|numeric|min:0',
        ]);

        $cid = auth()->user()->cid;
        $allDetails = $request->details;
        $firstDetail = reset($allDetails);

        $v_no = ConsumptionMaster::where('cid', $cid)->max('v_no') + 1;

        $product = ProductMaster::find($request->product);
        if ($product && $request->filled('cost_price')) {
            $product->cost_price = $request->cost_price;
            $product->save();
        }

        $master = ConsumptionMaster::create([
            'cid'          => $cid,
            'v_no'         => $v_no,
            'date'         => $request->date,
            'descr'        => $request->descr,
            'cost_price'   => $request->filled('cost_price') ? $request->cost_price : null,
            'qty'          => $request->product_qty,
            'item_id'      => $request->product,
            'item_title'   => $firstDetail['item_id'] ?? null,
            'mold_id'      => $firstDetail['mold_id'] ?? null,
            'mold_qty'     => $firstDetail['mold_qty'] ?? null,
            'machine_capacity' => $firstDetail['machine_capacity'] ?? null,
            'stroke'       => $firstDetail['stroke'] ?? null,
            'grams'        => $firstDetail['grams'] ?? null,
            'contractor_account_id' => $firstDetail['contractor_account_id'] ?? null,
            'wages_rate'   => $firstDetail['wages_rate'] ?? null,
            'wages_amount' => $firstDetail['wages_amount'] ?? null,
            'lot_no'       => $request->lot_no ?? null,
            'lot_qty'      => $request->lot_qty ?? null,
            'qty_per_carton' => $request->qty_per_carton ?? null,
            'cartons_in_lot' => $request->cartons_in_lot ?? null,
            'target_qty'   => $request->target_qty ?? null,
            'calc_qty'     => $firstDetail['calc_qty'] ?? null,
            'unit'         => $firstDetail['unit'] ?? null,
            'material_rate'=> (isset($firstDetail['material_rate']) && trim($firstDetail['material_rate']) !== '' && is_numeric($firstDetail['material_rate'])) ? $firstDetail['material_rate'] : null,
            'mat_amount'   => $firstDetail['mat_amount'] ?? null,
            'hatt'         => $firstDetail['hatt'] ?? null,
        ]);

        foreach ($allDetails as $detail) {
            ConsumptionDetail::create([
                'cid'     => $cid,
                'v_no'    => $v_no,
                'item_id' => $detail['item_id'],
                'qty'     => $detail['mold_qty'] ?? 0,
                'rate'    => $detail['wages_rate'] ?? 0,
                'amount'  => $detail['wages_amount'] ?? 0,
                'mold_id' => $detail['mold_id'] ?? null,
                'machine_capacity' => $detail['machine_capacity'] ?? null,
                'stroke'  => $detail['stroke'] ?? null,
                'grams'   => $detail['grams'] ?? null,
                'calc_qty' => $detail['calc_qty'] ?? null,
                'unit'    => $detail['unit'] ?? null,
                'contractor_account_id' => $detail['contractor_account_id'] ?? null,
                'material_rate' => (isset($detail['material_rate']) && trim($detail['material_rate']) !== '' && is_numeric($detail['material_rate'])) ? $detail['material_rate'] : null,
                'mat_amount' => $detail['mat_amount'] ?? null,
                'hatt'    => $detail['hatt'] ?? null,
            ]);
        }

        return redirect()->route('consumption2.index')->with('success', 'Consumption added successfully!');
    }

   public function edit($id)
{
    $consumption = ConsumptionMaster::with('details')->findOrFail($id);
    $cid = auth()->user()->cid;
    $module = Module::where('name', 'Consumption')->first();
    if ($module) {
        $perm = UserPermission::where('module_id', $module->id)->where('cid', $cid)->where('userid', auth()->user()->id)->where('update', 1);
        if (!$perm->exists()) {
            return redirect()->back()->with('error', 'You have not Permission. Please Contact with your admin');
        }
    }

    $products = ProductMaster::where('cid', $cid)->get();
    $raw   = ItemMaster::with('itemunits')->where('cid', $cid)->get();

    $molds = Mold::where('cid', $cid)->get();

    // Latest purchase invoice rates by item
    $latestRates = PurchaseInvoice::where('purchase_invoices.cid', $cid)
        ->whereNotNull('purchase_invoices.rate')
        ->where('purchase_invoices.rate', '>', 0)
        ->leftJoin('purchase_master', function ($join) use ($cid) {
            $join->on('purchase_invoices.v_no', '=', 'purchase_master.v_no')
                 ->where('purchase_master.cid', '=', $cid);
        })
        ->orderByDesc('purchase_master.v_date')
        ->orderByDesc('purchase_invoices.v_no')
        ->orderByDesc('purchase_invoices.id')
        ->get(['purchase_invoices.item_id', 'purchase_invoices.rate'])
        ->groupBy('item_id')
        ->map(function ($group) {
            return (float) $group->first()->rate;
        });

    $contractorLevel = Level2::where('cid', $cid)->where('title', 'Contractor')->value('id');
    $contractors = AccountMaster::where('cid', $cid);
    if ($contractorLevel) {
        $contractors = $contractors->where('level2_id', $contractorLevel);
    }
    $contractors = $contractors->get();

    $productMaster = ProductMaster::where('cid', $cid)->orderBy('product_name')->get();
    return \Inertia\Inertia::render('Manufacturing/Consumption2/CreateEdit', [
        'isEdit' => true,
        'consumption' => $consumption,
        'products' => $products,
        'productMaster' => $productMaster,
        'raw' => $raw,
        'molds' => $molds,
        'contractors' => $contractors,
        'latestRates' => $latestRates,
    ]);
}
    public function update(Request $request, $id)
    {
        $consumption = ConsumptionMaster::findOrFail($id);

        $request->validate([
            'date'           => 'required|date',
            'descr'          => 'nullable|string',
            'lot_no'         => 'nullable|string|max:255',
            'details'        => 'required|array|min:1',
            'product'        => 'required',
            'cost_price'     => 'nullable|string',
            'product_qty'    => 'required|string|min:1',
            'lot_qty'        => 'nullable|numeric|min:0',
            'qty_per_carton' => 'nullable|numeric|min:0',
            'cartons_in_lot' => 'nullable|numeric|min:0',
            'target_qty'     => 'nullable|numeric|min:0',
        ]);

        // Get first detail row for master-level fields
        $allDetails = $request->details;
        $firstDetail = reset($allDetails);

        // Update Consumption Master
        $consumption->update([
            'date'         => $request->date,
            'descr'        => $request->descr,
            'lot_no'       => $request->lot_no,
            'item_id'      => $request->product,
            'cost_price'   => $request->filled('cost_price') ? $request->cost_price : $consumption->cost_price,
            'qty'          => $request->product_qty,
            'lot_qty'      => $request->lot_qty,
            'qty_per_carton' => $request->qty_per_carton,
            'cartons_in_lot' => $request->cartons_in_lot,
            'target_qty'   => $request->target_qty,

            // Master fields from first detail row (fall back to existing value)
            'item_title'   => $request->item_title ?? $firstDetail['item_title'] ?? $consumption->item_title,
            'mold_id'      => $firstDetail['mold_id'] ?? $consumption->mold_id,
            'mold_qty'     => $firstDetail['mold_qty'] ?? $consumption->mold_qty,
            'machine_capacity' => $firstDetail['machine_capacity'] ?? $consumption->machine_capacity,
            'stroke'       => $firstDetail['stroke'] ?? $consumption->stroke,
            'grams'        => $firstDetail['grams'] ?? $consumption->grams,
            'contractor_account_id' => $firstDetail['contractor_account_id'] ?? $consumption->contractor_account_id,
            'wages_rate'   => $firstDetail['wages_rate'] ?? $consumption->wages_rate,
            'wages_amount' => $firstDetail['wages_amount'] ?? $consumption->wages_amount,
            'calc_qty'     => $firstDetail['calc_qty'] ?? $consumption->calc_qty,
            'unit'         => $firstDetail['unit'] ?? $consumption->unit,
            'material_rate'=> (isset($firstDetail['material_rate']) && trim($firstDetail['material_rate']) !== '' && is_numeric($firstDetail['material_rate'])) ? $firstDetail['material_rate'] : null,
            'mat_amount'   => $firstDetail['mat_amount'] ?? $consumption->mat_amount,
            'hatt'         => $firstDetail['hatt'] ?? $consumption->hatt,
        ]);

        // Delete old details and insert new ones
        $consumption->details()->where('cid', auth()->user()->cid)->delete();

        foreach ($allDetails as $detail) {
            ConsumptionDetail::create([
                'cid'     => $consumption->cid,
                'v_no'    => $consumption->v_no,
                'item_id' => $detail['item_id'],
                'qty'     => $detail['mold_qty'] ?? 0,
                'rate'    => $detail['wages_rate'] ?? 0,
                'amount'  => $detail['wages_amount'] ?? 0,
                'mold_id' => $detail['mold_id'] ?? null,
                'machine_capacity' => $detail['machine_capacity'] ?? null,
                'stroke'  => $detail['stroke'] ?? null,
                'grams'   => $detail['grams'] ?? null,
                'calc_qty' => $detail['calc_qty'] ?? null,
                'unit'    => $detail['unit'] ?? null,
                'contractor_account_id' => $detail['contractor_account_id'] ?? null,
                'material_rate' => (isset($detail['material_rate']) && trim($detail['material_rate']) !== '' && is_numeric($detail['material_rate'])) ? $detail['material_rate'] : null,
                'mat_amount' => $detail['mat_amount'] ?? null,
                'hatt'    => $detail['hatt'] ?? null,
            ]);
        }

        return redirect()->route('consumption2.index')->with('success', 'Consumption updated successfully!');
    }

    public function getMaterialRate(Request $request, $itemId)
    {
        $cid = auth()->user()->cid;

        // Strictly fetch rate from the latest Purchase Invoice for this item
        $latestPurchase = PurchaseInvoice::where('purchase_invoices.cid', $cid)
            ->where('purchase_invoices.item_id', $itemId)
            ->whereNotNull('purchase_invoices.rate')
            ->where('purchase_invoices.rate', '>', 0)
            ->leftJoin('purchase_master', function ($join) use ($cid) {
                $join->on('purchase_invoices.v_no', '=', 'purchase_master.v_no')
                     ->where('purchase_master.cid', '=', $cid);
            })
            ->orderByDesc('purchase_master.v_date')
            ->orderByDesc('purchase_invoices.v_no')
            ->orderByDesc('purchase_invoices.id')
            ->select('purchase_invoices.rate')
            ->first();

        if ($latestPurchase && (float) $latestPurchase->rate > 0) {
            return response()->json([
                'rate' => (float) $latestPurchase->rate
            ]);
        }

        // If no purchase invoice rate found, return null (do not fallback to random/previous consumption rates)
        return response()->json([
            'rate' => null
        ]);
    }

    public function destroy($id)
    {
        $consumption = ConsumptionMaster::findOrFail($id);
        $consumption->details()->where('cid', auth()->user()->cid)->delete();
        $consumption->delete();

        return redirect()->route('consumption2.index')->with('success', 'Consumption deleted!');
    }

    public function print($id)
    {
        $consumption = ConsumptionMaster::with(['details' => function ($query) {
            $query->where('cid', auth()->user()->cid);
        }])->where('cid', auth()->user()->cid)->findOrFail($id);

        return view('consumption2.print', compact('consumption'));
    }
}