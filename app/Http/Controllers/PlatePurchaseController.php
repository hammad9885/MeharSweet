<?php

namespace App\Http\Controllers;

use App\Models\TRNDTL;
use App\Models\ErpParam;
use App\Models\ItemMaster;
use Illuminate\Http\Request;
use App\Models\AccountMaster;
use App\Models\PurchasePlate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ProductMaster;
use App\Models\Country;
use Carbon\Carbon;

class PlatePurchaseController extends Controller
{
    public function index()
    {
        $loggedInUser = Auth::user();
        $items = ItemMaster::all();
        $erpParams = ErpParam::with('level2')->get();
        $product = ProductMaster::all();
        $countries = Country::all();
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
        return view('plate_purchase.list',get_defined_vars());
    }
    
    public function getProductsByCountry(Request $request)
{
    $countryId = $request->input('country_id');
    
    // Assuming you have a relationship between products and countries
    $products = ProductMaster::where('country_id', $countryId)->get(['id', 'prod_name']);
    
    return response()->json($products);
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

        // Loop through each entry in the request
        foreach ($request->entries as $index => $entry) {
        
        $itemCode = DB::table('item_masters')
    ->where('id', $entry['item'] ?? 0)
    ->value('item_code');


            // Create the corresponding PurchaseDetail record with the same voucher number
            $purchasePlate = PurchasePlate::create([
                'item_code' => $entry['item'] ?? null,
                'product_name' => $entry['product'] ?? 0,
                'description' => $entry['length'] ?? 0,       // Fixed the spelling from 'lenght' to 'length'
                'qty' => $entry['quantity'] ?? 0,
                'country' => $entry['country'] ?? 0,
                'rate' => $entry['rate'] ?? 0,
                'amount' => $entry['amount'] ?? 0,
                'vorcher_no' => $newInvoiceNumber,   // Associate with the voucher
                'freight' => $entry['freight'] ?? null,
            ]);

             // Create a new TRNDTL record with the same voucher number
             $trndtl = TRNDTL::create([
                'v_no' => $newInvoiceNumber,
                'date' => Carbon::now(),
                'account_id' => $entry['supplier'] ?? null,
                'preparedby' => $entry['prepared_by'] ?? null,
                'cash_id' => $entry['cash'] ?? null,
                'debit' => '0',
                'credit' => $entry['amount'] ?? null,
                'v_type' => 'PPN',
                'status' => 'unofficial',
                'description' => 
                ($itemCode) . 'x' .
    ($entry['product'] ?? 0) . 'x' . 
    ($entry['length'] ?? 0) . 'x' . 
    ($entry['quantity'] ?? 0) . 'Rolls' . 
   '@' . ($entry['rate'] ?? 0),
                'r_id' => $purchasePlate->id
            ]);
            
            $erpParam = ErpParam::first(); 
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
        'v_type' => 'PPN',
        'r_id' => $purchasePlate->id,
          'description'=> 'Freight',
    ]);}
            
            
            
            
            
        }

        // Return a success response
        return redirect()->route('plate_purchase.reports')->with('success', '' . $request->v_type . '-' . $newInvoiceNumber . ' has been saved successfully.');
    }
    public function reports(Request $request)
    {
         $products = ProductMaster::all();
         $countries = Country::all();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status'); // New status filter
        $v_no = $request->input('v_no');
    $account_id = $request->input('account_id');

        // Build the query with date range and status filters
        $query = TRNDTL::where('v_type', 'PPN')->where('debit', 0)->where('account_id', '!=', 35)->with('purchaseplates');

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
  $vNo = TRNDTL::where('v_type', 'PPN')->pluck('v_no')->unique()->toArray();
    $accountId = AccountMaster::whereIn('id', TRNDTL::where('v_type', 'PPN')->pluck('account_id'))
    ->where('title', '!=', 'Purchase Freight') // Exclude "Purchase Freight"
    ->pluck('title', 'id');

        $accountMasters = AccountMaster::all();

        return view('purchase_reports.index3', [
            'trndtl' => $trndtl,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'status' => $status, // Pass status to view
            'products' => $products,
            'countries' => $countries,
            'accountMasters' => $accountMasters,
            'vNo' => $vNo,
        'accountId' => $accountId,
        ]);
    }
    public function edit($v_no)
{
    $countries = Country::all();
    // Find the voucher and its entries by voucher number (v_no)
    $loggedInUser = Auth::user();
    $voucher = TRNDTL::where('v_no', $v_no)
                ->where('v_type', 'PPN') 
                ->where('debit', 0)// Assuming PPN type for Payment Voucher
                ->where('account_id', '!=', 35)
                ->get(); // Fetch all entries for this voucher

    $erpParams = ErpParam::with('level2')->get();
    $accountMasters = collect();
    $accountSuppliers = collect();
    $purchaseAccount = null;
$product = ProductMaster::all();
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

    // Explicitly pass v_no to the view
    return view('purchase_reports.edit3', compact('v_no', 'loggedInUser','countries', 'voucher', 'product' , 'erpParams', 'accountMasters', 'accountSuppliers', 'purchaseAccount', 'items'));
}


