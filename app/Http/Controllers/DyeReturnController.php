<?php

namespace App\Http\Controllers;

use App\Models\ItemMaster;
use App\Models\TRNDTL;
use App\Models\ErpParam;
use Illuminate\Http\Request;
use App\Models\AccountMaster;
use App\Models\DyeReturn;
use App\Models\DeliveryMaster;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DyeReturnController extends Controller
{


    public function index()
    {
        $loggedInUser = Auth::user();
        $accounts = AccountMaster::whereIn('level2_id', [4, 23])->get();
        $saleAccounts = AccountMaster::all();
         $items = ItemMaster::all();
        return view('dye_return.list', compact('loggedInUser', 'items', 'accounts', 'saleAccounts'));
    }
    
    
public function store(Request $request)
{
    // Validate the request data
    $request->validate([
        'entries' => 'required|array',
        'entries.*.date' => 'required|date',
        'entries.*.account' => 'required|exists:account_masters,id',
        'entries.*.item' => 'required|exists:item_masters,id',
        'entries.*.description' => 'nullable|string',
        'entries.*.amount' => 'required|numeric|min:0',
        'entries.*.qty' => 'required|numeric|min:0',
        'entries.*.file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Added PDF support
    ]);

    DB::beginTransaction();
    try {
        // Get the last voucher number
        $lastEntry = DyeReturn::orderBy('v_no', 'desc')->first();
        $newInvoiceNumber = $lastEntry ? (int)$lastEntry->v_no + 1 : 1;

        // Get ERP parameters
        $erpParam = ErpParam::first();
        if (!$erpParam || !$erpParam->purchase_return_account) {
            return redirect()->back()->with('error', 'Cash Account is not configured in ERP Params.');
        }
        $cashAccountId = $erpParam->purchase_return_account;

        // Create uploads directory if it doesn't exist
        $uploadPath = public_path('storage/uploads');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // Process each entry
        foreach ($request->entries as $key => $entry) {
            // Handle file upload if exists
            $filePath = null;
            $fileName = null;
            
            if ($request->hasFile("entries.$key.file")) {
                $file = $request->file("entries.$key.file");
                $fileName = 'voucher_' . $newInvoiceNumber . '_' . time() . '_' . $file->getClientOriginalName();
                
                // Move file to public storage
                $file->move($uploadPath, $fileName);
                
                // Store relative path for database
                $filePath = 'uploads/' . $fileName;
            }

            // Create DyeReturn record
            $DyeReturn = DyeReturn::create([
                'v_no' => $newInvoiceNumber,
                'amount' => $entry['amount'],
                'qty' => $entry['qty'],
                'item_code' => $entry['item'],
                'description' => $entry['description'] ?? null,
                'file_path' => $filePath,
                'file_name' => $fileName, // Store original filename
            ]);

            // Create TRNDTL record
            TRNDTL::create([
                'v_no' => $newInvoiceNumber,
                'date' => $entry['date'],
                'description' => $entry['description'] ?? null,
                'preparedby' => $entry['prepared_by'] ?? auth()->user()->name,
                'account_id' => $entry['account'],
                'cash_id' => $cashAccountId,
                'credit' => 0,
                'debit' => $entry['amount'],
                'status' => 'unofficial',
                'v_type' => 'Dye-Return',
                'r_id' => $DyeReturn->id,
            ]);
        }

        DB::commit();
        return redirect()->route('dye_return.reports')->with([
            'success' => 'Voucher No. ' . $newInvoiceNumber . ' has been saved successfully.',
            'voucher_no' => $newInvoiceNumber // Pass voucher number for reference
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        // Log the error for debugging
        \Log::error('Dye Purchase Store Error: ' . $e->getMessage());
        return redirect()->back()->with('error', 'An error occurred while saving the voucher: ' . $e->getMessage());
    }
}


   public function reports(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status'); // New status filter
        
          $v_no = $request->input('v_no');
    $account_id = $request->input('account_id');

        // Build the query with date range and status filters
        $query = TRNDTL::where('v_type', 'Dye-Return')->where('credit', 0)->where('account_id', '!=', 35)->with('dyereturns');

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
    $vNo = TRNDTL::where('v_type', 'Dye-Return')->pluck('v_no')->unique()->toArray();
    $accountId = AccountMaster::whereIn('id', TRNDTL::where('v_type', 'Dye-Return')->pluck('account_id'))
    ->where('title', '!=', 'Purchase Freight') 
    ->pluck('title', 'id');
    
        return view('dye_return.index', [
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
    try {
      DB::beginTransaction();

      $DyeReturn = DyeReturn::findOrFail($id);

      TRNDTL::where('v_type', 'Dye-Return')
        ->where('r_id', $DyeReturn->id)
        ->delete();

      // Delete the PlateReturn record
      $DyeReturn->delete();

      DB::commit();

      return redirect()->route('dye_return.reports')
        ->with('success', 'Dye Return record deleted successfully.');
    } catch (\Exception $e) {
      DB::rollBack();
      return redirect()->route('dye_return.reports')
        ->with('error', 'Failed to delete record: ' . $e->getMessage());
    }
  }

    
}