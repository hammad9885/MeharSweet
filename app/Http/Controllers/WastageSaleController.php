<?php

namespace App\Http\Controllers;

use App\Models\ItemMaster;
use App\Models\TRNDTL;
use App\Models\ErpParam;
use Illuminate\Http\Request;
use App\Models\AccountMaster;
use App\Models\WastageSale;
use App\Models\DeliveryMaster;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WastageSaleController extends Controller
{


    public function index()
    {
        $loggedInUser = Auth::user();
        $accounts = AccountMaster::where('level2_id',7)->get(); // All accounts for dropdowns
        $items = ItemMaster::all(); // Items for the list

        // Fetch sale accounts for dropdown (assuming all accounts are suitable)
        $saleAccounts = AccountMaster::all();

        return view('sales.wastage_sale.list', compact('loggedInUser', 'accounts', 'items', 'saleAccounts'));
    }
public function store(Request $request)
{
    // Validate the request
    $request->validate([
        'entries' => 'required|array',
        'entries.*.date' => 'required|date',
        'entries.*.supplier' => 'required|exists:account_masters,id',
        'entries.*.item' => 'required|exists:item_masters,id',
        'entries.*.description' => 'nullable|string',
       
        'entries.*.file' => 'nullable|file|mimes:jpg,jpeg,png|max:2048', // Validate file uploads
    ]);

    DB::beginTransaction();
    try {
        $lastEntry = WastageSale::orderBy('id', 'desc')->first();
        $lastInvoiceNumber = $lastEntry ? (int) $lastEntry->v_no : 0;
        $newInvoiceNumber = $lastInvoiceNumber + 1;

        // Get sale account ID from ERP parameters
        $erpParam = ErpParam::first();
        $saleAccountId = $erpParam ? $erpParam->sale_ac : null;

        if (!$saleAccountId) {
            return response()->json(['error' => 'Sale Account is not configured in ERP Params.'], 400);
        }

        $partyAccountId = $request->account;

        foreach ($request->entries as $key => $entry) {
            // Handle file upload
            $filePath = null;
            if ($request->hasFile("entries.{$key}.file")) {
                $file = $request->file("entries.{$key}.file");
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('storage/uploads'), $fileName);
                $filePath = 'uploads/' . $fileName;
            }

            // Create WastageSale record
            $wastageSale = WastageSale::create([
                'item_code' => $entry['item'],
                'weight' => $entry['weight'],
                'rate' => $entry['rate'],
                'total' => $entry['amount'],
                'v_no' => $newInvoiceNumber,
                'file_path' => $filePath, // Save the full path
            ]);

            // Create TRNDTL record
            TRNDTL::create([
                'v_no' => $newInvoiceNumber,
                'date' => $entry['date'],
                'description' => $entry['description'] ?? '',
                'account_id' => $partyAccountId,
                'cash_id' => $saleAccountId,
                'preparedby' => auth()->user()->name ?? null,
                'credit' => 0,
                'debit' => $entry['amount'],
                'status' => 'unofficial',
                'v_type' => 'WSN',
                'r_id' => $wastageSale->id,
            ]);
        }

        DB::commit();
        return redirect()->route('wastage_sale.reports')->with('success', 'Voucher No. ' . $newInvoiceNumber . ' has been saved successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'An error occurred while saving the voucher: ' . $e->getMessage());
    }
}

public function reports(Request $request)
{
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $status = $request->input('status');
    $itemCode = $request->input('item_code'); // Correct variable name to 'item_code'
    $v_no = $request->input('v_no');
    
    // Build the query
    $query = TRNDTL::where('v_type', 'WSN')
        ->with(['wastagesales', 'accounts', 'cashes']); // Added 'cashes' for sale account

    if ($startDate && $endDate) {
        $query->whereBetween('date', [$startDate, $endDate]);
    }

    if ($status) {
        $query->where('status', $status);
    }

    if ($v_no) {
        $query->where('v_no', $v_no);
    }
    // Apply item code filter if selected
    if ($itemCode) {
        $query->whereHas('wastagesales.items', function ($q) use ($itemCode) {
            $q->where('item_code', $itemCode);
        });
    }


    $trndtl = $query->orderBy('date', 'desc')
        ->orderBy('id', 'desc')
        ->orderBy('v_no', 'desc')
        ->get();

  
    $accountMasters = AccountMaster::all();
    $vNoList = WastageSale::pluck('v_no')->unique()->toArray();

    return view('sale_reports.index2', [
        'trndtl' => $trndtl,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'status' => $status,
        'item_code' => $itemCode, // Pass item code to view
        'accountMasters' => $accountMasters,
        'vNoList' => $vNoList,
    ]);
}


    public function destroy($id)
{
    // Find the TRNDTL record with v_type = 'WSN'
    $trndtl = TRNDTL::where('id', $id)->where('v_type', 'WSN')->firstOrFail();

    // Delete associated WastageSale if it exists
    if ($trndtl->r_id) {
        WastageSale::where('id', $trndtl->r_id)->delete();
    }

    // Delete the TRNDTL record
    $trndtl->delete();

    return back()->with('success', 'Record deleted successfully.');
}

public function delete($id)
{
    // Find the TRNDTL record with v_type = 'WSN'
    $trndtl = TRNDTL::where('id', $id)->where('v_type', 'WSN')->firstOrFail();

    // Delete associated WastageSale if it exists
    if ($trndtl->r_id) {
        WastageSale::where('id', $trndtl->r_id)->delete();
    }

    // Delete the TRNDTL record
    $trndtl->delete();

    return back()->with('success', 'Record deleted successfully.');
}

    public function edit($v_no)
{
    // Get the logged-in user
    $loggedInUser = Auth::user();

    // Find the voucher by voucher number (v_no) and type (WSN for Wastage Sale)
    $voucher = TRNDTL::where('v_no', $v_no)
        ->where('v_type', 'WSN')
        ->with(['accounts', 'cashes', 'wastagesales.items']) // Eager load related data
        ->get();

    // Fetch ERP parameters and related levels
    $erpParams = ErpParam::with('level2')->get();

    // Fetch all account masters for parties
    $accountMasters = AccountMaster::where('level2_id', 7)->get();// Show all accounts for Party selection

    // Initialize saleAccounts
    $saleAccounts = AccountMaster::all(); // Default to all sale accounts

    // Check if ERP parameters are available
    if ($erpParams->isNotEmpty()) {
        $erpParam = $erpParams->first(); // Get the first ERP parameter

        // If sale_account is set, filter sale accounts by it
        if ($erpParam->sale_account) {
            $saleAccounts = AccountMaster::where('level2_id', $erpParam->sale_account)->get();
        }
    }

    // Fetch all items for the view
    $items = ItemMaster::all();

    // Pass all data to the view
    return view('sale_reports.edit2', compact('v_no', 'loggedInUser', 'voucher', 'erpParams', 'accountMasters', 'saleAccounts', 'items'));
}




public function update(Request $request, $v_no)
{
    // Validate the request
    $request->validate([
        'entries' => 'required|array',
        'entries.*.date' => 'required|date',
        'entries.*.supplier' => 'required|exists:account_masters,id',
        'entries.*.item' => 'required|exists:item_masters,id',
        'entries.*.description' => 'nullable|string',
        'entries.*.weight' => 'required|numeric|min:0',
        'entries.*.rate' => 'required|numeric|min:0',
        'entries.*.amount' => 'required|numeric|min:0',
        'entries.*.file' => 'nullable|file|mimes:jpg,jpeg,png|max:2048', // Validate file uploads
    ]);

    DB::beginTransaction();
    try {
        
        // Get sale account ID from ERP parameters
        $erpParam = ErpParam::first();
        $saleAccountId = $erpParam ? $erpParam->sale_ac : null;

        if (!$saleAccountId) {
            return response()->json(['error' => 'Sale Account is not configured in ERP Params.'], 400);
        }

        $partyAccountId = $request->account;

        foreach ($request->entries as $key => $entry) {
            // Handle file upload
            $filePath = null;
            if ($request->hasFile("entries.{$key}.file")) {
                $file = $request->file("entries.{$key}.file");
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('storage/uploads'), $fileName);
                $filePath = 'uploads/' . $fileName;
            }

            // Create WastageSale record
            $wastageSale = WastageSale::create([
                'item_code' => $entry['item'],
                'weight' => $entry['weight'],
                'rate' => $entry['rate'],
                'total' => $entry['amount'],
                'v_no' => $v_no,
                'file_path' => $filePath, // Save the full path
            ]);

            // Create TRNDTL record
            TRNDTL::create([
                'v_no' => $v_no,
                'date' => $entry['date'],
                'description' => $entry['description'] ?? '',
                'account_id' => $partyAccountId,
                'cash_id' => $saleAccountId,
                'preparedby' => auth()->user()->name ?? null,
                'credit' => 0,
                'debit' => $entry['amount'],
                'status' => 'unofficial',
                'v_type' => 'WSN',
                'r_id' => $wastageSale->id,
            ]);
        }

        DB::commit();
        return redirect()->route('wastage_sale.reports')->with('success', 'Voucher No. ' . $v_no . ' has been saved successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'An error occurred while saving the voucher: ' . $e->getMessage());
    }
}


    
}
