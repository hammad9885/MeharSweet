<?php

namespace App\Http\Controllers;

use App\Models\TRNDTL;
use App\Models\ErpParam;
use App\Models\ItemMaster;
use Illuminate\Http\Request;
use App\Models\AccountMaster;
use App\Models\PurchaseDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PaymentInvoiceController extends Controller
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
            $accountSuppliers = AccountMaster::where('level2_id',$supplierLevelId)->get();
            $purchaseAccountId = $erpParams->first()->purchase_account;
        $purchaseAccount = AccountMaster::find($purchaseAccountId);
        }
        return view('payment_invoice.list',get_defined_vars());
    }
    
    public function getItemDetails($id)
{
    $item = ItemMaster::find($id);

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
    // Get the highest v_no for this v_type
    $lastInvoiceNumber = TRNDTL::where('v_type', $request->v_type)
        ->max('v_no');

    // If no records found, start from 0
    $lastInvoiceNumber = $lastInvoiceNumber ?? 0;

    // Increment voucher number
    $newInvoiceNumber = $lastInvoiceNumber + 1;

    // Fetch ERP Parameters once
    $erpParam = ErpParam::first();
    $cashAccId     = $erpParam ? $erpParam->cash_acc : null;
    $Purfreight    = $erpParam ? $erpParam->pur_freight : null;
    $PurfreightExp = $erpParam ? $erpParam->pur_freight_exp : null;

    // Loop through request entries
    foreach ($request->entries as $entry) {
        $itemCode = DB::table('item_masters')
            ->where('id', $entry['item'] ?? 0)
            ->value('item_code');

        // Save purchase details
        $purchaseDetail = PurchaseDetail::create([
            'item_code'  => $entry['item'] ?? null,
            'width'      => $entry['width'] ?? 0,
            'lenght'     => $entry['length'] ?? 0,
            'grammage'   => $entry['gramage'] ?? 0,
            'qty'        => $entry['quantity'] ?? 0,
            'rate'       => $entry['rate'] ?? 0,
            'amount'     => $entry['amount'] ?? 0,
            'total_wt'   => $entry['weight'] ?? 0,
            'freight'    => $entry['freight'] ?? 0,
            'vorcher_no' => $newInvoiceNumber,
        ]);

        // Save main TRNDTL
        TRNDTL::create([
            'v_no'        => $newInvoiceNumber,
            'date'        => $entry['date'] ??Carbon::now(),
            'account_id'  => $entry['supplier'] ?? null,
            'cash_id'     => $entry['cash'] ?? null,
            'preparedby'  => $entry['prepared_by'] ?? null,
            'credit'      => $entry['amount'] ?? null,
            'debit'       => 0,
            'status'      => 'unofficial',
            'v_type'      => $request->v_type,
            'description' =>
                ($itemCode) . 'x' .
                ($entry['width'] ?? 0) . 'x' .
                ($entry['length'] ?? 0) . 'x' .
                ($entry['gramage'] ?? 0) . 'x' .
                ($entry['quantity'] ?? 0) . 'Rolls' .
                '@' . ($entry['rate'] ?? 0),
            'r_id' => $purchaseDetail->id,
        ]);

        // Freight entry
        if (($entry['freight'] ?? 0) > 0) {
            TRNDTL::create([
                'v_no'        => $newInvoiceNumber,
                'date'        => $entry['date'] ?? Carbon::now(),
                'account_id'  => $PurfreightExp,
                'cash_id'     => $Purfreight,
                'preparedby'  => $entry['prepared_by'] ?? null,
                'credit'      => 0,
                'debit'       => 0,
                'status'      => 'unofficial',
                'v_type'      => $request->v_type,
                'r_id'        => $purchaseDetail->id,
                'description' => 'Freight',
            ]);
        }
    }

    return redirect()->route('payment_invoice.reports')
        ->with('success', 'Voucher ' . $request->v_type . '-' . $newInvoiceNumber . ' has been saved successfully.');
}






