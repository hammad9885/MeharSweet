<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductLog;

class ProductLogController extends Controller
{
    
    public function index()
    {
        return view('product_log.index');
    }
    
   public function report(Request $request)
{
    // Initialize query for the product_log table
    $query = ProductLog::query();

    // Apply date range filter only if dates are provided
    if ($request->filled('start_date') && $request->filled('end_date')) {
        $query->whereBetween('updated_at', [
            $request->input('start_date') . ' 00:00:00',
            $request->input('end_date') . ' 23:59:59',
        ]);
    }

    // Apply product name filter
    if ($request->filled('prod_name')) {
        $query->where('prod_name', 'LIKE', '%' . $request->input('prod_name') . '%');
    }

    // Apply action filter
    if ($request->filled('action')) {
        $query->where('action', 'LIKE', '%' . $request->input('action') . '%');
    }

    // Sort by latest update
    $productLogs = $query->orderBy('updated_at', 'desc')->get();

    // Fetch distinct product names for the dropdown
    $productNames = ProductLog::distinct()->pluck('prod_name')->toArray();
    $actions = ProductLog::distinct()->pluck('action')->toArray();

    // Return view with data
    return view('product_log.index', compact('productLogs', 'productNames', 'actions'));
}


}
