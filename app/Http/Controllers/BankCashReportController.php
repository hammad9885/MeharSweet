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

class BankCashReportController extends Controller
{


public function reports(Request $request)
{
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $productType = $request->input('product_type');
    $partyName = $request->input('party_name'); // Get party name from request

    // Initialize query builders with sorting
    $cashQuery = DB::table('cash_report')
        ->orderBy('account_title', 'asc')
        ->orderBy('date', 'asc');
    
    $bankQuery = DB::table('bank_report')
        ->orderBy('account_title', 'asc')
        ->orderBy('date', 'asc');
        
    $jvQuery = DB::table('jv_report')
        ->orderBy('account_title', 'asc')
        ->orderBy('date', 'asc');
        
    $chqQuery = DB::table('chq_report')
        ->orderBy('account_title', 'asc')
        ->orderBy('date', 'asc');

    // Apply date filters if provided
    if ($startDate) {
        $cashQuery->where('date', '>=', $startDate);
        $bankQuery->where('date', '>=', $startDate);
        $jvQuery->where('date', '>=', $startDate);
        $chqQuery->where('date', '>=', $startDate);
    }
    
    if ($endDate) {
        $cashQuery->where('date', '<=', $endDate);
        $bankQuery->where('date', '<=', $endDate);
        $jvQuery->where('date', '<=', $endDate);
        $chqQuery->where('date', '<=', $endDate);
    }

    // Apply party name filter if provided
    if ($partyName) {
        $cashQuery->where('account_title', $partyName);
        $bankQuery->where('account_title', $partyName);
        $jvQuery->where('account_title', $partyName);
        $chqQuery->where('account_title', $partyName);
    }

    // Get all unique party names for dropdown
    $cashParties = DB::table('cash_report')->distinct()->pluck('account_title');
    $bankParties = DB::table('bank_report')->distinct()->pluck('account_title');
    $jvParties = DB::table('jv_report')->distinct()->pluck('account_title');
    $chqParties = DB::table('chq_report')->distinct()->pluck('account_title');
    $allParties = $cashParties->merge($bankParties)->merge($jvParties)->merge($chqParties)->unique()->sort();

    // Get results based on product type
    switch ($productType) {
        case 'Cash':
            $cash_sales = $cashQuery->get();
            $bank_sales = collect();
            $jv_sales = collect();
            $chq_sales = collect();
            break;
        case 'Bank':
            $bank_sales = $bankQuery->get();
            $cash_sales = collect();
            $jv_sales = collect();
            $chq_sales = collect();
            break;
        case 'JV':
            $jv_sales = $jvQuery->get();
            $cash_sales = collect();
            $bank_sales = collect();
            $chq_sales = collect();
            break;
        case 'CHQ':
            $chq_sales = $chqQuery->get();
            $cash_sales = collect();
            $bank_sales = collect();
            $jv_sales = collect();
            break;
        default:
            $cash_sales = $cashQuery->get();
            $bank_sales = $bankQuery->get();
            $jv_sales = $jvQuery->get();
            $chq_sales = $chqQuery->get();
    }

    return view('bank_cash.index', compact(
        'cash_sales', 
        'bank_sales',
        'jv_sales',
        'chq_sales',
        'productType', 
        'startDate', 
        'endDate',
        'allParties',
        'partyName'
    ));
}

}
