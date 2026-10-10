<?php

namespace App\Http\Controllers;

use App\Models\TRNDTL;
use App\Models\ErpParam;
use App\Models\ItemMaster;
use Illuminate\Http\Request;
use App\Models\AccountMaster;
use App\Models\LaminationPurchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LeminationPurchaseController extends Controller
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
        return view('lemination_purchase.list',get_defined_vars());
    }
    public function store(Request $request)
    {
        $lastEntry = TRNDTL::where('v_type', $request->v_type)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastEntry && is_numeric($lastEntry->v_no)) {
            $lastInvoiceNumber = (int) $lastEntry->v_no; 
        } else {
            $lastInvoiceNumber = 0;
        }

        $newInvoiceNumber = $lastInvoiceNumber + 1;

        foreach ($request->entries as $index => $entry) {

$itemCode = DB::table('item_masters')
    ->where('id', $entry['item'] ?? 0)
    ->value('item_code');


            // Create the corresponding PurchaseDetail record with the same voucher number
            $purchaseGlue = LaminationPurchase::create([
                'item_id' => $entry['item'] ?? null,      // Fixed the spelling from 'lenght' to 'length'
                'qty' => $entry['quantity'] ?? 0,
                'size' => $entry['size'] ?? 0,
                'rate' => $entry['rate'] ?? 0,
                'amount' => $entry['amount'] ?? 0,
                'vorcher_no' => $newInvoiceNumber, 
                'freight' => $entry['freight'] ?? null,// Associate with the voucher
            ]);

             // Create a new TRNDTL record with the same voucher number
             $trndtl = TRNDTL::create([
                'v_no' => $newInvoiceNumber,
                'date' =>Carbon::now(),
                'account_id' => $entry['supplier'] ?? null,
                'preparedby' => $entry['prepared_by'] ?? null,
                'cash_id' => $entry['cash'] ?? null,
                'debit' => '0',
                'status' => 'unofficial',
                'credit' => $entry['amount'] ?? null,
                'v_type' => 'LPN',
                'description' => 
                ($itemCode) . 'x' .
    ($entry['size'] ?? 0) . '"' . 
     'x' .($entry['quantity'] ?? 0) . 'Rolls' . 
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
        'credit' =>  $entry['freight'] ?? null,
        'debit' => '0',
        'status' => 'unofficial',
        'v_type' => 'LPN',
        'r_id' => $purchaseGlue->id,
          'description'=> 'Freight',
    ]);}
    
        }

        // Return a success response
        return redirect()->route('lemination_purchase.reports')->with('success', 'LPN-' . $newInvoiceNumber . ' has been saved successfully.');
    }
    public function reports(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status'); // New status filter
        $v_no = $request->input('v_no');
    $account_id = $request->input('account_id');

        // Build the query with date range and status filters
        $query = TRNDTL::where('v_type', 'LPN')->where('debit', 0)->where('account_id', '!=', 35)->with('leminationpurchases');

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
                
                ->orderBy('v_no', 'desc')
                ->orderBy('id', 'desc')
                ->get();

        $accountMasters = AccountMaster::all();
        $items = ItemMaster::all();


           $vNo = TRNDTL::where('v_type', 'LPN')->pluck('v_no')->unique()->toArray();
    $accountId = AccountMaster::whereIn('id', TRNDTL::where('v_type', 'LPN')->pluck('account_id'))
    ->where('title', '!=', 'Purchase Freight') // Exclude "Purchase Freight"
    ->pluck('title', 'id');
    
        return view('purchase_reports.index5', [
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
                ->where('v_type', 'LPN')
                ->where('debit', 0)// Assuming LPN type for some voucher type
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
    return view('purchase_reports.edit5', compact('v_no', 'loggedInUser', 'voucher', 'erpParams', 'accountMasters', 'accountSuppliers', 'purchaseAccount', 'items'));
}

public function update(Request $request, $id)
{
    // Uncomment and set your validation rules as needed
    // $validatedData = $request->validate([...]);

    // Loop through each entry in the request
    foreach ($request->entries as $index => $entry) {
        // Find the existing TRNDTL record by v_no and v_type
        $trndtl = TRNDTL::where('v_no', $id)
                        ->where('v_type', 'LPN')
                        ->where('debit', 0)
                        ->first();

$itemCode = DB::table('item_masters')
    ->where('id', $entry['item'] ?? 0)
    ->value('item_code');


        // If TRNDTL record doesn't exist, handle this case appropriately
        if (!$trndtl) {
            return redirect()->back()->withErrors(['v_no' => 'No records found for voucher number: ' . $id]);
        }

        // Check if the current entry is an update or a new record
        if (isset($entry['r_id']) && $entry['r_id']) {
            // If updating an existing record
            $purchasePlate = LaminationPurchase::find($entry['r_id']);

            if (!$purchasePlate) {
                return redirect()->back()->withErrors(['r_id' => 'The purchase plate record was not found for ID: ' . $entry['r_id']]);
            }

            // Update the existing PurchaseDetail record
            $purchasePlate->update([
                'item_id' => $entry['item'] ?? null,
                'qty' => $entry['qty'] ?? 0,
                'rate' => $entry['rate'] ?? 0,
                'size' => $entry['size'] ?? 0,
                'amount' => $entry['amount'] ?? 0,
                'vorcher_no' => $id,
                  'freight' => $entry['freight'] ?? null,
            ]);
        } else {
            // If it's a new entry, create a new PurchaseReturn record
            $purchasePlate = LaminationPurchase::create([
                'item_id' => $entry['item'] ?? null,
                'qty' => $entry['qty'] ?? 0,
                'size' => $entry['size'] ?? 0,
                'rate' => $entry['rate'] ?? 0,
                'amount' => $entry['amount'] ?? 0,
                'vorcher_no' => $id,
                  'freight' => $entry['freight'] ?? null,
            ]);
        }

        // Update or create TRNDTL record
        TRNDTL::updateOrCreate(
            ['v_no' => $id, 'v_type' => 'LPN', 'r_id' => $purchasePlate->id],
            [
                'date' => $entry['date'] ?? null,
                'account_id' => $entry['supplier'] ?? null,
                'preparedby' => $entry['prepared_by'] ?? null,
                'cash_id' => $entry['cash'] ?? null,
                'credit' => $entry['amount'] ?? null,
                'debit' => '0',
                'status' => 'unofficial',
                'v_type' => 'LPN',
                'description' => 
                ($itemCode) . 'x' .
    ($entry['size'] ?? 0) . '"' . 
     'x' .($entry['qty'] ?? 0) . 'Rolls' . 
    '@' .($entry['rate'] ?? 0),
            ]
        );
        
        
    
        
          $erpParam = ErpParam::first(); 
          $Purfreight = $erpParam ? $erpParam->pur_freight : null;
    $PurfreightExp = $erpParam ? $erpParam->pur_freight_exp : null;
    
        if ($entry['freight'] > 0) {
            TRNDTL::create([
        'v_no' => $id,
        'date' => Carbon::now(),
        'account_id' => $PurfreightExp,
        'cash_id' => $Purfreight,
        'preparedby' => $entry['prepared_by'] ?? null,
        'credit' =>  $entry['freight'] ?? null,
        'debit' => '0',
        'status' => 'unofficial',
        'v_type' => 'LPN',
        'r_id' => $purchasePlate->id,
         'description'=> 'Freight',
    ]);}
    
    }

    if ($request->ajax()) {
        return response()->json(['success' => 'PIN-' . $id . ' has been updated successfully.'], 200);
    }

    return back(); // Fallback for non-AJAX requests
}


public function destroy($id)
{
    $trndtl = TRNDTL::find($id);

    if (!$trndtl) {
        return redirect()->back()->with('error', 'Record not found.');
    }

    if ($trndtl->v_type == 'LPN') {
        // Delete only TRNDTL records with same r_id AND v_type = 'LPN'
        TRNDTL::where('r_id', $trndtl->r_id)
              ->where('v_type', 'LPN')
              ->delete();

        // Delete the related LaminationPurchase record if it exists
        $purchaseDetail = LaminationPurchase::find($trndtl->r_id);
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
                         ->where('v_type', 'LPN')
                         ->where('description', 'freight')
                         ->first();

    // Set the freight value to 0 if no matching record is found
    $freight = $freightData ? $freightData->credit : 0;

    // Fetch the freight_type from the PurchaseDetail table
    $purchaseDetail = LaminationPurchase::where('vorcher_no', $v_no)->first();
    $freight_type = $purchaseDetail ? $purchaseDetail->freight_type : null;

    // Sum the qty from purchase_details for the same voucher_no
    $totalQty = LaminationPurchase::where('vorcher_no', $v_no)->sum('qty');

    // Pass $freight, $v_no, $totalQty, and $freight_type to the view
    return view('purchase_reports.editLamination', compact('freight', 'v_no', 'totalQty', 'freight_type'));
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
                                 ->where('v_type', 'LPN')
                                 ->where('description', 'Freight')
                                 ->first();

        // Check if a record with vorcher_no == $id exists in PurchaseDetail
        $existingPurchaseDetail = LaminationPurchase::where('vorcher_no', $id)->first();

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
                $purchaseDetail = LaminationPurchase::create([
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
                    'v_type' => 'LPN',
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

       return redirect()->route('lemination_purchase.reports')->with('success', 'Freight updated successfully for LPN-' . $id);

    } catch (\Exception $e) {
        // Handle any exceptions and return an error message
        return redirect()->route('lemination_purchase.reports')->with('error', 'An error occurred: ' . $e->getMessage());
    }
}




}
