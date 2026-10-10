<?php

namespace App\Http\Controllers;

use App\Models\ItemMaster;
use App\Models\Module;
use App\Models\StockAdjDetail;
use App\Models\StockView;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockReportControllers extends Controller
{
    public function index(Request $request)
{
    $items=ItemMaster::where('item_type','rp')->where('cid',auth()->user()->cid)->pluck('id');
    // Get all items, or filter by item_id if selected
    $query = StockView::where('cid', auth()->user()->cid)->with(['item'=>function($q){
        $q->where('cid',auth()->user()->cid);
    }])->whereIn('item_id',$items);

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

    // Initialize the item summaries
    $items = [];

    // Loop through each report entry and aggregate quantities and amounts by item
    foreach ($report as $entry) {
        // If the item is not in the items array, initialize it
        if (!isset($items[$entry->item_id])) {
            $items[$entry->item_id] = [
                'item_code' => $entry->item->item_code??'N/A',  // Assuming 'item_code' is the identifier for your item
                'opening_qty' => 0,
                'purchase_qty' => 0,
                'purchase_return_qty' => 0,
                'consumed_qty' => 0,
                'adjustment_qty' => 0,
                'current_stock' => 0
            ];
        }

        // Aggregate the data based on the source
        switch ($entry->source) {
            case 'opening':
                $items[$entry->item_id]['opening_qty'] += $entry->qty;
                break;
            case 'purchase':
                $items[$entry->item_id]['purchase_qty'] += $entry->qty;
                break;
            case 'purchase return':
                $items[$entry->item_id]['purchase_return_qty'] += $entry->qty;
                break;
            case 'consumed':
                $items[$entry->item_id]['consumed_qty'] += $entry->qty;
                break;
            case 'adjustment':
                $items[$entry->item_id]['adjustment_qty'] += $entry->qty;
                break;
        }

        // Calculate the current stock after considering all sources
        $items[$entry->item_id]['current_stock'] = $items[$entry->item_id]['opening_qty'] + $items[$entry->item_id]['purchase_qty'] + $items[$entry->item_id]['purchase_return_qty'] + $items[$entry->item_id]['consumed_qty'] + $items[$entry->item_id]['adjustment_qty'];
    }

    // Get all available items for the filter
    $allItems = ItemMaster::where('cid',auth()->user()->cid)->get();

                // dd($items);
        // Return the view with items and all items
        return view('StockReport.index', compact('items', 'allItems'));
    }
}