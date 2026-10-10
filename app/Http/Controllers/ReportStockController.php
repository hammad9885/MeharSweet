<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TRNDTL;
use App\Models\AccountMaster;
use App\Models\ProductMaster;
use App\Models\ItemMaster;
use Illuminate\Support\Facades\DB;


class ReportStockController extends Controller
{
    public function reports(Request $request)
{
    $items = ItemMaster::all();
    $products = ProductMaster::all();
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $productType = $request->input('product_type');

   $boxboardData = DB::table('boxboard_view')
    ->select('item_code', 'width', 'length', 'grammage', 'remain_qty', 'total_wt')
    ->orderBy('item_code', 'asc') 
    ->get();
    
    
    $inkData = DB::table('ink_view')
    ->select('item', 'remain_qty')
    ->orderBy('item', 'asc') // 'asc' for A-Z, 'desc' for Z-A
    ->get();
    
    $glueData = DB::table('glue_view')
    ->select('item', 'remain_qty')
    ->orderBy('item', 'asc') // 'asc' for A-Z, 'desc' for Z-A
    ->get();
   
    $shipperData = DB::table('shipper_view')
    ->select('item', 'remain_qty')
    ->orderBy('item', 'asc') // 'asc' for A-Z, 'desc' for Z-A
    ->get();
    
    $laminationData = DB::table('lamination_view')
    ->select('total_qty', 'remain_qty', 'item_id', 'size', 'item_name')
    ->get();
    
    
    
    $corrugationData = DB::table('corrugation_view')
    ->select('remain_qty' , 'size', 'item_name')
    ->get();
    
    $dyeData = DB::table('dye_view')
    ->select('item_name', 'remain_qty')
    ->orderBy('item_name', 'asc') // 'asc' for A-Z, 'desc' for Z-A
    ->get();
    
     $plateData = DB::table('plate_view')
    ->select('product_name', 'item_code', 'country_name', 'remain_qty')
    ->get();
    
    $accounts = TRNDTL::select('account_id')
    ->distinct()
    ->with('accounts')
    ->whereIn('v_type', ['BPN', 'PRN', 'PPN', 'GPN', 'IPN', 'LPN', 'CPN', 'SPN'])
    ->get();


    // Initialize queries
    $query = TRNDTL::where('v_type', 'BPN')->where('description', '!=', 'Freight')->with(['purchasedetails', 'accounts']);
    $query1 = TRNDTL::where('v_type', 'PRN')->where('description', '!=', 'Freight')->with('purchasedetails' , 'accounts');
    $query2 = TRNDTL::where('v_type', 'PPN')->where('description', '!=', 'Freight')->with('purchaseplates', 'accounts');
    $query3 = TRNDTL::where('v_type', 'GPN')->where('description', '!=', 'Freight')->with('gluepurchases', 'accounts');
    $query4 = TRNDTL::where('v_type', 'IPN')->where('description', '!=', 'Freight')->with('inkpurchases', 'accounts');
    $query5 = TRNDTL::where('v_type', 'LPN')->where('description', '!=', 'Freight')->with('leminationpurchases', 'accounts');
    $query6 = TRNDTL::where('v_type', 'CPN')->where('description', '!=', 'Freight')->with('corrugationpurchases', 'accounts');
    $query7 = TRNDTL::where('v_type', 'SPN')->where('description', '!=', 'Freight')->with('shipperpurchases', 'accounts');
    
    
    if ($request->has('account') && $request->account != '') {
        $query->where('account_id', $request->account);
        $query1->where('account_id', $request->account);
        $query2->where('account_id', $request->account);
        $query3->where('account_id', $request->account);
        $query4->where('account_id', $request->account);
        $query5->where('account_id', $request->account);
        $query6->where('account_id', $request->account);
        $query7->where('account_id', $request->account);
    }
    
   if ($request->has('party') && $request->party != '') {
    $query->where('account_id', $request->party);
    $query1->where('account_id', $request->party);
    $query2->where('account_id', $request->party);
    $query3->where('account_id', $request->party);
    $query4->where('account_id', $request->party);
    $query5->where('account_id', $request->party);
    $query6->where('account_id', $request->party);
    $query7->where('account_id', $request->party);
}

    // Apply date range filter
    if ($startDate && $endDate) {
        $query->whereBetween('date', [$startDate, $endDate]);
        $query1->whereBetween('date', [$startDate, $endDate]);
        $query2->whereBetween('date', [$startDate, $endDate]);
        $query3->whereBetween('date', [$startDate, $endDate]);
        $query4->whereBetween('date', [$startDate, $endDate]);
        $query5->whereBetween('date', [$startDate, $endDate]);
        $query6->whereBetween('date', [$startDate, $endDate]);
        $query7->whereBetween('date', [$startDate, $endDate]);
    }

    // Filter by product type if selected
    if ($productType) {
    $query = TRNDTL::where('v_type', 'BPN')->where('description', '!=', 'Freight')->with(['purchasedetails', 'accounts']);
    $query1 = TRNDTL::where('v_type', 'PRN')->where('description', '!=', 'Freight')->with('purchasedetails');
    $query2 = TRNDTL::where('v_type', 'PPN')->where('description', '!=', 'Freight')->with('purchaseplates');
    $query3 = TRNDTL::where('v_type', 'GPN')->where('description', '!=', 'Freight')->with('gluepurchases');
    $query4 = TRNDTL::where('v_type', 'IPN')->where('description', '!=', 'Freight')->with('inkpurchases');
    $query5 = TRNDTL::where('v_type', 'LPN')->where('description', '!=', 'Freight')->with('leminationpurchases');
    $query6 = TRNDTL::where('v_type', 'CPN')->where('description', '!=', 'Freight')->with('corrugationpurchases');
    $query7 = TRNDTL::where('v_type', 'SPN')->where('description', '!=', 'Freight')->with('shipperpurchases');
}




    // Group and order the data
    $trndtl = $query
                ->orderBy('date', 'desc')
                ->orderBy('id', 'desc')
                ->orderBy('v_no', 'desc')
                ->get();
                
    $trndtl1 = $query1
                ->orderBy('date', 'desc')
                ->orderBy('id', 'desc')
                ->orderBy('v_no', 'desc')
                ->get();
    
    $trndtl2 = $query2
                ->orderBy('date', 'desc')
                ->orderBy('id', 'desc')
                ->orderBy('v_no', 'desc')
                ->get();
    
    $trndtl3 = $query3
                ->orderBy('date', 'desc')
                ->orderBy('id', 'desc')
                ->orderBy('v_no', 'desc')
                ->get();
                
    $trndtl4 = $query4
                ->orderBy('date', 'desc')
                ->orderBy('id', 'desc')
                ->orderBy('v_no', 'desc')
                ->get();
                
    $trndtl5 = $query5
                ->orderBy('date', 'desc')
                ->orderBy('id', 'desc')
                ->orderBy('v_no', 'desc')
                ->get();
                
    $trndtl6 = $query6
                ->orderBy('date', 'desc')
                ->orderBy('id', 'desc')
                ->orderBy('v_no', 'desc')
                ->get();
                
    $trndtl7 = $query7
                ->orderBy('date', 'desc')
                ->orderBy('id', 'desc')
                ->orderBy('v_no', 'desc')
                ->get();

    $accountMasters = AccountMaster::all();

    return view('report_stock.index', [
        'trndtl7' => $trndtl7,
        'trndtl6' => $trndtl6,
        'trndtl5' => $trndtl5,
        'trndtl4' => $trndtl4,
        'trndtl3' => $trndtl3,
        'trndtl2' => $trndtl2,
        'trndtl1' => $trndtl1,
        'trndtl' => $trndtl,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'products' => $products,
        'accountMasters' => $accountMasters,
        'items' => $items,
        'productType' => $productType, 
        'accounts' => $accounts,
        'boxboardData' => $boxboardData,
        'inkData' => $inkData,
        'glueData' => $glueData,
        'shipperData' => $shipperData,
        'laminationData' =>  $laminationData,
        'corrugationData' =>  $corrugationData,
        'plateData' =>  $plateData,
        'dyeData' =>  $dyeData,
        // 'itemshippers' => $itemshippers,
        // 'itemCorrugations' => $itemCorrugations,
        // 'itemLaminations' => $itemLaminations,
    ]);
}



}
