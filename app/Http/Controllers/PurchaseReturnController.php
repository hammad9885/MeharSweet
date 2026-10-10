<?php

namespace App\Http\Controllers;

use App\Models\TRNDTL;
use App\Models\ErpParam;
use App\Models\ItemMaster;
use Illuminate\Http\Request;
use App\Models\AccountMaster;
use App\Models\PurchaseDetail;
use App\Models\PurchaseReturn;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PurchaseReturnController extends Controller
{
    public function index()
    {
        $loggedInUser = Auth::user();
        $items = ItemMaster::all();
        $erpParams = ErpParam::with('level2')->get();

        // Initialize accountMasters as an empty collection to avoid errors
        $accountMasters = collect();
        $accountSuppliers = collect();
        $purchaseAccount = null;

        // Check if there is at least one ERP Param and that cash_level is set
        if ($erpParams->isNotEmpty()) {
            // Get the cash_level from the first ERP Param
            $cashLevelId = $erpParams->first()->cash_level;
            $supplierLevelId = $erpParams->first()->supplier_level;
            // Fetch AccountMasters associated with the cash_level
            $accountMasters = AccountMaster::where('level2_id', $cashLevelId)->get();
            $accountSuppliers = AccountMaster::where('level2_id', 4)->get();
            $purchaseAccountId = $erpParams->first()->purchase_account;
            $purchaseAccount = AccountMaster::find($purchaseAccountId);
        }
        return view('purchase_return.list',get_defined_vars());
    }
    public function getItemDetails($id)
    {
        $item = ItemMaster::find($id); // Assuming you have an 'Item' model

        if ($item) {
            return response()->json([
                'gramage' => $item->gramage,
                'purchase' => $item->purchase,
            ]);
        }

        return response()->json(['error' => 'Item not found'], 404);
    }
   
   
   public function store(Request $request)
{
    // Fetch the last TRNDTL record for the specific v_type (BRV or CRV)
    $lastEntry = TRNDTL::where('v_type', $request->v_type)
        ->orderBy('id', 'desc')
        ->first();

    // If there are no previous records for this v_type, start from 1
    if ($lastEntry && is_numeric($lastEntry->v_no)) {
        $lastInvoiceNumber = (int) $lastEntry->v_no; // Extract the numeric part of the v_no
    } else {
        $lastInvoiceNumber = 0; // Start from 0 if no previous invoice exists for this v_type
    }

    // Increment the invoice number by 1 for this batch of entries
    $newInvoiceNumber = $lastInvoiceNumber + 1;
    
    // Get ERP parameters
        $erpParam = ErpParam::first();
        if (!$erpParam || !$erpParam->purchase_return_account) {
            return redirect()->back()->with('error', 'Cash Account is not configured in ERP Params.');
        }
        $cashAccountId = $erpParam->purchase_return_account;

    // Loop through each entry in the request
    foreach ($request->entries as $index => $entry) {
    // Fetch the item_code for the current entry's item ID
    $itemCode = DB::table('item_masters')
        ->where('id', $entry['item'] ?? 0)
        ->value('item_code');

    // Use a fallback if the item_code is not found
    $itemCode = $itemCode ?: 'No Item Code Found';

    // Create the corresponding PurchaseDetail record
    $purchaseDetail = PurchaseReturn::create([
        'item_code' => $entry['item'] ?? null, // Store the ID if needed
        'width' => $entry['width'] ?? 0,
        'lenght' => $entry['length'] ?? 0,
        'grammage' => $entry['gramage'] ?? 0,
        'qty' => $entry['quantity'] ?? 0,
        'rate' => $entry['rate'] ?? 0,
        'amount' => $entry['amount'] ?? 0,
        'total_wt' => $entry['weight'] ?? 0,
        'vorcher_no' => $newInvoiceNumber,
        'freight' => $entry['freight'] ?? 0,
    ]);

    // Create a new TRNDTL record
    $trndtl = TRNDTL::create([
        'v_no' => $newInvoiceNumber,
        'date' => Carbon::now(),
        'account_id' => $entry['supplier'] ?? null,
        'preparedby' => $entry['prepared_by'] ?? null,
        'cash_id' =>  $cashAccountId,
        'credit' => '0',
        'debit' => $entry['amount'] ?? null,
        'v_type' => 'PRN',
        'status' => 'unofficial',
        'description' => 
            ($itemCode) . 'x' . 
            ($entry['width'] ?? 0) . 'x' . 
            ($entry['length'] ?? 0) . 'x' . 
            ($entry['gramage'] ?? 0) . 'x' . 
            ($entry['quantity'] ?? 0) . 'Rolls' . 
            '@' . ($entry['rate'] ?? 0),
        'r_id' => $purchaseDetail->id,
    ]);
    
    
  
    
}

    return redirect()->route('purchase_return.reports')->with('success', '' . $request->v_type . '-' . $newInvoiceNumber . ' has been saved successfully.');
}

    public function reports(Request $request)
{
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $status = $request->input('status'); // New status filter
      $v_no = $request->input('v_no');
    $account_id = $request->input('account_id');

    // Build the query with date range and status filters
    $query = TRNDTL::where('v_type', 'PRN')->where('credit', 0)->where('account_id', '!=', 35)->with('purchasereturns');

    if ($startDate && $endDate) {
        $query->whereBetween('date', [$startDate, $endDate]);
    }

    // Apply status filter if it is selected
    if ($status) {
        $query->where('status', $status);
    }
    
     if ($v_no) {
        $query->where('v_no', $v_no);
    }
    
    if ($account_id) {
        $query->where('account_id', $account_id);
    }

    $trndtl = $query
                ->orderBy('date', 'desc')
                ->orderBy('id', 'desc')
                ->orderBy('v_no', 'desc')
                ->get();

    $accountMasters = AccountMaster::all();
     $items = ItemMaster::all();
     $vNo = TRNDTL::where('v_type', 'PRN')->pluck('v_no')->unique()->toArray();
    $accountId = AccountMaster::whereIn('id', TRNDTL::where('v_type', 'PRN')->pluck('account_id'))
    ->where('title', '!=', 'Purchase Freight') // Exclude "Purchase Freight"
    ->pluck('title', 'id');

    return view('purchase_reports.index2', [
        'trndtl' => $trndtl,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'status' => $status, // Pass status to view
        'accountMasters' => $accountMasters,
        'vNo' => $vNo,
        'accountId' => $accountId,
    ]);
}





public function destroy($id)
{
    $trndtl = TRNDTL::find($id);

    if (!$trndtl) {
        return redirect()->back()->with('error', 'Record not found.');
    }

    if ($trndtl->v_type === 'PRN') {
        // Delete all related TRNDTL records where r_id matches
        TRNDTL::where('r_id', $trndtl->r_id)->where('v_type', 'PRN')->delete();

        // Delete the related ShipperPurchases record if it exists
        $purchaseDetail = PurchaseReturn::find($trndtl->r_id);
        if ($purchaseDetail) {
            $purchaseDetail->delete();
        }
    } else {
        // Delete only the individual TRNDTL record
        $trndtl->delete();
    }

    return redirect()->back()->with('success', 'Record deleted successfully.');
}



public function delete($id)
{
    
    return $this->destroy($id);
}




public function edit($v_no)
{
    $loggedInUser = Auth::user();
    $voucher = TRNDTL::where('v_no', $v_no)
                ->where('v_type', 'PRN')
                ->where('debit', 0)->where('account_id', '!=', 35)
                ->get(); 

    $erpParams = ErpParam::with('level2')->get();
    $accountMasters = collect();
    $accountSuppliers = collect();
    $purchaseAccount = null;

    if ($erpParams->isNotEmpty()) {
        $cashLevelId = $erpParams->first()->cash_level;
        $supplierLevelId = $erpParams->first()->supplier_level;
        $accountMasters = AccountMaster::where('level2_id', $cashLevelId)->get();
        $accountSuppliers = AccountMaster::where('level2_id', 4)->get();
        $purchaseAccountId = $erpParams->first()->purchase_account;
        $purchaseAccount = AccountMaster::find($purchaseAccountId);
    }

    $items = ItemMaster::all();

    return view('purchase_reports.edit2', compact('v_no', 'loggedInUser', 'voucher', 'erpParams', 'accountMasters', 'accountSuppliers', 'purchaseAccount', 'items'));
}




public function update(Request $request, $id)
{
    DB::beginTransaction();

    try {
        foreach ($request->entries as $index => $entry) {
            // Fetch TRNDTL record for voucher number
            $trndtl = TRNDTL::where('v_no', $id)
                            ->where('v_type', 'PRN')
                            ->where('debit', 0)
                            ->first();
            
            $itemCode = DB::table('item_masters')
                        ->where('id', $entry['item'] ?? 0)
                        ->value('item_code');

            if (!$trndtl) {
                return $request->ajax()
                    ? response()->json(['error' => 'No TRNDTL record found for voucher: ' . $id], 400)
                    : redirect()->back()->withErrors(['v_no' => 'No records found for voucher number: ' . $id]);
            }

            // Create a new PurchaseDetail record for each entry (don't update existing ones)
            $purchaseReturn = PurchaseReturn::create([
                'item_code' => $entry['item'] ?? null,
                'width' => $entry['width'] ?? 0,
                'lenght' => $entry['length'] ?? 0,
                'grammage' => $entry['gramage'] ?? 0,
                'qty' => $entry['quantity'] ?? 0,
                'rate' => $entry['rate'] ?? 0,
                'amount' => $entry['amount'] ?? 0,
                'total_wt' => $entry['weight'] ?? 0,
                'vorcher_no' => $id,
                'freight' => $entry['freight'] ?? 0,
            ]);

            // Create a new TRNDTL record for the new purchase detail
            TRNDTL::create([
                'v_no' => $id, 
                'v_type' => 'PRN', 
                'r_id' => $purchaseReturn->id,
                'date' => Carbon::now(),
                'account_id' => $entry['supplier'] ?? null,
                'preparedby' => $entry['prepared_by'] ?? null,
                'cash_id' => $entry['cash'] ?? null,
                'debit' => $entry['amount'] ?? null,
                'credit' => 0,
                'status' => 'unofficial',
                'description' =>
                    ($itemCode ?? '') . 'x' . 
                    ($entry['width'] ?? 0) . 'x' . 
                    ($entry['length'] ?? 0) . 'x' . 
                    ($entry['gramage'] ?? 0) . 'x' . 
                    ($entry['quantity'] ?? 0) . ' Rolls @ ' .
                    ($entry['rate'] ?? 0),
            ]);

            

           
        }

        DB::commit();

        return $request->ajax()
            ? response()->json(['success' => 'New entries have been added successfully for PIN-' . $id], 200)
            : redirect()->route('purchase_return.reports')->with('success', 'New entries have been added successfully for PIN-' . $id);
    } catch (\Exception $e) {
        DB::rollBack();

        // Log the exception for debugging
        \Log::error('Update Error: ' . $e->getMessage());

        return $request->ajax()
            ? response()->json(['error' => 'An error occurred while adding new entries. Please try again.'], 500)
            : redirect()->route('purchase_return.reports')->withErrors(['error' => 'An error occurred while adding new entries.']);
    }
}





}
