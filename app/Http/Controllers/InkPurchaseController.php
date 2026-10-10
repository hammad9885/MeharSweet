<?php

namespace App\Http\Controllers;

use App\Models\TRNDTL;
use App\Models\ErpParam;
use App\Models\ItemMaster;
use App\Models\InkPurchase;
use Illuminate\Http\Request;
use App\Models\AccountMaster;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InkPurchaseController extends Controller
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
            $accountSuppliers = AccountMaster::whereIn('level2_id', [4, 23])->get();
            $purchaseAccountId = $erpParams->first()->purchase_account;
            $purchaseAccount = AccountMaster::find($purchaseAccountId);
        }
        return view('ink_purchase.list',get_defined_vars());
    }
   public function store(Request $request)
{
    $maxVoucherNo = InkPurchase::max('vorcher_no');
$newInvoiceNumber = $maxVoucherNo ? ((int) $maxVoucherNo + 1) : 1;
    
    
    
    foreach ($request->entries as $index => $entry) {
        $itemCode = DB::table('item_masters')
    ->where('id', $entry['item'] ?? 0)
    ->value('item_code');

        $purchaseGlue = InkPurchase::create([
            'item_code' => $entry['item'] ?? null,
            'qty' => $entry['quantity'] ?? 0,
            'rate' => $entry['rate'] ?? 0,
            'amount' => $entry['amount'] ?? 0,
            'vorcher_no' => $newInvoiceNumber,
            'freight' => $entry['freight'] ?? null,
        ]);
        $trndtl = TRNDTL::create([
            'v_no' => $newInvoiceNumber,
            'date' => Carbon::now(),
            'account_id' => $entry['supplier'] ?? null,
            'preparedby' => $entry['prepared_by'] ?? null,
            'cash_id' => $entry['cash'] ?? null,
            'debit' => '0',
            'status' => 'unofficial',
            'credit' => $entry['amount'] ?? null,
            'v_type' => 'IPN',
            'description' => 
            ($itemCode) . 'x' .
     ($entry['quantity'] ?? 0) . 'Rolls' . 
    '@' .($entry['rate'] ?? 0),
            'r_id' => $purchaseGlue->id
        ]);
        
        
        $erpParam = ErpParam::first(); 
    $cashAccId = $erpParam ? $erpParam->cash_acc : null;
     $Purfreight = $erpParam ? $erpParam->pur_freight : null;
    $PurfreightExp = $erpParam ? $erpParam->pur_freight_exp : null;
    
        if ($entry['freight'] > 0) {
            TRNDTL::create([
        'v_no' => $newInvoiceNumber,
        'date' => Carbon::now(),
        'account_id' => $PurfreightExp,
        'cash_id' => $Purfreight,
        'preparedby' => $entry['prepared_by'] ?? null,
        'credit' => $entry['freight'] ?? null,
        'debit' =>  '0',
        'status' => 'unofficial',
        'v_type' => 'IPN',
          'description'=> 'Freight',
        'r_id' => $purchaseGlue->id,
    ]);}
        
        
        
        
        
        
    }

    return redirect()->route('ink_purchase.reports')->with('success', 'Voucher No. ' . $newInvoiceNumber . ' has been saved successfully.');
}



    public function reports(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status'); // New status filter
        $v_no = $request->input('v_no');
    $account_id = $request->input('account_id');

        // Build the query with date range and status filters
        $query = TRNDTL::where('v_type', 'IPN')->where('debit', 0)->where('account_id', '!=', 35)->with('inkpurchases');

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
        
        $vNo = TRNDTL::where('v_type', 'IPN')->pluck('v_no')->unique()->toArray();
    $accountId = AccountMaster::whereIn('id', TRNDTL::where('v_type', 'IPN')->pluck('account_id'))
    ->where('title', '!=', 'Purchase Freight') // Exclude "Purchase Freight"
    ->pluck('title', 'id');

        return view('purchase_reports.index7', [
            'trndtl' => $trndtl,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'status' => $status, // Pass status to view
            'accountMasters' => $accountMasters,
            'vNo' => $vNo,
        'accountId' => $accountId,
        ]);
    }
  public function edit($v_no)
{
    // Find the voucher and its entries by voucher number (v_no)
    $loggedInUser = Auth::user();
    $voucher = TRNDTL::where('v_no', $v_no)
                ->where('v_type', 'IPN') 
                ->where('debit', 0)// Assuming GPN type for a voucher
                ->where('account_id', '!=', 35)
                ->get(); // Fetch all entries for this voucher

    $erpParams = ErpParam::with('level2')->get();
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
     $accountSuppliers = AccountMaster::whereIn('level2_id', [4, 23])->get();
        $purchaseAccountId = $erpParams->first()->purchase_account;
        $purchaseAccount = AccountMaster::find($purchaseAccountId);
    }

    $items = ItemMaster::all();

    // Pass v_no explicitly to the view
    return view('purchase_reports.edit7', compact('v_no', 'loggedInUser', 'voucher', 'erpParams', 'accountMasters', 'accountSuppliers', 'purchaseAccount', 'items'));
}



