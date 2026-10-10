<?php

namespace App\Http\Controllers;

use App\Models\ItemMaster;
use App\Models\TRNDTL;
use App\Models\ErpParam;
use Illuminate\Http\Request;
use App\Models\AccountMaster;
use App\Models\DyePurchase; 
use App\Models\GeneralJobSheet; 
use App\Models\DeliveryMaster;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GeneralJobSheetController extends Controller
{


   public function index()
{
    $loggedInUser = Auth::user();
    $accounts = AccountMaster::whereIn('level2_id', [4, 7])->get();
    $saleAccounts = AccountMaster::all();
    $items = ItemMaster::all();
    return view('general_job_sheet.list', compact('loggedInUser', 'items', 'accounts', 'saleAccounts'));
}
    
public function getPurchaseItems(Request $request)
{
    try {
        $request->validate([
            'purchase_type' => 'required',
            'view' => 'required',
            'item_column' => 'required'
        ]);
        
        $query = DB::table($request->view);
        
        // For Boxboard, include all necessary fields
        if ($request->purchase_type === 'Purchase Boxboard') {
            $items = $query->select([
                'item_code',
                'length',
                'width',
                'remain_qty'
            ])->get();
        } 
        // For Lamination and Corrugation, include size
        elseif ($request->purchase_type === 'Lamination Purchase' || $request->purchase_type === 'Corrugation Purchase') {
            $items = $query->select([
                $request->item_column,
                'remain_qty',
                'size'
            ])->get();
        } 
        else {
            // For other types, include at least remain_qty
            $items = $query->select([
                $request->item_column,
                'remain_qty'
            ])->get();
        }
        
        return response()->json($items);
        
    } catch (\Exception $e) {
        return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
    }
}

public function getPurchaseItemDetails(Request $request)
{
    try {
        $request->validate([
            'purchase_type' => 'required',
            'view' => 'required',
            'item_column' => 'required',
            'item_value' => 'required'
        ]);
        
        // For Boxboard, we need to include length and width in the query if they're in the item_value
        if ($request->purchase_type === 'Purchase Boxboard') {
            // If item_value contains dimensions (from the dropdown), parse them
            if (strpos($request->item_value, '|') !== false) {
                $parts = explode('|', $request->item_value);
                $item_code = $parts[0];
                $length = $parts[1];
                $width = $parts[2];
                
                $item = DB::table($request->view)
                         ->where($request->item_column, $item_code)
                         ->where('length', $length)
                         ->where('width', $width)
                         ->first();
            } else {
                // Fallback to original behavior
                $item = DB::table($request->view)
                         ->where($request->item_column, $request->item_value)
                         ->first();
            }
        } else {
            $item = DB::table($request->view)
                     ->where($request->item_column, $request->item_value)
                     ->first();
        }
                 
        if (!$item) {
            \Log::warning('Purchase item not found', [
                'view' => $request->view,
                'item_column' => $request->item_column,
                'item_value' => $request->item_value
            ]);
            return response()->json(['error' => 'Item not found'], 404);
        }
        
        $response = ['remain_qty' => $item->remain_qty];
        
        // Add additional fields based on purchase type
        switch($request->purchase_type) {
            case 'Purchase Boxboard':
                $response['length'] = $item->length ?? null;
                $response['width'] = $item->width ?? null;
                break;
            case 'Purchase Plate':
                $response['product_name'] = $item->product_name ?? null;
                $response['country_name'] = $item->country_name ?? null;
                break;
            case 'Lamination Purchase':
            case 'Corrugation Purchase':
                $response['size'] = $item->size ?? null;
                break;
        }
        
        \Log::info('Purchase item details fetched', [
            'view' => $request->view,
            'item_column' => $request->item_column,
            'item_value' => $request->item_value
        ]);
        
        return response()->json($response);
        
    } catch (\Exception $e) {
        \Log::error('Error fetching purchase item details', [
            'error' => $e->getMessage(),
            'view' => $request->view,
            'item_column' => $request->item_column,
            'item_value' => $request->item_value
        ]);
        return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
    }
}


public function store(Request $request)
{
    // Validate the request data
    $validatedData = $request->validate([
        'prepared_by' => 'required|string',
        'account' => 'required|exists:account_masters,id',
        'product_type' => 'required|string',
        'item_name' => 'required|string',
        'qty' => 'required|numeric|min:0.01',
        'rate' => 'required|numeric|min:0',
        'description' => 'nullable|string',
        
        // Conditional fields based on product type
        'length' => 'nullable|numeric|required_if:product_type,Purchase Boxboard',
        'width' => 'nullable|numeric|required_if:product_type,Purchase Boxboard',
        'product_name' => 'nullable|string|required_if:product_type,Purchase Plate',
        'country_name' => 'nullable|string|required_if:product_type,Purchase Plate',
        'size' => 'nullable|numeric|required_if:product_type,Lamination Purchase,Corrugation Purchase',
    ]);

    try {
        // Get the maximum v_no from the table
        $maxVno = GeneralJobSheet::max('v_no');
        
        // Increment by 1 or start from 1 if no records exist
        $newVno = $maxVno ? $maxVno + 1 : 1;

        // Create a new GeneralJobSheet record
        $jobSheet = new GeneralJobSheet();
        
        // Set the basic fields
        $jobSheet->v_no = $newVno;
        $jobSheet->prepared_by =  auth()->user()->name;
        $jobSheet->account_id = $validatedData['account'];
        $jobSheet->product_type = $validatedData['product_type'];
        $jobSheet->item_name = $validatedData['item_name'];
        $jobSheet->qty = $validatedData['qty'];
        $jobSheet->rate = $validatedData['rate'];
        $jobSheet->description = $validatedData['description'] ?? null;
        
        // Set fields based on product type
        switch ($validatedData['product_type']) {
            case 'Purchase Boxboard':
                $jobSheet->length = $validatedData['length'];
                $jobSheet->width = $validatedData['width'];
                break;
                
            case 'Purchase Plate':
                $jobSheet->product_name = $validatedData['product_name'];
                $jobSheet->country_name = $validatedData['country_name'];
                break;
                
            case 'Lamination Purchase':
            case 'Corrugation Purchase':
                $jobSheet->size = $validatedData['size'];
                break;
        }
        
        
        // Save the record
        $jobSheet->save();
        
        // Return success response
        return redirect()->route('general_job_sheet.report')->with('success', 'General Job Sheet created successfully with Voucher No: ' . $newVno);
        
    } catch (\Exception $e) {
        // Return error response if something goes wrong
        return redirect()->back()
            ->with('error', 'Error creating General Job Sheet: ' . $e->getMessage())
            ->withInput();
    }
}


public function report(Request $request)
{
    $query = GeneralJobSheet::query()->with('account');
    
    // Apply filters
    if ($request->has('start_date') && $request->start_date) {
        $query->whereDate('created_at', '>=', $request->start_date);
    }

    if ($request->has('end_date') && $request->end_date) {
        $query->whereDate('created_at', '<=', $request->end_date);
    }

    if ($request->has('v_no') && $request->v_no) {
        $query->where('v_no', $request->v_no);
    }

    if ($request->has('account_id') && $request->account_id) {
        $query->where('account_id', $request->account_id);
    }
    
    if ($request->has('employee') && $request->employee) {
        $query->where('employee_type', $request->employee);
    }
    
    $generalJobSheets = $query->get();
    
    // Get only account_ids that exist in general_job_sheets
    $vNos = GeneralJobSheet::distinct()->pluck('v_no');
    $accountIds = GeneralJobSheet::with('account')
        ->select('account_id')
        ->distinct()
        ->get()
        ->pluck('account.title', 'account_id')
        ->filter();
    
    return view('general_job_sheet.index', compact('generalJobSheets', 'vNos', 'accountIds'));
}


public function destroy($id)
{
    try {
        // Find the GeneralJobSheet by ID
        $jobSheet = GeneralJobSheet::findOrFail($id);

        // Delete the record
        $jobSheet->delete();

        // Return success response
        return redirect()->back()->with('success', 'General Job Sheet deleted successfully.');
    } catch (\Exception $e) {
        // Return error response if something goes wrong
        return redirect()->back()
            ->with('error', 'Error deleting General Job Sheet: ' . $e->getMessage());
    }
}


public function edit($id)
{
    try {
        // Find the GeneralJobSheet by ID with account relationship
        $jobSheet = GeneralJobSheet::with('account')->findOrFail($id);
        
        // Get necessary data for the form
        $loggedInUser = Auth::user();
        $accounts = AccountMaster::whereIn('level2_id', [4, 7])->get();
        $saleAccounts = AccountMaster::all();
        $items = ItemMaster::all();
        
        return view('general_job_sheet.edit', compact(
            'jobSheet', 
            'loggedInUser', 
            'items', 
            'accounts', 
            'saleAccounts'
        ));
        
    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Error loading edit form: ' . $e->getMessage());
    }
}

public function update(Request $request, $id)
{
    // Validate the request data
    $validatedData = $request->validate([
        'prepared_by' => 'required|string',
        'account' => 'required|exists:account_masters,id',
        'product_type' => 'required|string',
        'item_name' => 'required|string',
        'qty' => 'required|numeric|min:0.01',
        'rate' => 'required|numeric|min:0',
        'description' => 'nullable|string',
        
        // Conditional fields based on product type
        'length' => 'nullable|numeric|required_if:product_type,Purchase Boxboard',
        'width' => 'nullable|numeric|required_if:product_type,Purchase Boxboard',
        'product_name' => 'nullable|string|required_if:product_type,Purchase Plate',
        'country_name' => 'nullable|string|required_if:product_type,Purchase Plate',
        'size' => 'nullable|numeric|required_if:product_type,Lamination Purchase,Corrugation Purchase',
    ]);

    try {
        // Find the existing GeneralJobSheet record
        $jobSheet = GeneralJobSheet::findOrFail($id);
        
        // Update the basic fields
        $jobSheet->prepared_by = auth()->user()->name;
        $jobSheet->account_id = $validatedData['account'];
        $jobSheet->product_type = $validatedData['product_type'];
        $jobSheet->item_name = $validatedData['item_name'];
        $jobSheet->qty = $validatedData['qty'];
        $jobSheet->rate = $validatedData['rate'];
        $jobSheet->description = $validatedData['description'] ?? null;
        
        // Update fields based on product type
        switch ($validatedData['product_type']) {
            case 'Purchase Boxboard':
                $jobSheet->length = $validatedData['length'];
                $jobSheet->width = $validatedData['width'];
                // Clear other type-specific fields
                $jobSheet->product_name = null;
                $jobSheet->country_name = null;
                $jobSheet->size = null;
                break;
                
            case 'Purchase Plate':
                $jobSheet->product_name = $validatedData['product_name'];
                $jobSheet->country_name = $validatedData['country_name'];
                // Clear other type-specific fields
                $jobSheet->length = null;
                $jobSheet->width = null;
                $jobSheet->size = null;
                break;
                
            case 'Lamination Purchase':
            case 'Corrugation Purchase':
                $jobSheet->size = $validatedData['size'];
                // Clear other type-specific fields
                $jobSheet->length = null;
                $jobSheet->width = null;
                $jobSheet->product_name = null;
                $jobSheet->country_name = null;
                break;
        }
        
        // Save the updated record
        $jobSheet->save();
        
        // Return success response
        return redirect()->route('general_job_sheet.report')
            ->with('success', 'General Job Sheet updated successfully.');
        
    } catch (\Exception $e) {
        // Return error response if something goes wrong
        return redirect()->back()
            ->with('error', 'Error updating General Job Sheet: ' . $e->getMessage())
            ->withInput();
    }
}



}
