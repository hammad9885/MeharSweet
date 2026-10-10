<?php

namespace App\Http\Controllers;

use App\Models\PurchaseInvoice;
use App\Models\PurchaseReturnInvoice;
use App\Models\SaleInvoices;
use App\Models\SaleReturnInvoice;
use App\Models\StockView;
use App\Models\Customers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemSelectedController extends Controller
{
    //
    public function sale($id, $customerId)
    {
        $purchaseItems = [];
        $query = [];
        if ($customerId != 0) {
            $purchaseItems = SaleInvoices::where("item_id", $id)
                ->with(['customer' => function ($q) use ($customerId) {
                    $q->where('cid', auth()->user()->cid);
                }])
                ->where('cid', auth()->user()->cid)
                ->where('customer_id', $customerId)
                ->orderBy('v_no', 'desc')
                ->get();
        } else {
            $purchaseItems = SaleInvoices::where("item_id", $id)
                ->with(['customer' => function ($q) {
                    $q->where('cid', auth()->user()->cid);
                }])
                ->where('cid', auth()->user()->cid)
                ->orderBy('v_no', 'desc')
                ->get();
        }
        try {
            $query = StockView::where('cid', auth()->user()->cid)->where('item_id', $id);
            $results = $query->select(
                DB::raw('SUM(qty) AS balance_qty')
            )
                ->groupBy('item_id')
                ->get();
        } catch (\Exception $e) {
            $results = collect();
        }
        $excludeVno = request()->query('exclude_vno');
        $lastRate = SaleInvoices::where('item_id', $id)
            ->where('cid', auth()->user()->cid)
            ->when($excludeVno, function ($q) use ($excludeVno) {
                $q->where('v_no', '!=', $excludeVno);
            })
            ->when($customerId != 0, function ($q) use ($customerId) {
                $q->where('customer_id', $customerId);
            })
            ->orderByDesc('v_no')
            ->orderByDesc('id')
            ->value('rate');
        $consumptionQpc = \App\Models\ConsumptionMaster::where('item_id', $id)
            ->where('cid', auth()->user()->cid)
            ->whereNotNull('qty_per_carton')
            ->where('qty_per_carton', '>', 0)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->value('qty_per_carton');

        return response()->json([
            'sale' => $purchaseItems,
            'balanced' => $results,
            'lastRate' => $lastRate,
            'qty_per_carton' => $consumptionQpc ? (int) $consumptionQpc : null,
        ]);
    }


    public function checker()
    {
        // Get all stock entries for the current user's CID
        $report = StockView::where('cid', auth()->user()->cid)->get();

        // Initialize an array to store the summarized stock information for each item
        $itemSummaries = [];

        // Loop through each report entry and aggregate quantities and amounts by source and item
        foreach ($report as $entry) {
            // Initialize the item summary if it doesn't exist
            if (!isset($itemSummaries[$entry->item_id])) {
                $itemSummaries[$entry->item_id] = [
                    'sale' => ['quantity' => 0, 'amount' => 0],
                    'sale_return' => ['quantity' => 0, 'amount' => 0],
                    'purchase' => ['quantity' => 0, 'amount' => 0],
                    'purchase_return' => ['quantity' => 0, 'amount' => 0],
                    'opening' => ['quantity' => 0, 'amount' => 0],
                    'adjustment' => ['quantity' => 0, 'amount' => 0],
                    'consumed' => ['quantity' => 0, 'amount' => 0],
                    'production' => ['quantity' => 0, 'amount' => 0],
                ];
            }

            // Now aggregate the quantities and amounts for each source and item
            switch ($entry->source) {
                case 'sale':
                    $itemSummaries[$entry->item_id]['sale']['quantity'] += $entry->qty;
                    $itemSummaries[$entry->item_id]['sale']['amount'] += $entry->total_amount;
                    break;
                case 'sale return':
                    $itemSummaries[$entry->item_id]['sale_return']['quantity'] += $entry->qty;
                    $itemSummaries[$entry->item_id]['sale_return']['amount'] += $entry->total_amount;
                    break;
                case 'purchase':
                    $itemSummaries[$entry->item_id]['purchase']['quantity'] += $entry->qty;
                    $itemSummaries[$entry->item_id]['purchase']['amount'] += $entry->total_amount;
                    break;
                case 'purchase return':
                    $itemSummaries[$entry->item_id]['purchase_return']['quantity'] += $entry->qty;
                    $itemSummaries[$entry->item_id]['purchase_return']['amount'] += $entry->total_amount;
                    break;
                case 'opening':
                    $itemSummaries[$entry->item_id]['opening']['quantity'] += $entry->qty;
                    $itemSummaries[$entry->item_id]['opening']['amount'] += $entry->total_amount;
                    break;
                case 'adjustment':
                    $itemSummaries[$entry->item_id]['adjustment']['quantity'] += $entry->qty;
                    $itemSummaries[$entry->item_id]['adjustment']['amount'] += $entry->total_amount;
                    break;
                case 'consumed':
                    $itemSummaries[$entry->item_id]['consumed']['quantity'] += $entry->qty;
                    $itemSummaries[$entry->item_id]['consumed']['amount'] += $entry->total_amount;
                    break;
                case 'production':
                    $itemSummaries[$entry->item_id]['production']['quantity'] += $entry->qty;
                    $itemSummaries[$entry->item_id]['production']['amount'] += $entry->total_amount;
                    break;
            }
        }
        return view('StockReport.index', compact('items', 'allItems'));
    }


    public function saleReturn($id, $customerId)
    {
        $purchaseItems = [];
        $query = [];
        if ($customerId != 0) {
            // $cust=Customers::find($customerId);
            // dd($customerId);
            $purchaseItems = SaleReturnInvoice::where("item_id", $id)
                ->with(['customer' => function ($q) {
                    $q->where('cid', auth()->user()->cid);
                }])
                ->where('cid', auth()->user()->cid)
                ->where('customer_id', $customerId)
                ->orderBy('v_no', 'desc')
                ->get();
        }

        //    dd($purchaseItems);
        try {
            $query = StockView::where('cid', auth()->user()->cid)->where('item_id', $id);
            $results = $query->select(
                DB::raw('SUM(qty) AS balance_qty')
            )
                ->groupBy('item_id')
                ->get();
        } catch (\Exception $e) {
            $results = collect();
        }
        $lastRate = SaleReturnInvoice::where('item_id', $id)
            ->where('cid', auth()->user()->cid)
            ->when($customerId != 0, function ($q) use ($customerId) {
                $q->where('customer_id', $customerId);
            })
            ->latest('v_no')
            ->value('rate');
        // dd($purchaseItems);
        return response()->json([
            'sale' => $purchaseItems,
            'balanced' => $results,
            'lastRate' => $lastRate,
        ]);
    }

    public function purchase($id,$customerId)
    {
        $purchaseItems = PurchaseInvoice::where("item_id", $id)
            ->with(['supplier' => function ($q) {
                $q->where('cid', auth()->user()->cid);
            }])
            ->where('cid', auth()->user()->cid)
            ->orderBy('v_no', 'desc')
            ->get();
        try {
            $query = StockView::where('cid', auth()->user()->cid)->where('item_id', $id);
            $results = $query->select(
                DB::raw('SUM(qty) AS balance_qty')
            )
                ->groupBy('item_id')
                ->get();
        } catch (\Exception $e) {
            $results = collect();
        }
        $lastRate = PurchaseInvoice::where('item_id', $id)
            ->where('cid', auth()->user()->cid)
            ->when($customerId != 0, function ($q) use ($customerId) {
                $q->where('supplier_id', $customerId);
        })
        ->latest('v_no')
        ->value('rate');
        return response()->json([
            'sale' => $purchaseItems,
            'balanced' => $results,
            'lastRate'=>$lastRate
        ]);
    }



    public function purchaseReturn($id,$customerId)
    {
        $purchaseItems = PurchaseReturnInvoice::where("item_id", $id)
            ->with(['supplier' => function ($q) {
                $q->where('cid', auth()->user()->cid);
            }])
            ->where('cid', auth()->user()->cid)
            ->orderBy('v_no', 'desc')
            ->get();
        try {
            $query = StockView::where('cid', auth()->user()->cid)->where('item_id', $id);
            $results = $query->select(
                DB::raw('SUM(qty) AS balance_qty')
            )
                ->groupBy('item_id')
                ->get();
        } catch (\Exception $e) {
            $results = collect();
        }
         $lastRate = PurchaseReturnInvoice::where('item_id', $id)
            ->where('cid', auth()->user()->cid)
            ->when($customerId != 0, function ($q) use ($customerId) {
                $q->where('supplier_id', $customerId);
        })
        ->latest('v_no')
        ->value('rate');
        return response()->json([
            'sale' => $purchaseItems,
            'balanced' => $results,
            'lastRate'=>$lastRate
        ]);
    }
}