public function update(Request $request, $id)
{
    DB::beginTransaction();

    try {
        foreach ($request->entries as $entry) {
            // Fetch TRNDTL record for voucher number
            $trndtl = TRNDTL::where('v_no', $id)
                            ->where('v_type', 'IPN')
                            ->where('debit', 0)
                            ->first();

            if (!$trndtl) {
                return $request->ajax()
                    ? response()->json(['error' => 'No TRNDTL record found for voucher: ' . $id], 400)
                    : redirect()->back()->withErrors(['v_no' => 'No records found for voucher number: ' . $id]);
            }

            $itemCode = DB::table('item_masters')
                        ->where('id', $entry['item'] ?? 0)
                        ->value('item_code');

            $inkPurchase = InkPurchase::create([
                'item_code' => $entry['item'] ?? null,
                'qty' => $entry['quantity'] ?? 0,
                'rate' => $entry['rate'] ?? 0,
                'amount' => $entry['amount'] ?? 0,
                'vorcher_no' => $id,
                'freight' => $entry['freight'] ?? null,
            ]);

            TRNDTL::create([
                'v_no' => $id,
                'v_type' => 'IPN',
                'r_id' => $inkPurchase->id,
                'date' => $entry['date'] ?? null,
                'account_id' => $entry['supplier'] ?? null,
                'preparedby' => $entry['prepared_by'] ?? null,
                'cash_id' => $entry['cash'] ?? null,
                'credit' => $entry['amount'] ?? 0,
                'debit' => 0,
                'status' => 'unofficial',
                'description' => ($itemCode ?? '') . ' x ' . ($entry['quantity'] ?? 0) . ' Rolls @ ' . ($entry['rate'] ?? 0),
            ]);

            $erpParam = ErpParam::first(); 
            $cashAccId = $erpParam ? $erpParam->cash_acc : null;
             $Purfreight = $erpParam ? $erpParam->pur_freight : null;
    $PurfreightExp = $erpParam ? $erpParam->pur_freight_exp : null;

            if (!empty($entry['freight'])) {
                TRNDTL::create([
                    'v_no' => $id,
                    'date' => Carbon::now(),
                    'account_id' => $PurfreightExp,
                    'cash_id' => $Purfreight,
                    'preparedby' => $entry['prepared_by'] ?? null,
                    'credit' => $entry['freight'] ?? 0,
                    'debit' => 0, 
                    'status' => 'unofficial',
                    'v_type' => 'IPN',
                    'r_id' => $inkPurchase->id,
                    'description' => 'Freight',
                ]);
            }
        }

        DB::commit();

        return $request->ajax()
            ? response()->json(['success' => 'New entries have been added successfully for PIN-' . $id], 200)
            : redirect()->back()->with('success', 'New entries have been added successfully for PIN-' . $id);
    } catch (\Exception $e) {
        DB::rollBack();

        \Log::error('Update Error: ' . $e->getMessage());

        return $request->ajax()
            ? response()->json(['error' => 'An error occurred while adding new entries. Please try again.'], 500)
            : redirect()->back()->withErrors(['error' => 'An error occurred while adding new entries.']);
    }
}