public function reports(Request $request)
{
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $status = $request->input('status'); // New status filter
    $v_no = $request->input('v_no');
    $account_id = $request->input('account_id');

  $query = TRNDTL::where('v_type', 'BPN')
    ->where('debit', 0)
    ->where('account_id', '!=', 35)
    ->with(['purchasedetails', 'accounts']);

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

    // Sort by date (latest on top) and then by v_no
    $trndtl = $query
                ->orderBy('date', 'desc')
                ->orderBy('id', 'desc')
                ->orderBy('v_no', 'desc')
                ->get();

    $accountMasters = AccountMaster::all();
    
    // Fix incorrect method chaining
    $vNo = TRNDTL::where('v_type', 'BPN')->pluck('v_no')->unique()->toArray();
    $accountId = AccountMaster::whereIn('id', TRNDTL::where('v_type', 'BPN')->pluck('account_id'))
    ->where('title', '!=', 'Purchase Freight') // Exclude "Purchase Freight"
    ->pluck('title', 'id');



    return view('purchase_reports.index', [
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
    $loggedInUser = Auth::user();
    $voucher = TRNDTL::where('v_no', $v_no)
                ->where('v_type', 'BPN')
                ->where('debit', 0)
                ->where('account_id', '!=', 35)
                ->get();
                
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


    // Pass v_no explicitly to the view
    return view('purchase_reports.edit', compact('v_no', 'loggedInUser', 'voucher', 'erpParams', 'accountMasters', 'accountSuppliers', 'purchaseAccount', 'items'));
}



public function update(Request $request, $id)
{
    DB::beginTransaction();

    try {
        foreach ($request->entries as $index => $entry) {
            // Fetch TRNDTL record for voucher number
            $trndtl = TRNDTL::where('v_no', $id)
                            ->where('v_type', 'BPN')
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
            $purchaseDetail = PurchaseDetail::create([
                'item_code' => $entry['item'] ?? null,
                'width' => $entry['width'] ?? 0,
                'lenght' => $entry['length'] ?? 0,
                'grammage' => $entry['gramage'] ?? 0,
                'qty' => $entry['quantity'] ?? 0,
                'rate' => $entry['rate'] ?? 0,
                'amount' => $entry['amount'] ?? 0,
                'total_wt' => $entry['weight'] ?? 0,
                'freight' =>0,
                'vorcher_no' => $id,
            ]);

            // Create a new TRNDTL record for the new purchase detail
            TRNDTL::create([
                'v_no' => $id,
                'date' => $entry['date'] ??Carbon::now(),
                'account_id' => $entry['supplier'] ?? null,
                'preparedby' => $entry['prepared_by'] ?? null,
                'cash_id' => $entry['cash'] ?? null,
                'credit' => $entry['amount'] ?? null,
                'debit' => '0',
                'v_type' => 'BPN',
                'status' => 'unofficial',
                'description' => 
                    ($itemCode) . 'x' .
                    ($entry['width'] ?? 0) . 'x' . 
                    ($entry['length'] ?? 0) . 'x' . 
                    ($entry['gramage'] ?? 0) . 'x' . 
                    ($entry['quantity'] ?? 0) . 'Rolls' . 
                    '@'.($entry['rate'] ?? 0),
                'r_id' => $purchaseDetail->id, // Associate with the new purchase detail
            ]);

            // Fetch ERP parameters
            $erpParam = ErpParam::first(); 
            $cashAccId = $erpParam ? $erpParam->cash_acc : null;
           $Purfreight = $erpParam ? $erpParam->pur_freight : null;
    $PurfreightExp = $erpParam ? $erpParam->pur_freight_exp : null;

            if ($entry['freight'] > 0) {
                TRNDTL::create([
                    'v_no' => $id,
                    'date' => $entry['date'] ??Carbon::now(),
                    'account_id' => $PurfreightExp,
                    'cash_id' => $Purfreight,
                    'preparedby' => $entry['prepared_by'] ?? null,
                    'credit' => 0,
                    'debit' => '0',
                    'status' => 'unofficial',
                    'v_type' => 'BPN',
                    'description'=> 'Freight',
                    'r_id' => $purchaseDetail->id,
                ]);
            }
        }

        DB::commit();

        return $request->ajax()
            ? response()->json(['success' => 'New entries have been added successfully for BPN-' . $id], 200)
            : redirect()->back()->with('success', 'New entries have been added successfully for BPN-' . $id);
    } catch (\Exception $e) {
        DB::rollBack();

        // Log the exception for debugging
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

    if ($trndtl->v_type === 'BPN') {
        // Delete all related TRNDTL records where r_id matches
        TRNDTL::where('r_id', $trndtl->r_id)
        ->where('v_type', 'BPN')->delete();

        // Delete the related ShipperPurchases record if it exists
        $purchaseDetail = PurchaseDetail::find($trndtl->r_id);
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
                         ->where('v_type', 'BPN')
                         ->where('description', 'freight')
                         ->first();

    // Set the freight value to 0 if no matching record is found
    $freight = $freightData ? $freightData->credit : 0;

    // Fetch the freight_type from the PurchaseDetail table
    $purchaseDetail = PurchaseDetail::where('vorcher_no', $v_no)->first();
    $freight_type = $purchaseDetail ? $purchaseDetail->freight_type : null;

    // Sum the qty from purchase_details for the same voucher_no
    $totalQty = PurchaseDetail::where('vorcher_no', $v_no)->sum('qty');

    // Pass $freight, $v_no, $totalQty, and $freight_type to the view
    return view('purchase_reports.editBoxboard', compact('freight', 'v_no', 'totalQty', 'freight_type'));
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
                                 ->where('v_type', 'BPN')
                                 ->where('description', 'Freight')
                                 ->first();

        // Check if a record with vorcher_no == $id exists in PurchaseDetail
        $existingPurchaseDetail = PurchaseDetail::where('vorcher_no', $id)->first();

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
                $purchaseDetail = PurchaseDetail::create([
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
                    'v_type' => 'BPN',
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

       return redirect()->route('payment_invoice.reports')->with('success', 'Freight updated successfully for BPN-' . $id);

    } catch (\Exception $e) {
        // Handle any exceptions and return an error message
        return redirect()->route('payment_invoice.reports')->with('error', 'An error occurred: ' . $e->getMessage());
    }
}
}