public function update(Request $request, $id)
{
    DB::beginTransaction();

    try {
        foreach ($request->entries as $index => $entry) {
            // Fetch TRNDTL record for voucher number
            $trndtl = TRNDTL::where('v_no', $id)
                            ->where('v_type', 'PPN')
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
            $purchasePlate = PurchasePlate::create([
                 'item_code' => $entry['item'] ?? null,
                    'product_name' => $entry['product'] ?? 0,
                    'description' => $entry['length'] ?? 0,
                    'qty' => $entry['quantity'] ?? 0,
                    'rate' => $entry['rate'] ?? 0,
                    'country' => $entry['country'] ?? 0,
                    'amount' => $entry['amount'] ?? 0,
                    'vorcher_no' => $id,
                    'freight' => $entry['freight'] ?? null,
            ]);

            // Create a new TRNDTL record for the new purchase detail
            TRNDTL::create([
                'v_no' => $id, 
                'v_type' => 'PPN',
                'r_id' => $purchasePlate->id,
                'date' => $entry['date'] ?? null,
                    'account_id' => $entry['supplier'] ?? null,
                    'preparedby' => $entry['prepared_by'] ?? null,
                    'cash_id' => $entry['cash'] ?? null,
                    'credit' => $entry['amount'] ?? null,
                    'debit' => '0',
                    'status' => 'unofficial',
                    'description' => 
                        ($itemCode) . 'x' .
                        ($entry['product'] ?? 0) . 'x' . 
                        ($entry['length'] ?? 0) . 'x' . 
                        ($entry['quantity'] ?? 0) . 'Rolls' . 
                        '@'.($entry['rate'] ?? 0),
            ]);

            // Fetch ERP parameters
            $erpParam = ErpParam::first(); 
            $cashAccId = $erpParam ? $erpParam->cash_acc : null;
            $Purfreight = $erpParam ? $erpParam->pur_freight : null;
    $PurfreightExp = $erpParam ? $erpParam->pur_freight_exp : null;

            if ($entry['freight'] > 0) {
                TRNDTL::create([
                    'v_no' => $id,
                    'date' => Carbon::now(),
                    'account_id' => $PurfreightExp,
                    'cash_id' => $Purfreight,
                    'preparedby' => $entry['prepared_by'] ?? null,
                    'credit' => $entry['freight'] ?? null,
                    'debit' => '0',
                    'status' => 'unofficial',
                    'v_type' => 'PPN',
                    'description' => 'Freight',
                    'r_id' => $purchasePlate->id,
                ]);
            }
        }

        DB::commit();

        return $request->ajax()
            ? response()->json(['success' => 'New entries have been added successfully for PIN-' . $id], 200)
            : redirect()->route('plate_purchase.reports')->with('success', 'New entries have been added successfully for PIN-' . $id);
    } catch (\Exception $e) {
        DB::rollBack();

        // Log the exception for debugging
        \Log::error('Update Error: ' . $e->getMessage());

        return $request->ajax()
            ? response()->json(['error' => 'An error occurred while adding new entries. Please try again.'], 500)
            : redirect()->route('plate_purchase.reports')->withErrors(['error' => 'An error occurred while adding new entries.']);
    }
}





public function destroy($id)
{
    $trndtl = TRNDTL::find($id);

    if (!$trndtl) {
        return redirect()->back()->with('error', 'Record not found.');
    }

    if ($trndtl->v_type == 'PPN') {
        // Delete all related TRNDTL records where r_id matches
        TRNDTL::where('r_id', $trndtl->r_id)->where('v_type', 'PPN')->delete();

        // Delete the related ShipperPurchases record if it exists
        $purchaseDetail = PurchasePlate::find($trndtl->r_id);
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


}
