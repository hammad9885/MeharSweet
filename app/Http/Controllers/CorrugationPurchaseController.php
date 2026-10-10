<?php

namespace App\Http\Controllers;

use App\Models\TRNDTL;
use App\Models\ErpParam;
use App\Models\ItemMaster;
use Illuminate\Http\Request;
use App\Models\AccountMaster;
use App\Models\CorrugationPurchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CorrugationPurchaseController extends Controller
{
    public function index()
    {
        $loggedInUser = Auth::user();
        $items = ItemMaster::all();
        $erpParams = ErpParam::with('level2')->get();

        $accountMasters = collect();
        $accountSuppliers = collect();
        $purchaseAccount = null;

        if ($erpParams->isNotEmpty()) {
            $cashLevelId = $erpParams->first()->cash_level;
            $supplierLevelId = $erpParams->first()->supplier_level;
            $accountMasters = AccountMaster::where('level2_id', $cashLevelId)->get();
            $accountSuppliers = AccountMaster::whereIn('level2_id', [4, 23])->get();
            $purchaseAccountId = $erpParams->first()->purchase_account;
            $purchaseAccount = AccountMaster::find($purchaseAccountId);
        }
        return view('corrugation_purchase.list', get_defined_vars());
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

            $purchaseGlue = CorrugationPurchase::create([
                'item_id' => $entry['item'] ?? null,
                'qty' => $entry['quantity'] ?? 0,
                'size' => $entry['size'] ?? 0,
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
                'v_type' => 'CPN',
                'r_id' => $purchaseGlue->id,
                'description' => 
                ($itemCode) . 'x' .
   ($entry['size'] ?? 0) . '"' . 
     'x' .($entry['quantity'] ?? 0) . 'Rolls' . 
    '@' .($entry['rate'] ?? 0),
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
        'v_type' => 'CPN',
        'r_id' => $purchaseGlue->id,
          'description'=> 'Freight',
    ]);}
    
    
        }

        return redirect()
            ->route('corrugation_purchase.reports')
            ->with('success', 'CPN-' . $newInvoiceNumber . ' has been saved successfully.');
    }

    public function reports(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status');
        $v_no = $request->input('v_no');
    $account_id = $request->input('account_id');

        $query = TRNDTL::where('v_type', 'CPN')->where('debit', 0)->where('account_id', '!=', 35)->with('corrugationpurchases');

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }

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
        
         $vNo = TRNDTL::where('v_type', 'CPN')->pluck('v_no')->unique()->toArray();
    $accountId = AccountMaster::whereIn('id', TRNDTL::where('v_type', 'CPN')->pluck('account_id'))
    ->where('title', '!=', 'Purchase Freight') // Exclude "Purchase Freight"
    ->pluck('title', 'id');
    
    $totalQty = CorrugationPurchase::where('vorcher_no', $v_no)->sum('qty');
    
        return view('purchase_reports.index6', [
            'trndtl' => $trndtl,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'status' => $status,
            'accountMasters' => $accountMasters,
            'items' => $items,
            'vNo' => $vNo,
        'accountId' => $accountId,
            'totalQty' => $totalQty,
        ]);
    }
    public function edit($v_no)
    {
        $loggedInUser = Auth::user();
        $voucher = TRNDTL::where('v_no', $v_no)->where('v_type', 'CPN')->where('account_id', '!=', 35)->where('debit', 0)->get();
        $items = ItemMaster::all();
        $erpParams = ErpParam::with('level2')->get();
        $accountMasters = collect();
        $accountSuppliers = collect();
        $purchaseAccount = null;

        if ($erpParams->isNotEmpty()) {
            $cashLevelId = $erpParams->first()->cash_level;
            $supplierLevelId = $erpParams->first()->supplier_level;
            $accountMasters = AccountMaster::where('level2_id', $cashLevelId)->get();
        $accountSuppliers = AccountMaster::whereIn('level2_id', [4, 23])->get();
            $purchaseAccountId = $erpParams->first()->purchase_account;
            $purchaseAccount = AccountMaster::find($purchaseAccountId);
        }

        $items = ItemMaster::all();

        return view('purchase_reports.edit6', compact('v_no', 'loggedInUser', 'voucher', 'erpParams', 'accountMasters', 'accountSuppliers', 'purchaseAccount', 'items'));
    }

   public function update(Request $request, $id)
{
    foreach ($request->entries as $index => $entry) {
        // Find the existing TRNDTL entry, or create a new one if it does not exist
        $trndtl = TRNDTL::where('v_no', $id)
                        ->where('v_type', 'CPN')
                        ->where('debit', 0)
                        ->first();
        
        $itemCode = DB::table('item_masters')
    ->where('id', $entry['item'] ?? 0)
    ->value('item_code');


        if (!$trndtl) {
            return redirect()->back()->withErrors(['v_no' => 'No records found for voucher number: ' . $id]);
        }

        // Check if 'r_id' exists in the entry and update or create the purchase plate
        if (isset($entry['r_id']) && $entry['r_id']) {
            $purchasePlate = CorrugationPurchase::find($entry['r_id']);

            if (!$purchasePlate) {
                return redirect()->back()->withErrors(['r_id' => 'The Corrugation plate record was not found for ID: ' . $entry['r_id']]);
            }
            
            // Update the purchase plate if found
            $purchasePlate->update([
                'item_id' => $entry['item'] ?? null,
                'qty' => $entry['qty'] ?? 0,
                'size' => $entry['size'] ?? 0,
                'rate' => $entry['rate'] ?? 0,
                'amount' => $entry['amount'] ?? 0,
                'vorcher_no' => $id,
                'freight' => $entry['freight'] ?? null,
            ]);
        } else {
            // Create a new purchase plate if 'r_id' is not provided
            $purchasePlate = CorrugationPurchase::create([
                'item_id' => $entry['item'] ?? null,
                'qty' => $entry['qty'] ?? 0,
                'size' => $entry['size'] ?? 0,
                'rate' => $entry['rate'] ?? 0,
                'amount' => $entry['amount'] ?? 0,
                'vorcher_no' => $id,
                'freight' => $entry['freight'] ?? null,
            ]);
        }

        // Ensure TRNDTL entry is created or updated with the correct information
        TRNDTL::updateOrCreate(
            ['v_no' => $id, 'v_type' => 'CPN', 'r_id' => $purchasePlate->id],
            [
                'date' => $entry['date'] ?? now(),
                'account_id' => $entry['supplier'] ?? null,
                'preparedby' => $entry['prepared_by'] ?? null,
                'cash_id' => $entry['cash'] ?? null,
                'debit' => '0',  // Ensure this is set correctly, since it's a fixed value
                'status' => 'unofficial',
                'credit' => $entry['amount'] ?? 0,  // Ensure the credit is correctly populated
                'v_type' => 'CPN',
                'description' => 
                ($itemCode) . 'x' .
    ($entry['size'] ?? 0) . '"' . 
     'x' .($entry['quantity'] ?? 0) . 'Rolls' . 
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
        'v_type' => 'CPN',
          'description'=> 'Freight',
        'r_id' => $purchasePlate->id,
    ]);}
    }

    // Return to the previous page
    return back(); 
}



