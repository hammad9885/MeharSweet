<?php

namespace App\Http\Controllers;

use App\Models\ItemMaster;
use App\Models\TRNDTL;
use App\Models\ErpParam;
use Illuminate\Http\Request;
use App\Models\AccountMaster;
use App\Models\WastageSale;
use App\Models\ChequeMaster;
use App\Models\DeliveryMaster;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ChequeReceiptsController extends Controller
{

    public function index()
    {
        $loggedInUser = Auth::user();
        $accounts = AccountMaster::all();
        $banks = AccountMaster::all();
        $items = ItemMaster::all();
        $saleAccounts = AccountMaster::all();
        return view('cheque.index', compact('loggedInUser', 'accounts', 'items', 'saleAccounts', 'banks'));
    }

    public function store(Request $request)
{
    $lastInvoiceNumber = ChequeMaster::max('v_no') ?? 0;
    $newInvoiceNumber = $lastInvoiceNumber + 1;

    $chq = ChequeMaster::create([
        'chq_status'   => $request->input('chq_status'), 
        'chq_no'       => $request->input('chq_no'), 
        'chq_date'     => $request->input('chq_date'), 
        'description'  => $request->input('description'), 
        'chq_amt'      => $request->input('chq_amt', 0),     
        'v_no'         => $newInvoiceNumber,                 
        'v_type'       => 'CHR',                
        'aid'          => $request->input('account'),             
        'prepared_by'  => $request->input('prepared_by'), 
        'bank'  => $request->input('bank'), 
    ]);

    // Check if 'chq_status' is 'Completed'
    if ($request->input('chq_status') === 'Completed') {
        $trndtl = TRNDTL::create([
            'v_no'        => $newInvoiceNumber,
            'date'        => Carbon::now(),
            'preparedby'  => $request->input('prepared_by'),
            'account_id'  => $request->input('bank'), 
            'cash_id'     => $request->input('account'), 
            'debit'       => $request->input('chq_amt', 0),  
            'status'      => 'unofficial',
            'credit'      => 0,
            'v_type'      => 'CHR',
            'r_id'        => $chq->id,
            'description' => 'Completed Cheque',
        ]);
    }

    return redirect()->route('cheque_receipts.reports')->with('success', 'Voucher CHR-' . $newInvoiceNumber . ' has been saved successfully.');
}


   
   public function destroy($id)
{
    // Find the ChequeMaster record by ID
    $chequeReceipt = ChequeMaster::find($id);

    // Check if the record exists
    if ($chequeReceipt) {
        // Delete related TRNDTL record where v_type == 'CHR' and r_id == $id
        TRNDTL::where('v_type', 'CHR')->where('r_id', $id)->delete();

        // Delete the ChequeMaster record
        $chequeReceipt->delete();

        // Redirect back with success message
        return redirect()->back()->with('success', 'Cheque Receipt deleted successfully.');
    } else {
        // If the record does not exist, return an error
        return redirect()->back()->withErrors(['error' => 'Cheque Receipt not found.']);
    }
}

public function del($id)
{
    // Find the ChequeMaster record by ID
    $chequeReceipt = ChequeMaster::find($id);

    // Check if the record exists
    if ($chequeReceipt) {
        // Delete related TRNDTL record where v_type == 'CHR' and r_id == $id
        TRNDTL::where('v_type', 'CHR')->where('r_id', $id)->delete();

        // Delete the ChequeMaster record
        $chequeReceipt->delete();

        // Redirect back with success message
        return redirect()->back()->with('success', 'Cheque Receipt  deleted successfully.');
    } else {
        // If the record does not exist, return an error
        return redirect()->back()->withErrors(['error' => 'Cheque Receipt not found.']);
    }
}


   public function edit($v_no)
{
    $loggedInUser = Auth::user();
    $accountMasters = AccountMaster::all();
    $banks = AccountMaster::all();
    $voucher = ChequeMaster::where('v_no', $v_no)->get();
    return view('cheque.edit', compact('v_no', 'loggedInUser', 'voucher', 'accountMasters', 'banks'));
}

public function reports(Request $request)
{
    // Start building the query
    $query = ChequeMaster::where('v_type', 'CHR');

    // Apply date filter if dates are provided
    if ($request->filled('start_date') && $request->filled('end_date')) {
        $query->whereBetween('chq_date', [$request->input('start_date'), $request->input('end_date')]);
    }

    // Apply additional filters
    if ($request->filled('v_no')) {
        $query->where('v_no', $request->input('v_no'));
    }

    if ($request->filled('chq_status')) {
        $query->where('chq_status', $request->input('chq_status'));
    }

    if ($request->filled('bank')) {
        $query->where('bank', $request->input('bank'));
    }

    // Order by v_no descending to get max v_no at top
    $query->orderBy('v_no', 'desc');

    // Execute the query
    $cheques = $query->get();

    // Get filter options (for dropdowns)
    $vNoList = ChequeMaster::where('v_type', 'CHR')->pluck('v_no')->unique()->toArray();
    $accountIdList = ChequeMaster::where('v_type', 'CHR')->pluck('bank')->unique()->toArray();
    $chqStatusList = ChequeMaster::where('v_type', 'CHR')->pluck('chq_status')->unique()->toArray();

    // Pass data to the view
    return view('cheque.list', [
        'cheques' => $cheques,
        'startDate' => $request->input('start_date'),
        'endDate' => $request->input('end_date'),
        'vNoList' => $vNoList,
        'accountIdList' => $accountIdList,
        'chqStatusList' => $chqStatusList,
    ]);
}



public function update(Request $request, $v_no)
{
    // Fetch the first ChequeMaster record based on v_no
    $chequeMaster = ChequeMaster::where('v_no', $v_no)->first();

    // If no record is found
    if (!$chequeMaster) {
        return redirect()->back()->withErrors(['error' => 'No records found for the provided v_no.']);
    }

    // Generate new invoice number if needed
    $newInvoiceNumber = $v_no; // Replace with logic if new invoice number generation is needed

    // Update the chequeMaster record
    $chequeMaster->update([
        'chq_status'   => $request->input('chq_status'), 
        'chq_no'       => $request->input('chq_no'), 
        'chq_date'     => $request->input('chq_date'), 
        'description'  => $request->input('description'), 
        'chq_amt'      => $request->input('chq_amt', 0),     
        'v_no'         => $newInvoiceNumber,                 
        'v_type'       => 'CHR',                
        'aid'          => $request->input('account'),             
        'prepared_by'  => $request->input('prepared_by'), 
        'bank'         => $request->input('bank'), 
    ]);

    // Check if 'chq_status' is 'Completed'
    if ($request->input('chq_status') === 'Completed') {
        // Find existing TRNDTL record for this cheque
        $trndtl = TRNDTL::where('r_id', $chequeMaster->id)->where('v_type', 'CHR')->first();
        
        if ($trndtl) {
            // Update existing record
            $trndtl->update([
                'v_no'        => $newInvoiceNumber,
                'date'        => $request->input('chq_date'), 
                'preparedby'  => $request->input('prepared_by'),
                'account_id'  => $request->input('bank'), 
                'cash_id'     => $request->input('account'), 
                'debit'       => $request->input('chq_amt', 0),  
                'status'      => 'unofficial',
                'credit'      => 0,
                'v_type'      => 'CHR',
                'description' => 'Completed Cheque',
            ]);
        } else {
            // Create new record if none exists
            TRNDTL::create([
                'v_no'        => $newInvoiceNumber,
                'date'        => $request->input('chq_date'), 
                'preparedby'  => $request->input('prepared_by'),
                'account_id'  => $request->input('bank'), 
                'cash_id'     => $request->input('account'), 
                'debit'       => $request->input('chq_amt', 0),  
                'status'      => 'unofficial',
                'credit'      => 0,
                'v_type'      => 'CHR',
                'r_id'        => $chequeMaster->id,
                'description' => 'Completed Cheque',
            ]);
        }
    }

    // Check if 'chq_status' is changed to 'Pending' or 'Dishonor' and remove related TRNDTL entries
    if (in_array($request->input('chq_status'), ['Pending', 'Dishonor'])) {
        TRNDTL::where('r_id', $chequeMaster->id)->where('v_type','CHR')->delete();
    }

    return redirect()->route('cheque_receipts.reports')->with('success', 'Cheque Receipt updated successfully.');
}



}
