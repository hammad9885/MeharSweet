<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TRNDTL;
use App\Models\AccountMaster;
use App\Models\ProductMaster;
use App\Models\ItemMaster;
use Carbon\Carbon;

class ReportsController extends Controller
{
 
 
 public function reports(Request $request)
{
    $items = ItemMaster::all();
    $products = ProductMaster::all();
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $productType = $request->input('product_type');
   
    $accounts = TRNDTL::select('account_id')
        ->distinct()
        ->with('accounts')
        ->where('account_id', '!=', 35)
        ->whereIn('v_type', ['BPN', 'PRN', 'PPN', 'GPN', 'IPN', 'LPN', 'CPN', 'SPN', 'DPN'])
        ->get();

    $today = Carbon::today();
    $firstDayOfMonth = Carbon::now()->startOfMonth();

    // Initialize base query conditions
    $baseConditions = function($query) use ($request) {
        $query->where('account_id', '!=', 35);
        
        if ($request->has('account') && $request->account != '') {
            $query->where('account_id', $request->account);
        }
        
        if ($request->has('party') && $request->party != '') {
            $query->where('account_id', $request->party);
        }
    };

    // Initialize queries with base conditions
    $query = TRNDTL::where('v_type', 'BPN')->where($baseConditions)->with(['purchasedetails', 'accounts']);
    $query1 = TRNDTL::where('v_type', 'PRN')->where($baseConditions)->with('purchasereturns', 'accounts');
    $query2 = TRNDTL::where('v_type', 'PPN')->where($baseConditions)->with('purchaseplates', 'accounts');
    $query3 = TRNDTL::where('v_type', 'GPN')->where($baseConditions)->with('gluepurchases', 'accounts');
    $query4 = TRNDTL::where('v_type', 'IPN')->where($baseConditions)->with('inkpurchases', 'accounts');
    $query5 = TRNDTL::where('v_type', 'LPN')->where($baseConditions)->with('leminationpurchases', 'accounts');
    $query6 = TRNDTL::where('v_type', 'CPN')->where($baseConditions)->with('corrugationpurchases', 'accounts');
    $query7 = TRNDTL::where('v_type', 'SPN')->where($baseConditions)->with('shipperpurchases', 'accounts');
    $query8 = TRNDTL::where('v_type', 'DPN')->where($baseConditions)->with('dyepurchases', 'accounts');

    // Apply date range filter to all queries
    if ($startDate && $endDate) {
        $dateRange = [$startDate, $endDate];
    } else {
        $dateRange = [$firstDayOfMonth, $today];
    }

    $query->whereBetween('date', $dateRange);
    $query1->whereBetween('date', $dateRange);
    $query2->whereBetween('date', $dateRange);
    $query3->whereBetween('date', $dateRange);
    $query4->whereBetween('date', $dateRange);
    $query5->whereBetween('date', $dateRange);
    $query6->whereBetween('date', $dateRange);
    $query7->whereBetween('date', $dateRange);
    $query8->whereBetween('date', $dateRange);

    // Only filter by product type if selected, but don't reinitialize queries
    if ($productType) {
        // Apply product type filter to the appropriate query
        switch ($productType) {
            case 'PIN':
                $query = $query->where('v_type', 'BPN');
                break;
            case 'PRN':
                $query1 = $query1->where('v_type', 'PRN');
                break;
            case 'PPN':
                $query2 = $query2->where('v_type', 'PPN');
                break;
            case 'GPN':
                $query3 = $query3->where('v_type', 'GPN');
                break;
            case 'IPN':
                $query4 = $query4->where('v_type', 'IPN');
                break;
            case 'LPN':
                $query5 = $query5->where('v_type', 'LPN');
                break;
            case 'CPN':
                $query6 = $query6->where('v_type', 'CPN');
                break;
            case 'SPN':
                $query7 = $query7->where('v_type', 'SPN');
                break;
            case 'DPN':
                $query8 = $query8->where('v_type', 'DPN');
                break;
        }
    }

    // Get the results with ordering
    $orderBy = ['date' => 'desc', 'id' => 'desc', 'v_no' => 'desc'];
    
    // Get the results with ordering
$trndtl = $query->orderBy('date', 'desc')
                ->orderBy('id', 'desc')
                ->orderBy('v_no', 'desc')
                ->get();
                
$trndtl1 = $query1->orderBy('date', 'desc')
                  ->orderBy('id', 'desc')
                  ->orderBy('v_no', 'desc')
                  ->get();

$trndtl2 = $query2->orderBy('date', 'desc')
                  ->orderBy('id', 'desc')
                  ->orderBy('v_no', 'desc')
                  ->get();

$trndtl3 = $query3->orderBy('date', 'desc')
                  ->orderBy('id', 'desc')
                  ->orderBy('v_no', 'desc')
                  ->get();

$trndtl4 = $query4->orderBy('date', 'desc')
                  ->orderBy('id', 'desc')
                  ->orderBy('v_no', 'desc')
                  ->get();

$trndtl5 = $query5->orderBy('date', 'desc')
                  ->orderBy('id', 'desc')
                  ->orderBy('v_no', 'desc')
                  ->get();

$trndtl6 = $query6->orderBy('date', 'desc')
                  ->orderBy('id', 'desc')
                  ->orderBy('v_no', 'desc')
                  ->get();

$trndtl7 = $query7->orderBy('date', 'desc')
                  ->orderBy('id', 'desc')
                  ->orderBy('v_no', 'desc')
                  ->get();

$trndtl8 = $query8->orderBy('date', 'desc')
                  ->orderBy('id', 'desc')
                  ->orderBy('v_no', 'desc')
                  ->get();

    $accountMasters = AccountMaster::all();

    return view('reports.index', [
        'trndtl8' => $trndtl8,
        'trndtl7' => $trndtl7,
        'trndtl6' => $trndtl6,
        'trndtl5' => $trndtl5,
        'trndtl4' => $trndtl4,
        'trndtl3' => $trndtl3,
        'trndtl2' => $trndtl2,
        'trndtl1' => $trndtl1,
        'trndtl' => $trndtl,
        'startDate' => $startDate ?? $firstDayOfMonth->format('Y-m-d'),
        'endDate' => $endDate ?? $today->format('Y-m-d'),
        'products' => $products,
        'accountMasters' => $accountMasters,
        'items' => $items,
        'productType' => $productType, 
        'accounts' => $accounts,
    ]);
}


}
