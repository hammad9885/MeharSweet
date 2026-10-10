<?php

namespace App\Http\Controllers;

use App\Models\TRNDTL;
use App\Models\ErpParam;
use App\Models\ItemMaster;
use Illuminate\Http\Request;
use App\Models\AccountMaster;
use App\Models\PlateReturn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ProductMaster;
use App\Models\Country;
use Carbon\Carbon;

class PlateReturnController extends Controller
{
    public function index()
    {
        $loggedInUser = Auth::user();
        $items = ItemMaster::all();
        $erpParams = ErpParam::with('level2')->get();
        $product = ProductMaster::all();
        $countries = Country::all();
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
        return view('plate_return.list',get_defined_vars());
    }
    
  
   public function store(Request $request)
    {
        $lastEntry = TRNDTL::where('v_type','Plate-Return')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastEntry && is_numeric($lastEntry->v_no)) {
            $lastInvoiceNumber = (int) $lastEntry->v_no; 
        } else {
            $lastInvoiceNumber = 0; 
        }

        $newInvoiceNumber = $lastInvoiceNumber + 1;
        
        // Get ERP parameters
        $erpParam = ErpParam::first();
        if (!$erpParam || !$erpParam->purchase_return_account) {
            return redirect()->back()->with('error', 'Cash Account is not configured in ERP Params.');
        }
        $cashAccountId = $erpParam->purchase_return_account;

        foreach ($request->entries as $index => $entry) {
        
        $itemCode = DB::table('item_masters')
    ->where('id', $entry['item'] ?? 0)
    ->value('item_code');


            $purchasePlate = PlateReturn::create([
                'item_code' => $entry['item'] ?? null,
                'product_name' => $entry['product'] ?? 0,
                'description' => $entry['description'] ?? 0,     
                'qty' => $entry['quantity'] ?? 0,
                'country' => $entry['country'] ?? 0,
                'rate' => $entry['rate'] ?? 0,
                'amount' => $entry['amount'] ?? 0,
                'vorcher_no' => $newInvoiceNumber,   
            ]);

             $trndtl = TRNDTL::create([
                'v_no' => $newInvoiceNumber,
                'date' => Carbon::now(),
                'account_id' => $entry['supplier'] ?? null,
                'preparedby' => $entry['prepared_by'] ?? null,
                'cash_id' =>  $cashAccountId,
                'debit' => $entry['amount'] ?? null,
                'credit' => '0', 
                'v_type' => 'Plate-Return',
                'status' => 'unofficial',
                'description' => 
                ($itemCode) . 'x' .
    ($entry['product'] ?? 0) . 'x' . 
    ($entry['description'] ?? 0) . 'x' . 
    ($entry['quantity'] ?? 0) . 'Rolls' . 
   '@' . ($entry['rate'] ?? 0),
                'r_id' => $purchasePlate->id
            ]);
            
        }

        return redirect()->route('plate_return.reports')->with('success', '' . 'Plate-Return' . '-' . $newInvoiceNumber . ' has been saved successfully.');
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
        $query = TRNDTL::where('v_type', 'Plate-Return')->where('credit', 0)->where('account_id', '!=', 35)->with('platereturns');

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
  $vNo = TRNDTL::where('v_type', 'Plate-Return')->pluck('v_no')->unique()->toArray();
    $accountId = AccountMaster::whereIn('id', TRNDTL::where('v_type', 'Plate-Return')->pluck('account_id'))
    ->where('title', '!=', 'Purchase Freight') // Exclude "Purchase Freight"
    ->pluck('title', 'id');

        $accountMasters = AccountMaster::all();

        return view('plate_return.index', [
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

public function destroy($id)
{
    try {
        DB::beginTransaction();
        
        $plateReturn = PlateReturn::findOrFail($id);

        // Delete associated TRNDTL record
        TRNDTL::where('v_type', 'Plate-Return')
              ->where('r_id', $plateReturn->id)
              ->delete();

        // Delete the PlateReturn record
        $plateReturn->delete();

        DB::commit();

        return redirect()->route('plate_return.reports')
             ->with('success', 'Plate Return record deleted successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->route('plate_return.reports')
             ->with('error', 'Failed to delete record: ' . $e->getMessage());
    }
}

}
