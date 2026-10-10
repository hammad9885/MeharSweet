<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TRNDTL;
use App\Models\AccountMaster;
use App\Models\SaleInvoice;
use App\Models\ProductMaster;
use App\Models\ItemMaster;
use App\Models\ConfectBilling;
use App\Models\ItemType;
use Illuminate\Support\Facades\DB; 

class SReportsController extends Controller
{


public function reports(Request $request)
{
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $productType = $request->input('product_type');

    // Initialize query builders with sorting
    $confectQuery = DB::table('confect_sale')
        ->orderBy('account_title', 'asc')
        ->orderBy('date', 'asc');
    
    $pharmaQuery = DB::table('pharma_sale')
        ->orderBy('account_title', 'asc')
        ->orderBy('date', 'asc');
        
    $generalQuery = DB::table('general_sale')
        ->orderBy('account_title', 'asc')
        ->orderBy('date', 'asc');

    // Apply date filters if provided
    if ($startDate) {
        $confectQuery->where('date', '>=', $startDate);
        $pharmaQuery->where('date', '>=', $startDate);
        $generalQuery->where('date', '>=', $startDate);
    }
    
    if ($endDate) {
        $confectQuery->where('date', '<=', $endDate);
        $pharmaQuery->where('date', '<=', $endDate);
        $generalQuery->where('date', '<=', $endDate);
    }

    // Get results based on product type
    if ($productType === 'CBill') {
        $confect_sales = $confectQuery->get();
        $pharma_sales = collect();
        $general_sales = collect();
        
        
    } elseif ($productType === 'PBill') {
        $pharma_sales = $pharmaQuery->get();
        $confect_sales = collect();
        $general_sales = collect();
        
    } elseif ($productType === 'GBill') {
        $general_sales = $generalQuery->get();
        $confect_sales = collect();
        $pharma_sales = collect();
        
    }  else {
        $confect_sales = $confectQuery->get();
        $pharma_sales = $pharmaQuery->get();
        $general_sales = $generalQuery->get();
    }

    return view('reports.sale', compact('confect_sales', 'pharma_sales', 'general_sales' ,'productType', 'startDate', 'endDate'));
}


}
