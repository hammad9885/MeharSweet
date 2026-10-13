<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockView;
use App\Models\StockQtyView;
use App\Models\Item;
use App\Models\ItemMaster;

class ProductionStockReport extends Controller
{
    public function index(Request $request)
    {
        // Using the Model with scopes
        $query = StockView::where('cid', auth()->user()->cid)->with(['item'=>function($q){
        $q->where('cid',auth()->user()->cid);
    }]);

    if ($request->has('item_id') && $request->item_id != '') {
        $query->where('item_id', $request->item_id);
    }

    // Get the stock data
   $stockData = $query->get();
// dd($stockData);
$items = [];

foreach ($stockData as $entry) {

    if (!isset($items[$entry->item_id])) {
        $items[$entry->item_id] = [
            'item_code' => $entry->item->item_code ?? 'N/A',
            'opening_qty' => 0,
            'opening_amount' => 0,
            'purchase_qty' => 0,
            'purchase_amount' => 0,
            'purchase_return_qty' => 0,
            'purchase_return_amount' => 0,
            'sale_return_qty' => 0,
            'sale_return_amount' => 0,
            'adjustment_qty' => 0,
            'adjustment_amount' => 0,
            'sale_qty' => 0,
            'sale_amount' => 0,
            'prod_qty' => 0,
            'prod_amount' => 0,
            'consumed_qty' => 0,
            'consumed_amount' => 0,
            'available_qty' => 0,
            'available_amt' => 0,
            'closing_qty' => 0,
            'closing_amt' => 0,
        ];
    }

    switch ($entry->source) {
        case 'opening':
            $items[$entry->item_id]['opening_qty'] += $entry->qty;
            $items[$entry->item_id]['opening_amount'] += $entry->total_amount;
            break;
        case 'purchase':
            $items[$entry->item_id]['purchase_qty'] += $entry->qty;
            $items[$entry->item_id]['purchase_amount'] += $entry->total_amount;
            break;
        case 'purchase return':
            $items[$entry->item_id]['purchase_return_qty'] += $entry->qty;
            $items[$entry->item_id]['purchase_return_amount'] += $entry->total_amount;
            break;
        case 'sale return':
            $items[$entry->item_id]['sale_return_qty'] += $entry->qty;
            $items[$entry->item_id]['sale_return_amount'] += $entry->total_amount;
            break;
        case 'adjustment':
            $items[$entry->item_id]['adjustment_qty'] += $entry->qty;
            $items[$entry->item_id]['adjustment_amount'] += $entry->total_amount;
            break;
        case 'sale':
            $items[$entry->item_id]['sale_qty'] += $entry->qty;
            $items[$entry->item_id]['sale_amount'] += $entry->total_amount;
            break;
        case 'production':
            $items[$entry->item_id]['prod_qty'] += $entry->qty;
            $items[$entry->item_id]['prod_amount'] += $entry->total_amount;
            break;
        case 'consumed':
            $items[$entry->item_id]['consumed_qty'] += $entry->qty;
            $items[$entry->item_id]['consumed_amount'] += $entry->total_amount;
            break;
    }

    // Calculate totals
    $items[$entry->item_id]['available_qty'] = 
        $items[$entry->item_id]['opening_qty'] + 
        $items[$entry->item_id]['purchase_qty'] + 
        $items[$entry->item_id]['purchase_return_qty'] + 
        $items[$entry->item_id]['adjustment_qty'];

    $items[$entry->item_id]['available_amt'] = 
        $items[$entry->item_id]['opening_amount'] + 
        $items[$entry->item_id]['purchase_amount'] + 
        $items[$entry->item_id]['purchase_return_amount'] + 
        $items[$entry->item_id]['adjustment_amount'];

    $items[$entry->item_id]['closing_qty'] = 
        $items[$entry->item_id]['available_qty'] + 
        $items[$entry->item_id]['sale_qty'] + 
        $items[$entry->item_id]['sale_return_qty'] + 
        $items[$entry->item_id]['prod_qty'] + 
        $items[$entry->item_id]['consumed_qty'];
        
        $items[$entry->item_id]['closing_amt'] = 
        $items[$entry->item_id]['available_amt'] +
        $items[$entry->item_id]['sale_amount'] + 
        $items[$entry->item_id]['sale_return_amount'] + 
        $items[$entry->item_id]['prod_amount'] + 
        $items[$entry->item_id]['consumed_amount'];
}
// dd($items);
        // Calculate the current stock after considering all sources
        //  $items[$entry->item_id]['available_stock'] = $items[$entry->item_id]['opening_qty'] + $items[$entry->item_id]['purchase_qty'] - $items[$entry->item_id]['purchase_return_qty'] + $items[$entry->item_id]['consumed_qty'] + $items[$entry->item_id]['adjustment_qty'];
        // $items[$entry->item_id]['current_stock'] = $items[$entry->item_id]['opening_qty'] + $items[$entry->item_id]['purchase_qty'] + $items[$entry->item_id]['purchase_return_qty'] + $items[$entry->item_id]['consumed_qty'] + $items[$entry->item_id]['adjustment_qty'];
    
        // Process data
        // $stocks = $stockData->map(function ($item) {
        //     return [
        //         'item_id' => $item->item_id,
        //         'item_name' => $item->item->name ?? 'N/A',
        //         'item_code' => $item->item->item_code ?? 'N/A',
        //         'cid' => $item->cid,
                
        //         // Opening Stock
        //         'opn_qty' => $item->total_opn_qty,
        //         'opn_amt' => $item->total_opn_amt,
                
        //         // Net Purchase
        //         'pur_qty' => $item->total_pur_qty - $item->total_pret_qty,
        //         'pur_amt' => $item->total_pur_amt - $item->total_pret_amt,
                
        //         // Net Sales
        //         'sal_qty' => $item->total_sal_qty - $item->total_sret_qty,
        //         'sal_amt' => $item->total_sal_amt - $item->total_sret_amt,
                
        //         // Available Stock
        //         'available_qty' => $item->total_opn_qty + ($item->total_pur_qty - $item->total_pret_qty),
        //         'available_amt' => $item->total_opn_amt + ($item->total_pur_amt - $item->total_pret_amt),
                
        //         // Material Consumed
        //         'con_qty' => $item->total_con_qty,
        //         'con_amt' => $item->total_con_amt,
                
        //         // Closing Stock
        //         'closing_qty' => $item->total_opn_qty + ($item->total_pur_qty - $item->total_pret_qty) - $item->total_con_qty,
        //         'closing_amt' => $item->total_opn_amt + ($item->total_pur_amt - $item->total_pret_amt) - $item->total_con_amt
        //     ];
        // });

        // Get items for dropdown
        $allitems = ItemMaster::select('id', 'item_code')->where('cid',auth()->user()->cid)->get();

        return \Inertia\Inertia::render('Inventory/Reports/ProductionStock', [
            'items'    => $items,
            'allitems' => $allitems,
            'filters'  => $request->only(['item_id', 'start_date', 'end_date']),
        ]);
    }