public function destroy($id)
{
    $trndtl = TRNDTL::find($id);

    if (!$trndtl) {
        return redirect()->back()->with('error', 'Record not found.');
    }

    if ($trndtl->v_type === 'IPN') {
        // Delete all related TRNDTL records where r_id matches
        TRNDTL::where('r_id', $trndtl->r_id)->where('v_type', 'IPN')->delete();

        // Delete the related ShipperPurchases record if it exists
        $purchaseDetail = InkPurchase::find($trndtl->r_id);
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





public function editBoxboard($v_no)
{
    // Query the TRNDTL model to find freight data
    $freightData = TRNDTL::where('v_no', $v_no)
                         ->where('v_type', 'IPN')
                         ->where('description', 'freight')
                         ->first();

    // Set the freight value to 0 if no matching record is found
    $freight = $freightData ? $freightData->credit : 0;

    // Fetch the freight_type from the PurchaseDetail table
    $purchaseDetail = InkPurchase::where('vorcher_no', $v_no)->first();
    $freight_type = $purchaseDetail ? $purchaseDetail->freight_type : null;

    // Sum the qty from purchase_details for the same voucher_no
    $totalQty = InkPurchase::where('vorcher_no', $v_no)->sum('qty');

    // Pass $freight, $v_no, $totalQty, and $freight_type to the view
    return view('purchase_reports.editInk', compact('freight', 'v_no', 'totalQty', 'freight_type'));
}
public function updateBoxboard(Request $request, $id)
{
    // Validate the request
    $validatedData = $request->validate([
        'total_freight' => 'required|numeric|min:0',
        'freight_type' => 'required|string', // Add validation for freight_type
    ]);

    try {
        // Fetch ERP parameters
        $erpParam = ErpParam::first();
        if (!$erpParam) {
            throw new \Exception('ERP parameters not found.');
        }

        $cashAccId = $erpParam->cash_acc;
        $Purfreight = $erpParam->pur_freight;
        $PurfreightExp = $erpParam->pur_freight_exp;

        // Check if a record with v_type == 'BPN' and description == 'Freight' exists in TRNDTL
        $existingFreight = TRNDTL::where('v_no', $id)
                                 ->where('v_type', 'IPN')
                                 ->where('description', 'Freight')
                                 ->first();

        // Check if a record with vorcher_no == $id exists in PurchaseDetail
        $existingPurchaseDetail = InkPurchase::where('vorcher_no', $id)->first();

        // If total_freight is greater than 0
        if ($validatedData['total_freight'] > 0) {
            // Update or create PurchaseDetail record
            if ($existingPurchaseDetail) {
                // Update the existing PurchaseDetail record
                $existingPurchaseDetail->update([
                    'freight' => $validatedData['total_freight'],
                    'freight_type' => $validatedData['freight_type'], // Add this line
                ]);

                // Get the id of the updated PurchaseDetail record
                $purchaseDetailId = $existingPurchaseDetail->id;
            } else {
                // Create a new PurchaseDetail record
                $purchaseDetail = InkPurchase::create([
                    'vorcher_no' => $id,
                    'freight' => $validatedData['total_freight'],
                    'freight_type' => $validatedData['freight_type'], // Add this line
                    // Add other necessary fields here
                ]);

                // Get the id of the newly created PurchaseDetail record
                $purchaseDetailId = $purchaseDetail->id;
            }

            // Update or create TRNDTL record
            if ($existingFreight) {
                // Update the existing TRNDTL record
                $existingFreight->update([
                    'credit' => $validatedData['total_freight'],
                    'preparedby' => Auth::user()->name ?? null,
                    'date' => Carbon::now(),
                    'r_id' => $purchaseDetailId, // Set r_id to the PurchaseDetail id
                ]);
            } else {
                // Create a new TRNDTL record
                TRNDTL::create([
                    'v_no' => $id,
                    'date' => Carbon::now(),
                    'account_id' => $PurfreightExp,
                    'cash_id' => $Purfreight,
                    'preparedby' => Auth::user()->name ?? null,
                    'credit' => $validatedData['total_freight'],
                    'debit' => '0',
                    'status' => 'unofficial',
                    'v_type' => 'IPN',
                    'description' => 'Freight',
                    'r_id' => $purchaseDetailId, // Set r_id to the PurchaseDetail id
                ]);
            }
        } else {
            // If total_freight is 0, delete the existing TRNDTL record (if any)
            if ($existingFreight) {
                $existingFreight->delete();
            }

            // If total_freight is 0, update the PurchaseDetail record (if any)
            if ($existingPurchaseDetail) {
                $existingPurchaseDetail->update([
                    'freight' => 0,
                ]);
            }
        }

       return redirect()->route('ink_purchase.reports')->with('success', 'Freight updated successfully for IPN-' . $id);

    } catch (\Exception $e) {
        // Handle any exceptions and return an error message
        return redirect()->route('ink_purchase.reports')->with('error', 'An error occurred: ' . $e->getMessage());
    }
}




}
