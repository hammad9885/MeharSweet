<?php

namespace App\Http\Controllers;

use App\Models\AccountMaster;
use App\Models\ConsumptionDetail;
use App\Models\ConsumptionMaster;
use App\Models\ItemMaster;
use App\Models\Level2;
use App\Models\Mold;
use App\Models\Module;
use App\Models\ProductMaster;
use App\Models\StockIssueDetail;
use App\Models\StockIssueMaster;  
use App\Models\UserPermission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockIssueController extends Controller
{
    public function index(Request $request)
    {
        $module = Module::where('name', 'Stock Issue')->first();
        if ($module) {
            $perm = UserPermission::where('module_id', $module->id)
                ->where('cid', auth()->user()->cid)
                ->where('userid', auth()->user()->id)
                ->where('read', 1);
            if (!$perm->exists()) {
                return redirect()->back()->with('error', 'You have not Permission. Please Contact with your admin');
            }
        }

        $cid = Auth::user()->cid;

        $query = StockIssueMaster::with(['details.product', 'product', 'contractor'])
            ->where('cid', $cid);

        if (request('inv_no')) {
            $query->where('inv_no', request('inv_no'));
        }

        if (request('product_id')) {
            $query->where('product_id', request('product_id'));
        }

        if (request('contractor_id')) {
            $query->where('contractor_id', request('contractor_id'));
        }

        if (request('startDate')) {
            $query->where('date', '>=', Carbon::parse(request('startDate'))->format('Y-m-d'));
        }

        if (request('endDate')) {
            $query->where('date', '<=', Carbon::parse(request('endDate'))->format('Y-m-d'));
        }

        $masters = $query->orderByDesc('date')
            ->orderByDesc('inv_no')
            ->get();

        $products = ProductMaster::where('cid', $cid)->get();

        $contractorLevel = Level2::where('cid', $cid)->where('title', 'Contractor')->value('id');
        $contractors = AccountMaster::where('cid', $cid);
        if ($contractorLevel) {
            $contractors = $contractors->where('level2_id', $contractorLevel);
        }
        $contractors = $contractors->get();

        return \Inertia\Inertia::render('Inventory/StockIssue/Index', [
            'masters'     => $masters,
            'products'    => $products,
            'contractors' => $contractors,
            'filters'     => $request->only(['inv_no', 'product_id', 'contractor_id', 'startDate', 'endDate']),
        ]);
    }

    public function create()
    {
        $cid = Auth::user()->cid;
        $products = ProductMaster::where('cid', $cid)->get();

        $contractorLevel = Level2::where('cid', $cid)->where('title', 'Contractor')->value('id');
        $contractors = AccountMaster::where('cid', $cid);
        if ($contractorLevel) {
            $contractors = $contractors->where('level2_id', $contractorLevel);
        }
        $contractors = $contractors->get();

        $lastInv = StockIssueMaster::where('cid', $cid)->max('inv_no');
        $nextInvNo = $lastInv ? ((int) $lastInv + 1) : 1;

        $previousBalances = StockIssueDetail::where('cid', $cid)
            ->where('inv_no', '<', $nextInvNo)
            ->groupBy('product_id')
            ->selectRaw('product_id, SUM(qty) as total')
            ->pluck('total', 'product_id');

        $contractorByProduct = $this->contractorByPackingMold($cid);

        return \Inertia\Inertia::render('Inventory/StockIssue/CreateEdit', compact('products', 'contractors', 'nextInvNo', 'previousBalances', 'contractorByProduct'));
    }

    public function store(Request $request)
    {
        if (!$request->has('details') || empty(array_filter($request->details, function ($d) {
            return !empty($d['product_id']);
        }))) {
            return redirect()->back()->withInput()->withErrors(['details' => 'Please add at least one item.']);
        }

        $filteredDetails = array_values(array_filter($request->details, function ($d) {
            return !empty($d['product_id']);
        }));
        $request->merge(['details' => $filteredDetails]);

        $request->validate([
            'inv_no'              => 'required|integer|unique:stock_issue_masters,inv_no',
            'date'                => 'required|date',
            'product_id'          => 'nullable|exists:product_masters,id',
            'contractor_id'       => 'nullable|exists:account_masters,id',
            'lot_no'              => 'nullable|string',
            'details'             => 'required|array|min:1',
            'details.*.product_id'    => 'required|exists:item_masters,id',
            'details.*.qty'           => 'required|numeric|min:0',
        ]);

        $cid = Auth::user()->cid;

        $master = StockIssueMaster::create([
            'cid'           => $cid,
            'inv_no'        => $request->inv_no,
            'date'          => $request->date,
            'product_id'    => $request->product_id,
            'contractor_id' => $request->contractor_id,
            'lot_no'        => $request->lot_no,
        ]);

        foreach ($request->details as $detail) {
            if (empty($detail['product_id'])) {
                continue;
            }
            StockIssueDetail::create([
                'cid'        => $cid,
                'inv_no'     => $request->inv_no,
                'product_id' => $detail['product_id'],
                'qty'        => $detail['qty'] ?? 0,
            ]);
        }

        return redirect()->route('stock-issue.index')->with('success', "Stock Issue #$request->inv_no created successfully!");
    }

    public function edit($id)
    {
        $cid = Auth::user()->cid;
        $stockIssue = StockIssueMaster::with(['details'])->where('cid', $cid)->findOrFail($id);

        $products = ProductMaster::where('cid', $cid)->get();

        $contractorLevel = Level2::where('cid', $cid)->where('title', 'Contractor')->value('id');
        $contractors = AccountMaster::where('cid', $cid);
        if ($contractorLevel) {
            $contractors = $contractors->where('level2_id', $contractorLevel);
        }
        $contractors = $contractors->get();

        $previousBalances = StockIssueDetail::where('cid', $cid)
            ->where('inv_no', '<', $stockIssue->inv_no)
            ->groupBy('product_id')
            ->selectRaw('product_id, SUM(qty) as total')
            ->pluck('total', 'product_id');

        $contractorByProduct = $this->contractorByPackingMold($cid);

        $stockIssue->load('details');
        return \Inertia\Inertia::render('Inventory/StockIssue/CreateEdit', compact('stockIssue', 'products', 'contractors', 'previousBalances', 'contractorByProduct'));
    }

    public function update(Request $request, $id)
    {
        if (!$request->has('details') || empty(array_filter($request->details, function ($d) {
            return !empty($d['product_id']);
        }))) {
            return redirect()->back()->withInput()->withErrors(['details' => 'Please add at least one item.']);
        }

        $filteredDetails = array_values(array_filter($request->details, function ($d) {
            return !empty($d['product_id']);
        }));
        $request->merge(['details' => $filteredDetails]);

        $cid = Auth::user()->cid;
        $stockIssue = StockIssueMaster::where('cid', $cid)->findOrFail($id);

        $request->validate([
            'date'                => 'required|date',
            'product_id'          => 'nullable|exists:product_masters,id',
            'contractor_id'       => 'nullable|exists:account_masters,id',
            'lot_no'              => 'nullable|string',
            'details'             => 'required|array|min:1',
            'details.*.product_id'    => 'required|exists:item_masters,id',
            'details.*.qty'           => 'required|numeric|min:0',
        ]);

        $stockIssue->update([
            'date'          => $request->date,
            'product_id'    => $request->product_id,
            'contractor_id' => $request->contractor_id,
            'lot_no'        => $request->lot_no,
        ]);

        StockIssueDetail::where('cid', $cid)->where('inv_no', $stockIssue->inv_no)->delete();

        foreach ($request->details as $detail) {
            if (empty($detail['product_id'])) {
                continue;
            }
            StockIssueDetail::create([
                'cid'        => $cid,
                'inv_no'     => $stockIssue->inv_no,
                'product_id' => $detail['product_id'],
                'qty'        => $detail['qty'] ?? 0,
            ]);
        }

        return redirect()->route('stock-issue.index')->with('success', "Stock Issue #$stockIssue->inv_no updated successfully!");
    }

    public function destroy($id)
    {
        $cid = Auth::user()->cid;
        $stockIssue = StockIssueMaster::where('cid', $cid)->findOrFail($id);

        StockIssueDetail::where('cid', $cid)->where('inv_no', $stockIssue->inv_no)->delete();
        $stockIssue->delete();

        return redirect()->route('stock-issue.index')->with('success', "Stock Issue #$stockIssue->inv_no deleted successfully!");
    }

    public function getMoldQty($productId, $itemId)
    {
        $cid = Auth::user()->cid;

        $latest = ConsumptionMaster::where('cid', $cid)
            ->where('item_id', $productId)
            ->latest()
            ->first();

        if ($latest) {
            $detail = ConsumptionDetail::where('cid', $cid)
                ->where('v_no', $latest->v_no)
                ->where('item_id', $itemId)
                ->latest()
                ->first();

            if ($detail && $detail->qty !== null && $detail->qty !== '') {
                return response()->json(['mold_qty' => (float) $detail->qty]);
            }

            if ($latest->mold_qty !== null) {
                return response()->json(['mold_qty' => (float) $latest->mold_qty]);
            }
        }

        return response()->json(['mold_qty' => null]);
    }

    public function getItemsByProduct($productId)
    {
        $cid = Auth::user()->cid;

        $itemIds = ConsumptionDetail::where('cid', $cid)
            ->whereIn('v_no', function ($q) use ($productId, $cid) {
                $q->select('v_no')
                    ->from('consumption_masters')
                    ->where('cid', $cid)
                    ->where('item_id', $productId);
            })
            ->distinct()
            ->pluck('item_id');

        $items = ItemMaster::where('cid', $cid)
            ->whereIn('id', $itemIds)
            ->orderBy('item_code')
            ->get(['id', 'item_code']);

        return response()->json($items);
    }

    private function contractorByPackingMold($cid)
    {
        $packingMoldIds = Mold::where(function ($q) use ($cid) {
            $q->where('cid', $cid)->orWhereNull('cid');
        })
        ->where(function ($q) {
            $q->where('mold_name', 'like', '%packing%')
              ->orWhere('mold_id', 'like', '%packing%');
        })
        ->pluck('id');

        $query = ConsumptionMaster::where('cid', $cid)
            ->whereNotNull('item_id')
            ->whereNotNull('contractor_account_id');

        if ($packingMoldIds->isNotEmpty()) {
            $query->whereIn('mold_id', $packingMoldIds);
        }

        return $query->orderByDesc('id')
            ->get(['item_id', 'contractor_account_id'])
            ->groupBy('item_id')
            ->map(function ($rows) {
                return $rows->first()->contractor_account_id;
            });
    }
}
