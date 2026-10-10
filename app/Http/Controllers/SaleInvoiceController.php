<?php

namespace App\Http\Controllers;

use App\Models\ItemMaster;
use App\Models\ItemType;
use App\Models\TRNDTL;
use App\Models\ErpParam;
use Illuminate\Http\Request;
use App\Models\AccountMaster;
use App\Models\DeliveryMaster;
use App\Models\SaleInvoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SaleInvoiceController extends Controller
{

    public function index()
    {
        $loggedInUser = Auth::user();
        $accounts = AccountMaster::all();
        $delivery = DeliveryMaster::all();
        $items = ItemMaster::all();
        $saleAccounts = AccountMaster::all();
        return view('sales.pharma_billing.list', compact('loggedInUser', 'accounts', 'items', 'saleAccounts', 'delivery'));
    }
    
 public function getVnoss($accountId)
{
  // Fetch distinct voucher numbers for the given account ID (excluding NULLs)
$vnos = DeliveryMaster::where('account_id', $accountId)
                     ->whereNotNull('v_no')
                     ->distinct()
                     ->pluck('v_no')
                     ->toArray();

if (empty($vnos)) {
    return response()->json(['status' => 'no_vouchers']);
}

// Fetch only the used voucher numbers (optimized query)
$existingVnos = SaleInvoice::whereIn('old_vno', $vnos)
                          ->pluck('old_vno')
                          ->toArray();

// Separate into available and used
$usedVnos = array_intersect($vnos, $existingVnos);
$availableVnos = array_diff($vnos, $existingVnos);

return response()->json([
    'status' => 'success',
    'vnos' => array_values($availableVnos), // Reindex array
    'used_vnos' => array_values($usedVnos),
]);
}



public function getEntryDetailss($vno)
{
    // Fetch the DeliveryMaster and DeliveryDetail records based on the v_no
    $trndtl = DeliveryMaster::with(['deliveryDetails.products', 'accounts', 'deliveryDetails.itemType'])
                ->where('v_no', $vno)
                ->get();

    if ($trndtl->isNotEmpty()) {
        $entries = $trndtl->map(function ($data) {
            return [
                'date' => \Carbon\Carbon::parse($data->date)->format('d-m-Y'),
                'v_no' => $data->v_no,
                'preparedby' => $data->preparedby,
                'product_name' => $data->deliveryDetails->products->prod_name ?? 'N/A',
                'rate' => $data->deliveryDetails->products->rate ?? 'N/A',
                'product_id' => $data->deliveryDetails->products->id ?? null,
                'party' => $data->accounts->title ?? 'N/A',
                'item_type' => $data->deliveryDetails->itemType->type_title ?? 'N/A',
                'item_id' => $data->deliveryDetails->itemType->id ?? 'N/A',
                'box' => $data->deliveryDetails->box ?? 'N/A',
                'pack_qty' => $data->deliveryDetails->pack_qty ?? 'N/A',
                'batch_no' => $data->deliveryDetails->batch_no ?? 'N/A',
                'total' => $data->deliveryDetails->total ?? 'N/A',
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
    
     $billNo = SaleInvoice::max('billing_no');
    $maxBillNo = $billNo ? ((int) $billNo + 1) : 1;

    $data = $request->all();
    $accountId = $data['account']; // Correct account_id reference

    // Fetch max v_no for the given account_id
    $lastVno = SaleInvoice::where('account_id', $accountId)->max('v_no');

    // Increment or start from 1
    $vno = ($lastVno !== null) ? $lastVno + 1 : 1;

    $erpParam = ErpParam::first();
    $saleAccountId = $erpParam ? $erpParam->sale_ac : null;

    $preBal = TRNDTL::where(function($query) use ($accountId) {
        $query->where('account_id', $accountId)
              ->orWhere('cash_id', $accountId);
    })
    ->whereDate('date', '<', $data['date']) // Only transactions before bill date
    ->get()
    ->reduce(function ($carry, $item) use ($accountId) {
        $accountSum = ($item->account_id == $accountId) ? ($item->debit - $item->credit) : 0;
        $cashSum = ($item->cash_id == $accountId) ? ($item->debit - $item->credit) : 0;
        return $carry + ($accountSum - $cashSum);
    }, 0);


    foreach ($data['rate'] as $index => $rate) {
        $saleInvoice = SaleInvoice::create([
            'billing_no' => $maxBillNo,
            'v_no' => $vno,
            'old_vno' => $data['old_vno'][$index],
            'product_name' => $data['product_id'][$index] ?? 'N/A',
            'item' => $data['item_id'][$index] ?? 'N/A',
            'box' => $data['box'][$index] ?? 'N/A',
            'packing' => $data['packing'][$index] ?? 'N/A',
            'batch_no' => $data['batch_no'][$index] ?? 'N/A',
            'total' => $data['total'][$index] ?? 0,
            'rate' => $rate,
            'total_rate' => $data['total_rate'][$index] ?? 0,
            'account_id' => $accountId,
            'created_at' => $data['date'],
            'updated_at' =>  $data['date'] ,
        ]);
    }

    TRNDTL::create([
        'v_no' => $vno,
        'date' => $data['date'] ,
        'description' => $data['description'] ?? '',
        'account_id' => $accountId,
        'cash_id' => $saleAccountId,
        'preparedby' => auth()->user()->name,
        'credit' => 0,
        'debit' => $data['grand_total'] ?? 0,
        'status' => 'unofficial',
        'v_type' => 'PBill',
        'r_id' => $maxBillNo,
        'pre_bal' => $preBal,
         'created_at' => $data['date']  ,
            'updated_at' =>  $data['date'] ,
    ]);

    return redirect()->route('pharma_billing.reports')->with('success', 'PBill Created Successfully: ' . $vno);
}


public function reports(Request $request)
{
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $status = $request->input('status');
    $v_no = $request->input('v_no');
    $item = $request->input('item');
    $account_id = $request->input('account_id');

    // Query for TRNDTL where v_type is PBill - we'll use this to filter sale invoices
    $trndtlQuery = TRNDTL::where('v_type', 'PBill')->with('accounts');

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
    $trnDetails = $trndtlQuery->orderBy('created_at', 'desc') ->orderBy('v_no', 'desc')    ->get();

    // Get the v_nos from the filtered TRNDTL records
    $filteredVNos = $trnDetails->pluck('v_no')->unique()->toArray();

    // Query for SaleInvoice with relationships
    $saleInvoiceQuery = SaleInvoice::with(['items', 'product', 'itemType']);

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


    // Get unique v_no values from both SaleInvoice and TRNDTL tables
    $vNoList = SaleInvoice::when(!empty($filteredVNos), function($query) use ($filteredVNos) {
            return $query->whereIn('v_no', $filteredVNos);
        })
        ->pluck('v_no')
        ->merge(TRNDTL::where('v_type', 'PBill')
            ->when($account_id, function($query) use ($account_id) {
                return $query->where('account_id', $account_id);
            })
            ->pluck('v_no'))
        ->unique()
         ->sort() // This will sort the collection in ascending order
    ->values() // This will reset the array keys after sorting
        ->toArray();

    $itemList = SaleInvoice::when(!empty($filteredVNos), function($query) use ($filteredVNos) {
            return $query->whereIn('v_no', $filteredVNos);
        })
        ->pluck('item')
        ->unique()
        ->toArray();
    
    $itemTitles = ItemType::whereIn('id', $itemList)
        ->pluck('type_title', 'id')
        ->toArray();

    $accountList = TRNDTL::where('v_type', 'PBill')
        ->pluck('account_id')
        ->unique()
        ->toArray();
    $accountTitles = AccountMaster::whereIn('id', $accountList)
        ->pluck('title', 'id')
        ->toArray();

    $showPrevBalance = $request->input('show_prev_balance', '1');

    return view('sale_reports.index6', [
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
    SaleInvoice::where('billing_no', $billing_no)->delete();

    TRNDTL::where('v_type', 'PBill')
          ->where('r_id', $billing_no)
          ->delete();

    return redirect()->back()->with('success', 'Data deleted successfully for Bill No: ' . $billing_no);
}

}