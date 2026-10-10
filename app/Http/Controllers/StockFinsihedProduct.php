<?php

namespace App\Http\Controllers;

use App\Models\ItemMaster;
use App\Models\StockAdjDetail;
use App\Models\StockView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockFinsihedProduct extends Controller
{
    //
     public function index(Request $request)
    {
//          $user = auth()->user();
    
//         // Get the item_id from the request
//         $itemId = $request->input('item_id');
        
//         // Retrieve all items for the authenticated user's company
//         $allItems = ItemMaster::where('item_type','fp')->where('cid', Auth::user()->cid)->get();

//         // Initialize the query for items with related purchases, sales, and adjustments
//         $itemsQuery = ItemMaster::with(['sales', 'adjustments','salereturnreturn','production'])->where('cid',Auth::User()->cid)->where('item_type','fp');
//         // dd($itemsQuery->get());
            
        
//         // Filter by item_id if provided
//         if ($itemId) {
//             $itemsQuery->where('id', $itemId);
//         }
        
//         // Get the items
//         $items = $itemsQuery->get();
//         // dd($items);
//         // Calculate current stock for each item
//        foreach ($items as $item) {
//     $saleQty = $item->sales->sum('qty');
//     $salereturnQty = $item->salereturnreturn->sum('qty');
//     $productionQty = $item->production->sum('qty');
//     $totalAdjustmentQty = StockAdjDetail::where('item_id', $item->id)->sum('qty');

//     // Calculate current stock
//    $item->current_stock = (int)$item->op_qty + $saleQty +$productionQty - $salereturnQty + $totalAdjustmentQty;//50+10-0+150=60-0+150=
/// }

        // Return the view with items and all items
        $allItems = ItemMaster::where('item_type','fp')->where('cid', Auth::user()->cid)->pluck('id');
         $query = StockView::where('cid', auth()->user()->cid)->with(['item'=>function($q){
        $q->where('cid',auth()->user()->cid);
    }])->whereIn('item_id',$allItems);

    if ($request->has('item_id') && $request->item_id != '') {
        $query->where('item_id', $request->item_id);
    }
 if ($request->has('start_date') && $request->start_date != '') {
        $query->where('v_date', '>=', $request->start_date);  // Assuming 'created_at' is the date column in your StockView table
    }
    
    if ($request->has('end_date') && $request->end_date != '') {
        $query->where('v_date', '<=', $request->end_date);  // Assuming 'created_at' is the date column in your StockView table
    }

    // Get the stock data
    $report = $query->get();
// dd($report);
    // Initialize the item summaries
    $items = [];

    // Loop through each report entry and aggregate quantities and amounts by item
    foreach ($report as $entry) {
        // If the item is not in the items array, initialize it
        if (!isset($items[$entry->item_id])) {
            $items[$entry->item_id] = [
                'item_code' => $entry->item->item_code??'N/A',  // Assuming 'item_code' is the identifier for your item
                'opening_qty' => 0,
                'sale_qty' => 0,
                'sale_return_qty' => 0,
                'production_qty' => 0,
                'adjustment_qty' => 0,
                'current_stock' => 0
            ];
        }

        // Aggregate the data based on the source
        switch ($entry->source) {
            case 'opening':
                $items[$entry->item_id]['opening_qty'] += $entry->qty;
                break;
            case 'sale':
                $items[$entry->item_id]['sale_qty'] += $entry->qty;
                break;
            case 'sale return':
                $items[$entry->item_id]['sale_return_qty'] += $entry->qty;
                break;
            case 'production':
                $items[$entry->item_id]['production_qty'] += $entry->qty;
                break;
            case 'adjustment':
                $items[$entry->item_id]['adjustment_qty'] += $entry->qty;
                break;
        }

        // Calculate the current stock after considering all sources
        $items[$entry->item_id]['current_stock'] = $items[$entry->item_id]['opening_qty'] + $items[$entry->item_id]['sale_qty'] + $items[$entry->item_id]['sale_return_qty'] + $items[$entry->item_id]['production_qty'] + $items[$entry->item_id]['adjustment_qty'];
    }

    // Get all available items for the filter
    $allItems = ItemMaster::where('item_type','fp')->where('cid', Auth::user()->cid)->get();

                // dd($items);  
        return view('StockFinsihedProduct.index', compact('items', 'allItems'));
    }
}
