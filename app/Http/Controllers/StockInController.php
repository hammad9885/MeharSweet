<?php

namespace App\Http\Controllers;

use App\Models\AccountMaster;
use App\Models\ConsumptionMaster;
use App\Models\Contractor;
use App\Models\ItemMaster;
use App\Models\Level2;
use App\Models\Mold;
use App\Models\Module;
use App\Models\ProductMaster;
use App\Models\ReferenceName;
use App\Models\StockInDetail;
use App\Models\StockInMaster;
use App\Models\UserPermission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockInController extends Controller
{
    public function index(Request $request)
    {
        $module = Module::where('name', 'Stock in')->first();
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

        $query = StockInMaster::with(['details.product', 'details.contractor', 'refName'])
            ->where('cid', $cid);

        if (request('inv_no')) {
            $query->where('inv_no', request('inv_no'));
        }

        if (request('ref_name_id')) {
            $query->where('ref_name_id', request('ref_name_id'));
        }

        if (request('product_id')) {
            $query->whereHas('details', function ($q) {
                $q->where('product_id', request('product_id'));
            });
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

        $referenceNames = ReferenceName::where('cid', $cid)->orderByDesc('id')->get();
        $products = ProductMaster::where('cid', $cid)->get();

        return view('stock-in.index', compact('masters', 'referenceNames', 'products'));
    }

    public function create()
    {
        $cid = Auth::user()->cid;
        $referenceNames = ReferenceName::where('cid', $cid)->orderByDesc('id')->get();
        $products = ProductMaster::where('cid', $cid)->get();

        $contractorLevel = Level2::where('cid', $cid)->where('title', 'Contractor')->value('id');
        $contractors = AccountMaster::where('cid', $cid);
        if ($contractorLevel) {
            $contractors = $contractors->where('level2_id', $contractorLevel);
        }
        $contractors = $contractors->get();

        $lastInv = StockInMaster::where('cid', $cid)->max('inv_no');
        $nextInvNo = $lastInv ? ((int) $lastInv + 1) : 1;

        $previousBalancesData = StockInDetail::where('cid', $cid)
            ->where('inv_no', '<', $nextInvNo)
            ->whereNotNull('product_id')
            ->groupBy('product_id', 'lot_no')
            ->selectRaw("product_id, COALESCE(lot_no, '') as lot_no, SUM(qty) as total")
            ->get();

        $previousBalances = [];
        foreach ($previousBalancesData as $pb) {
            $key = $pb->product_id . '_' . trim((string) $pb->lot_no);
            $previousBalances[$key] = (float) $pb->total;
        }

        // Contractor of each product = contractor of the "Packing" item row
        // (Raw Material/Item column) in the product's latest consumption
        // record. Falls back to the consumption record's own contractor.
        $contractorByProduct = $this->contractorByPackingMold($cid);

        return view('stock-in.create', compact('referenceNames', 'products', 'contractors', 'nextInvNo', 'previousBalances', 'contractorByProduct'));
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
            'inv_no'              => 'required|integer|unique:stock_in_masters,inv_no',
            'date'                => 'required|date',
            'ref_name_id'         => 'nullable|exists:reference_names,id',
            'dala_no'             => 'nullable|string',
            'details'             => 'required|array|min:1',
            'details.*.product_id'    => 'required|exists:product_masters,id',
            'details.*.contractor_id' => 'nullable|exists:account_masters,id',
            'details.*.lot_no'        => 'nullable|string',
            'details.*.qty'           => 'required|numeric|min:0',
        ]);

        $cid = Auth::user()->cid;

        $master = StockInMaster::create([
            'cid'        => $cid,
            'inv_no'     => $request->inv_no,
            'date'       => $request->date,
            'ref_name_id'=> $request->ref_name_id,
            'dala_no'    => $request->dala_no,
        ]);

        foreach ($request->details as $detail) {
            if (empty($detail['product_id'])) {
                continue;
            }
            StockInDetail::create([
                'cid'           => $cid,
                'inv_no'        => $request->inv_no,
                'product_id'    => $detail['product_id'],
                'contractor_id' => $detail['contractor_id'] ?? null,
                'lot_no'        => $detail['lot_no'] ?? null,
                'qty'           => $detail['qty'] ?? 0,
            ]);
        }

        PackingLotPostingController::autoGenerateCompletedLots($cid);
        ContractorLotPostingController::autoGenerateLots($cid);

        return redirect()->route('stock-in.index')->with('success', "Stock In #$request->inv_no created successfully!");
    }

    public function edit($id)
    {
        $cid = Auth::user()->cid;
        $stockIn = StockInMaster::with(['details'])->where('cid', $cid)->findOrFail($id);

        $referenceNames = ReferenceName::where('cid', $cid)->orderByDesc('id')->get();
        $products = ProductMaster::where('cid', $cid)->get();

        $contractorLevel = Level2::where('cid', $cid)->where('title', 'Contractor')->value('id');
        $contractors = AccountMaster::where('cid', $cid);
        if ($contractorLevel) {
            $contractors = $contractors->where('level2_id', $contractorLevel);
        }
        $contractors = $contractors->get();

        $previousBalancesData = StockInDetail::where('cid', $cid)
            ->where('inv_no', '<', $stockIn->inv_no)
            ->whereNotNull('product_id')
            ->groupBy('product_id', 'lot_no')
            ->selectRaw("product_id, COALESCE(lot_no, '') as lot_no, SUM(qty) as total")
            ->get();

        $previousBalances = [];
        foreach ($previousBalancesData as $pb) {
            $key = $pb->product_id . '_' . trim((string) $pb->lot_no);
            $previousBalances[$key] = (float) $pb->total;
        }

        // Contractor of each product = contractor of the "Packing" item row
        // (Raw Material/Item column) in the product's latest consumption
        // record. Falls back to the consumption record's own contractor.
        $contractorByProduct = $this->contractorByPackingMold($cid);

        return view('stock-in.edit', compact('stockIn', 'referenceNames', 'products', 'contractors', 'previousBalances', 'contractorByProduct'));
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
        $stockIn = StockInMaster::where('cid', $cid)->findOrFail($id);

        $request->validate([
            'date'                => 'required|date',
            'ref_name_id'         => 'nullable|exists:reference_names,id',
            'dala_no'             => 'nullable|string',
            'details'             => 'required|array|min:1',
            'details.*.product_id'    => 'required|exists:product_masters,id',
            'details.*.contractor_id' => 'nullable|exists:account_masters,id',
            'details.*.lot_no'        => 'nullable|string',
            'details.*.qty'           => 'required|numeric|min:0',
        ]);

        $stockIn->update([
            'date'        => $request->date,
            'ref_name_id' => $request->ref_name_id,
            'dala_no'     => $request->dala_no,
        ]);

        StockInDetail::where('cid', $cid)->where('inv_no', $stockIn->inv_no)->delete();

        foreach ($request->details as $detail) {
            if (empty($detail['product_id'])) {
                continue;
            }
            StockInDetail::create([
                'cid'           => $cid,
                'inv_no'        => $stockIn->inv_no,
                'product_id'    => $detail['product_id'],
                'contractor_id' => $detail['contractor_id'] ?? null,
                'lot_no'        => $detail['lot_no'] ?? null,
                'qty'           => $detail['qty'] ?? 0,
            ]);
        }

        PackingLotPostingController::autoGenerateCompletedLots($cid);
        ContractorLotPostingController::autoGenerateLots($cid);

        return redirect()->route('stock-in.index')->with('success', "Stock In #$stockIn->inv_no updated successfully!");
    }

    public function print($id)
    {
        $cid = Auth::user()->cid;
        $stockIn = StockInMaster::with(['details.product', 'details.contractor', 'refName'])
            ->where('cid', $cid)
            ->findOrFail($id);

        // Previous stock qty per product and lot_no (all inv_no < current)
        $previousBalancesData = StockInDetail::where('cid', $cid)
            ->where('inv_no', '<', $stockIn->inv_no)
            ->groupBy('product_id', 'lot_no')
            ->selectRaw('product_id, lot_no, SUM(qty) as total')
            ->get();

        $previousBalances = [];
        foreach ($previousBalancesData as $row) {
            $lot = $row->lot_no ?: '';
            $previousBalances[$row->product_id][$lot] = $row->total;
        }

        return view('stock-in.print', compact('stockIn', 'previousBalances'));
    }

    public function destroy($id)
    {
        $cid = Auth::user()->cid;
        $stockIn = StockInMaster::where('cid', $cid)->findOrFail($id);

        // Get all product+lot combos from this Stock In BEFORE deleting
        $details = StockInDetail::where('cid', $cid)
            ->where('inv_no', $stockIn->inv_no)
            ->whereNotNull('product_id')
            ->whereNotNull('lot_no')
            ->where('lot_no', '!=', '')
            ->get(['product_id', 'lot_no']);

        // Delete stock in details and master
        StockInDetail::where('cid', $cid)->where('inv_no', $stockIn->inv_no)->delete();
        $stockIn->delete();

        // For each product+lot, check if any OTHER stock_in still has it
        // If not, soft-delete the corresponding CLP master
        foreach ($details as $detail) {
            $stillExists = StockInDetail::where('cid', $cid)
                ->where('product_id', $detail->product_id)
                ->where('lot_no', $detail->lot_no)
                ->exists();

            if (!$stillExists) {
                // No other stock_in has this product+lot — soft-delete CLP master
                \App\Models\ContractorLotPostingMaster::where('cid', $cid)
                    ->where('product_id', $detail->product_id)
                    ->where('lot_no', $detail->lot_no)
                    ->whereNull('deleted_at')
                    ->update(['deleted_at' => now()]);
            }
        }

        return redirect()->route('stock-in.index')->with('success', "Stock In #$stockIn->inv_no deleted successfully!");
    }


    public function getProductData(Request $request)
    {
        $cid = Auth::user()->cid;
        $productId = $request->product_id;
        $contractorId = $request->contractor_id;
        $lotNo = $request->lot_no;
        $invNo = $request->inv_no ?? PHP_INT_MAX;

        if (!$productId) {
            return response()->json([
                'lot_no' => '',
                'previous' => 0,
                'cartons_in_lot' => 0,
                'total_cartons_in_lot' => 0
            ]);
        }

        // SUM of all cartons_in_lot for this product across all consumption records
        $totalCartonsInLot = (float) ConsumptionMaster::where('cid', $cid)
            ->where('item_id', $productId)
            ->whereNotNull('cartons_in_lot')
            ->where('cartons_in_lot', '>', 0)
            ->sum('cartons_in_lot');

        $baseQuery = StockInDetail::where('cid', $cid)
            ->where('product_id', $productId)
            ->where('inv_no', '<', $invNo);

        $query = clone $baseQuery;
        if ($request->filled('contractor_id')) {
            $query->where('contractor_id', $contractorId);
        }

        // 1. If lot_no is explicitly provided (e.g. user manually changed or typed lot_no)
        if ($request->filled('lot_no')) {
            $previousBalance = (clone $query)->where('lot_no', $lotNo)->sum('qty');
            if ($previousBalance == 0 && $request->filled('contractor_id')) {
                $previousBalance = (clone $baseQuery)->where('lot_no', $lotNo)->sum('qty');
            }
            return response()->json([
                'lot_no' => $lotNo,
                'previous' => (float) $previousBalance,
                'total_cartons_in_lot' => $totalCartonsInLot
            ]);
        }

        // 2. If lot_no is not provided, auto-determine latest lot or increment to next lot
        $existingLots = (clone $query)
            ->whereNotNull('lot_no')
            ->where('lot_no', '!=', '')
            ->pluck('lot_no')
            ->unique()
            ->values();

        if ($existingLots->isEmpty() && $request->filled('contractor_id')) {
            $existingLots = (clone $baseQuery)
                ->whereNotNull('lot_no')
                ->where('lot_no', '!=', '')
                ->pluck('lot_no')
                ->unique()
                ->values();
        }

        $maxLot = null;
        if ($existingLots->isNotEmpty()) {
            $numericLots = $existingLots->filter(function ($val) {
                return is_numeric(trim((string) $val));
            })->map(function ($val) {
                return (int) trim((string) $val);
            });

            if ($numericLots->isNotEmpty()) {
                $maxLot = (string) $numericLots->max();
            } else {
                $maxLot = (string) $existingLots->last();
            }
        } else {
            // Check ConsumptionMaster for initial lot_no
            $consLotQuery = ConsumptionMaster::where('cid', $cid)
                ->where('item_id', $productId)
                ->whereNotNull('lot_no')
                ->where('lot_no', '!=', '');

            if ($request->filled('contractor_id')) {
                $consWithContractor = (clone $consLotQuery)->where('contractor_account_id', $contractorId)->latest('id')->first();
                if ($consWithContractor) {
                    $maxLot = (string) $consWithContractor->lot_no;
                }
            }

            if (!$maxLot) {
                $latestCons = (clone $consLotQuery)->latest('id')->first();
                if ($latestCons) {
                    $maxLot = (string) $latestCons->lot_no;
                }
            }
        }

        if ($maxLot === null || $maxLot === '') {
            return response()->json([
                'lot_no' => '',
                'previous' => 0,
                'cartons_in_lot' => 0,
                'total_cartons_in_lot' => $totalCartonsInLot
            ]);
        }

        // Find cartons_in_lot from ConsumptionMaster
        $consumptionQuery = ConsumptionMaster::where('cid', $cid)
            ->where('item_id', $productId)
            ->whereNotNull('cartons_in_lot')
            ->where('cartons_in_lot', '>', 0);

        $matchedConsumption = null;
        if ($request->filled('contractor_id')) {
            $matchedConsumption = (clone $consumptionQuery)
                ->where('contractor_account_id', $contractorId)
                ->where('lot_no', $maxLot)
                ->first();

            if (!$matchedConsumption) {
                $matchedConsumption = (clone $consumptionQuery)
                    ->where('contractor_account_id', $contractorId)
                    ->latest('id')
                    ->first();
            }
        }

        if (!$matchedConsumption) {
            $matchedConsumption = (clone $consumptionQuery)
                ->where('lot_no', $maxLot)
                ->first();
        }

        if (!$matchedConsumption) {
            $matchedConsumption = (clone $consumptionQuery)
                ->latest('id')
                ->first();
        }

        $cartonsInLot = $matchedConsumption ? (float) $matchedConsumption->cartons_in_lot : 0;

        // Total qty entered so far for maxLot
        $usedQty = (float) (clone $query)->where('lot_no', $maxLot)->sum('qty');
        if ($usedQty == 0 && $request->filled('contractor_id')) {
            $usedQty = (float) (clone $baseQuery)->where('lot_no', $maxLot)->sum('qty');
        }

        // Check if lot is complete
        if ($cartonsInLot > 0 && $usedQty >= $cartonsInLot) {
            // Lot is complete! Increment to next lot
            $nextLot = is_numeric($maxLot) ? (string) ((int) $maxLot + 1) : $maxLot;
            $lotNo = $nextLot;
            $previousBalance = 0;
        } else {
            // Lot still open
            $lotNo = $maxLot;
            $previousBalance = $usedQty;
        }

        return response()->json([
            'lot_no' => $lotNo,
            'previous' => $previousBalance,
            'cartons_in_lot' => $cartonsInLot,
            'total_cartons_in_lot' => $totalCartonsInLot
        ]);
    }

    // Map each product to the contractor of its latest Consumption record's
    // detail row that is a "Packing" item (Raw Material/Item column in the
    // Consumption Edit table). Falls back to the record's own contractor.
    private function contractorByPackingMold($cid)
    {
        $masters = ConsumptionMaster::where('cid', $cid)
            ->whereNotNull('item_id')
            ->orderByDesc('id')
            ->with('details')
            ->get();

        $detailItemIds = $masters->flatMap->details->pluck('item_id')->unique()->filter();
        $packingItemIds = ItemMaster::whereIn('id', $detailItemIds)
            ->where(function ($q) {
                $q->whereHas('itemtypes', function ($sub) {
                    $sub->where('type_title', 'like', '%packing%');
                })->orWhere('item_code', 'like', '%pack%');
            })
            ->pluck('id');

        $packingMoldIds = Mold::where(function ($q) use ($cid) {
            $q->where('cid', $cid)->orWhereNull('cid');
        })
        ->where(function ($q) {
            $q->where('mold_name', 'like', '%packing%')
              ->orWhere('mold_id', 'like', '%packing%');
        })
        ->pluck('id');

        $contractorByProduct = [];

        foreach ($masters as $master) {
            $productId = $master->item_id;
            if (array_key_exists($productId, $contractorByProduct)) {
                continue;
            }

            $packingDetail = $master->details->first(function ($detail) use ($packingItemIds, $packingMoldIds) {
                if (!$detail->contractor_account_id) {
                    return false;
                }

                return $packingItemIds->contains($detail->item_id)
                    || $packingMoldIds->contains($detail->mold_id);
            });

            $contractorByProduct[$productId] = $packingDetail
                ? $packingDetail->contractor_account_id
                : $master->contractor_account_id;
        }

        return $contractorByProduct;
    }
}
