<?php

namespace App\Http\Controllers;

use App\Models\ItemMaster;
use App\Models\ItemType;
use App\Models\TRNDTL;
use App\Models\ErpParam;
use Illuminate\Http\Request;
use App\Models\AccountMaster;
use App\Models\ConfectioneryMaster;
use Illuminate\Support\Arr;
use App\Models\ConfectBilling;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ConfectBillingController extends Controller
{

    public function index()
    {
        $loggedInUser = Auth::user();
        $accounts = AccountMaster::all();
        $confect = ConfectioneryMaster::all();
        $items = ItemMaster::all();
        $saleAccounts = AccountMaster::all();
        return view('sales.confect_billing.list', compact('loggedInUser', 'accounts', 'items', 'saleAccounts', 'confect'));
    }

    public function getVnos($accountId)
    {
       // Fetch distinct voucher numbers for the given account ID (excluding NULLs)
$vnos = ConfectioneryMaster::where('account_id', $accountId)
    ->whereNotNull('v_no')  // Ensure v_no is not NULL
    ->distinct('v_no')      // Use distinct directly on v_no (MySQL/PostgreSQL)
    ->pluck('v_no')
    ->toArray();

if (empty($vnos)) {
    return response()->json(['status' => 'no_vouchers']);
}

// Fetch only the used voucher numbers (optimized query)
$existingVnos = ConfectBilling::whereIn('old_vno', $vnos)
    ->pluck('old_vno')
    ->toArray();

// Separate into available and used (faster than looping)
$usedVnos = array_values(array_intersect($vnos, $existingVnos));
$availableVnos = array_values(array_diff($vnos, $existingVnos));

return response()->json([
    'status' => 'success',
    'vnos' => $availableVnos,
    'used_vnos' => $usedVnos,
]);
    }


    public function getEntryDetails($vno)
    {
        $trndtl = ConfectioneryMaster::with(['confectioneryDetails.products', 'accounts', 'confectioneryDetails.itemType'])
            ->where('v_no', $vno)
            ->get();

        if ($trndtl->isNotEmpty()) {
            $entries = $trndtl->map(function ($data) {
                return [
                    'date' => \Carbon\Carbon::parse($data->date)->format('d-m-Y'),
                    'v_no' => $data->v_no,
                    'preparedby' => $data->preparedby,
                    'product_name' => $data->confectioneryDetails->products->prod_name ?? 'N/A',
                    'rate' => $data->confectioneryDetails->products->rate ?? 'N/A',
                    'product_id' => $data->confectioneryDetails->products->id ?? null,
                    'party' => $data->accounts->title ?? 'N/A',
                    'item_type' => $data->confectioneryDetails->itemType->type_title ?? 'N/A',
                    'item_id' => $data->confectioneryDetails->itemType->id ?? 'N/A',
                    'box' => $data->confectioneryDetails->box ?? 'N/A',
                    'pack_qty' => $data->confectioneryDetails->pack_qty ?? 'N/A',
                    'po_no' => $data->confectioneryDetails->po_no ?? 'N/A',
                    'total' => $data->confectioneryDetails->total ?? 'N/A',
                ];
            });

            return response()->json([
                'status' => 'success',
                'entries' => $entries
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'No entries found for this voucher.'
            ]);
        }
    }
  

public function store(Request $request)
{
    $request->validate([
        'date' => 'required|date',
        'prepared_by' => 'required|string',
        'account' => 'required|integer',
        'v_type' => 'required|string',
        'total_amount' => 'required|numeric',
        'rate' => 'required|array',
        'rate.*' => 'required|numeric|min:0.01',
        'total_rate' => 'required|array',
        'grand_total' => 'required|numeric',
    ], [
        'rate.*.required' => 'Rate cannot be empty.',
        'rate.*.numeric' => 'Rate must be a number.',
        'rate.*.min' => 'Rate must be greater than 0.',
    ]);
    
    $billNo = ConfectBilling::max('billing_no');
    $maxBillNo = $billNo ? ((int) $billNo + 1) : 1;

    $data = $request->all();
    $accountId = $data['account']; // Correct account_id reference

    $lastVno = ConfectBilling::where('account_id', $accountId)
    ->orderByRaw('CAST(v_no AS UNSIGNED) DESC')
    ->value('v_no');

    $vno = ($lastVno !== null) ? (int)$lastVno + 1 : 1;


    $erpParam = ErpParam::first();
    $saleAccountId = $erpParam ? $erpParam->sale_ac : null;

    $preBal = TRNDTL::where(function($query) use ($accountId) {
        $query->where('account_id', $accountId)
              ->orWhere('cash_id', $accountId);
    })
    ->get()
    ->reduce(function ($carry, $item) use ($accountId) {
        $accountSum = ($item->account_id == $accountId) ? ($item->debit - $item->credit) : 0;
        $cashSum = ($item->cash_id == $accountId) ? ($item->debit - $item->credit) : 0;
        return $carry + ($accountSum - $cashSum);
    }, 0);
        
        

    foreach ($data['product_id'] as $index => $productId) {
        // Always fetch the registered rate from ItemMaster
        // $registeredRate = ItemMaster::where('id', $productId)->value('sale_rate') ?? 0;
        $saleInvoice =  ConfectBilling::create([
            'billing_no' => $maxBillNo,
            'v_no' => $vno,
            'old_vno' => $data['old_vno'][$index],
            'product_name' => $productId ?? 'N/A',
            'item' => $data['item_id'][$index] ?? 'N/A',
            'box' => $data['box'][$index] ?? 'N/A',
            'packing' => $data['packing'][$index] ?? 'N/A',
            'po_no' => $data['po_no'][$index] ?? 'N/A',
            'total' => $data['total'][$index] ?? 0,
            'rate' => $data['rate'][$index] ?? 0,
            'total_rate' => $data['total_rate'][$index] ?? 0,
            'account_id' => $accountId,
            'created_at' => $data['date'] ?? Carbon::now() ,
            'updated_at' =>  $data['date'] ?? Carbon::now() ,
        ]);
    }

    TRNDTL::create([
        'v_no' => $vno,
        'date' => $data['date'] ?? Carbon::now() ,
        'description' => 'CBill',
        'account_id' => $accountId,
        'cash_id' => $saleAccountId,
        'preparedby' => auth()->user()->name,
        'credit' => 0,
        'debit' => $data['grand_total'] ?? 0,
        'status' => 'unofficial',
        'v_type' => 'CBill',
        'r_id' => $maxBillNo,
        'pre_bal' => $preBal,
         'created_at' => $data['date'] ?? Carbon::now() ,
            'updated_at' =>  $data['date'] ?? Carbon::now() ,
    ]);

    return redirect()->route('confect_billing.reports')->with('success', 'CBill  Created Successfully: ' . $vno);
}


 public function reports(Request $request)
{
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $status = $request->input('status');
    $v_no = $request->input('v_no');
    $item = $request->input('item');
    $account_id = $request->input('account_id');
    $showPrevBalance = $request->input('show_prev_balance', '1');
    // dd($showPrevBalance);
    // Query for TRNDTL where v_type is CBill - we'll use this to filter sale invoices
    $trndtlQuery = TRNDTL::where('v_type', 'CBill')->with('accounts');
    
    // Apply date range filter if both start and end date are provided
    if ($startDate && $endDate) {
        $trndtlQuery->whereBetween('created_at', [$startDate, $endDate]);
    }

    // Apply other filters for TRNDTL
    if ($status) {
        $trndtlQuery->where('status', $status);
    }

    if ($v_no) {
        $trndtlQuery->where('v_no', $v_no);
    }
    
    if ($account_id) {
        $trndtlQuery->where('account_id', $account_id);
    }

    // Get TRNDTL records first
    $trnDetails = $trndtlQuery->orderBy('date', 'desc')->orderBy('v_no', 'desc')      ->get();

    // Get the v_nos from the filtered TRNDTL records
    $filteredVNos = $trnDetails->pluck('v_no')->unique()->toArray();

    // Query for SaleInvoice with relationships
    $saleInvoiceQuery = ConfectBilling::with(['items', 'product', 'itemType']);

    // Apply date range filter if both start and end date are provided
    if ($startDate && $endDate) {
        $saleInvoiceQuery->whereBetween('created_at', [$startDate, $endDate]);
    }

    // Apply other filters for SaleInvoice
    if ($v_no) {
        $saleInvoiceQuery->where('v_no', $v_no);
    }

    if ($item) {
        $saleInvoiceQuery->where('item', $item);
    }

    // Only show sale invoices that have matching TRNDTL records
    if (!empty($filteredVNos)) {
        $saleInvoiceQuery->whereIn('v_no', $filteredVNos);
    }

    // Get SaleInvoices
    $saleInvoices = $saleInvoiceQuery
    ->orderByRaw('DATE(created_at) DESC') // order by date only
    ->orderBy('v_no', 'desc')             // within same date, highest v_no first
    ->get();

    // Ensure rate and total are set for each invoice
$saleInvoices->transform(function ($invoice) {
    // If rate is missing, get from related item
    if (!isset($invoice->rate) || $invoice->rate === null) {
        $invoice->rate = $invoice->items->sale_rate ?? 0;
    }
    // If total is missing, get from related item or set to 0
    if (!isset($invoice->total) || $invoice->total === null) {
        $invoice->total = $invoice->items->purchase ?? 0;
    }
    return $invoice;
});

    // Get unique v_no values from both SaleInvoice and TRNDTL tables
    $vNoList = ConfectBilling::when(!empty($filteredVNos), function($query) use ($filteredVNos) {
            return $query->whereIn('v_no', $filteredVNos);
        })
        ->pluck('v_no')
        ->merge(TRNDTL::where('v_type', 'CBill')
            ->when($account_id, function($query) use ($account_id) {
                return $query->where('account_id', $account_id);
            })
            ->pluck('v_no'))
        ->unique()
        ->sort()
        ->values()
        ->toArray();

    // Fetch distinct item list from SaleInvoice
    $itemList = ConfectBilling::when(!empty($filteredVNos), function($query) use ($filteredVNos) {
            return $query->whereIn('v_no', $filteredVNos);
        })
        ->pluck('item')
        ->unique()
        ->toArray();
    
    $itemTitles = ItemType::whereIn('id', $itemList)
        ->pluck('type_title', 'id')
        ->toArray();

    // Fetch account list for TRNDTL and map to account titles from account_masters
    $accountList = TRNDTL::where('v_type', 'CBill')
        ->pluck('account_id')
        ->unique()
        ->toArray();
    $accountTitles = AccountMaster::whereIn('id', $accountList)
        ->pluck('title', 'id')
        ->toArray();
         // --- Calculate previous balance for each account ---
    $prevBalances = [];
    foreach ($trnDetails as $trn) {
        $accId = $trn->account_id;
        $trnDate = $trn->date;

        // Only calculate if showPrevBalance is enabled
        if ($showPrevBalance == 1) {
            $prevBalance = TRNDTL::where('account_id', $accId)
    ->where('date', '<', $trnDate)
    ->get()
    ->sum(function ($item) {
        return ($item->debit ?? 0) - ($item->credit ?? 0);
    });

            $prevBalances[$trn->id] = $prevBalance;
        } else {
            $prevBalances[$trn->id] = 0;
        }
    }

    // Attach previous balance to TRNDTL collection
    $trnDetails->transform(function($trn) use ($prevBalances) {
        $trn->pre_balance = $prevBalances[$trn->id] ?? 0;
        return $trn;
    });

    // Return the view with all necessary data
    return view('sale_reports.index7', [
        'saleInvoices' => $saleInvoices,
        'trnDetails' => $trnDetails,
        'vNoList' => $vNoList,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'status' => $status,
        'v_no' => $v_no,
        'item' => $item,
        'itemList' => $itemTitles,
        'accountList' => $accountTitles,
        'showPrevBalance' => $showPrevBalance,
    ]);
}

public function destroy($billing_no)
{
    ConfectBilling::where('billing_no', $billing_no)->delete();

    TRNDTL::where('v_type', 'CBill')
          ->where('r_id', $billing_no)
          ->delete();

    return redirect()->back()->with('success', 'Data deleted successfully for Bill No: ' . $billing_no);
}





}