    /**
     * Get stock for specific item
     */
    public function getStockByItem($itemId)
    {
        $stock = StockQtyView::with('item')
            ->whereItemId($itemId)
            ->summarized()
            ->first();

        if (!$stock) {
            return response()->json(['message' => 'Stock not found'], 404);
        }

        $processedStock = [
            'item_id' => $stock->item_id,
            'item_name' => $stock->item->name ?? 'N/A',
            'item_code' => $stock->item->code ?? 'N/A',
            'opn_qty' => $stock->total_opn_qty,
            'opn_amt' => $stock->total_opn_amt,
            'pur_qty' => $stock->total_pur_qty - $stock->total_pret_qty,
            'pur_amt' => $stock->total_pur_amt - $stock->total_pret_amt,
            'sal_qty' => $stock->total_sal_qty - $stock->total_sret_qty,
            'sal_amt' => $stock->total_sal_amt - $stock->total_sret_amt,
            'available_qty' => $stock->total_opn_qty + ($stock->total_pur_qty - $stock->total_pret_qty),
            'available_amt' => $stock->total_opn_amt + ($stock->total_pur_amt - $stock->total_pret_amt),
            'con_qty' => $stock->total_con_qty,
            'con_amt' => $stock->total_con_amt,
            'closing_qty' => $stock->total_opn_qty + ($stock->total_pur_qty - $stock->total_pret_qty) - $stock->total_con_qty,
            'closing_amt' => $stock->total_opn_amt + ($stock->total_pur_amt - $stock->total_pret_amt) - $stock->total_con_amt
        ];

        return response()->json($processedStock);
    }

    /**
     * Get stock for multiple items
     */
    public function getStockByItems(Request $request)
    {
        $request->validate([
            'item_ids' => 'required|array',
            'item_ids.*' => 'exists:items,id'
        ]);

        $stocks = StockQtyView::with('item')
            ->whereItemIds($request->item_ids)
            ->summarized()
            ->get()
            ->map(function ($stock) {
                return [
                    'item_id' => $stock->item_id,
                    'item_name' => $stock->item->name ?? 'N/A',
                    'item_code' => $stock->item->code ?? 'N/A',
                    'opn_qty' => $stock->total_opn_qty,
                    'opn_amt' => $stock->total_opn_amt,
                    'pur_qty' => $stock->total_pur_qty - $stock->total_pret_qty,
                    'pur_amt' => $stock->total_pur_amt - $stock->total_pret_amt,
                    'sal_qty' => $stock->total_sal_qty - $stock->total_sret_qty,
                    'sal_amt' => $stock->total_sal_amt - $stock->total_sret_amt,
                    'available_qty' => $stock->total_opn_qty + ($stock->total_pur_qty - $stock->total_pret_qty),
                    'available_amt' => $stock->total_opn_amt + ($stock->total_pur_amt - $stock->total_pret_amt),
                    'con_qty' => $stock->total_con_qty,
                    'con_amt' => $stock->total_con_amt,
                    'closing_qty' => $stock->total_opn_qty + ($stock->total_pur_qty - $stock->total_pret_qty) - $stock->total_con_qty,
                    'closing_amt' => $stock->total_opn_amt + ($stock->total_pur_amt - $stock->total_pret_amt) - $stock->total_con_amt
                ];
            });

        return response()->json($stocks);
    }
}