public function destroy($id)
{
    $trndtl = TRNDTL::find($id);

    if (!$trndtl) {
        return redirect()->back()->with('error', 'Record not found.');
    }

    if ($trndtl->v_type == 'CPN') {
        // Delete all related TRNDTL records where r_id matches
        TRNDTL::where('r_id', $trndtl->r_id)->where('v_type', 'CPN')->delete();

        // Delete the related ShipperPurchases record if it exists
        $purchaseDetail = CorrugationPurchase::find($trndtl->r_id);
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
                         ->where('v_type', 'CPN')
                         ->where('description', 'freight')
                         ->first();

    // Set the freight value to 0 if no matching record is found
    $freight = $freightData ? $freightData->credit : 0;

    // Fetch the freight_type from the PurchaseDetail table
    $purchaseDetail = CorrugationPurchase::where('vorcher_no', $v_no)->first();
    $freight_type = $purchaseDetail ? $purchaseDetail->freight_type : null;

    // Sum the qty from purchase_details for the same voucher_no
    $totalQty = CorrugationPurchase::where('vorcher_no', $v_no)->sum('qty');

    // Pass $freight, $v_no, $totalQty, and $freight_type to the view
    return view('purchase_reports.editCorrugation', compact('freight', 'v_no', 'totalQty', 'freight_type'));
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
                                 ->where('v_type', 'CPN')
                                 ->where('description', 'Freight')
                                 ->first();

        // Check if a record with vorcher_no == $id exists in PurchaseDetail
        $existingPurchaseDetail = CorrugationPurchase::where('vorcher_no', $id)->first();

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
                $purchaseDetail = CorrugationPurchase::create([
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
                    'v_type' => 'CPN',
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

       return redirect()->route('corrugation_purchase.reports')->with('success', 'Freight updated successfully for CPN-' . $id);

    } catch (\Exception $e) {
        // Handle any exceptions and return an error message
        return redirect()->route('corrugation_purchase.reports')->with('error', 'An error occurred: ' . $e->getMessage());
    }
}



}
