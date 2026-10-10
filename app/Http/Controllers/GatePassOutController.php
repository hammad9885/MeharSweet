<?php

namespace App\Http\Controllers;

use App\Models\ItemMaster;
use App\Models\TRNDTL;
use App\Models\ErpParam;
use Illuminate\Http\Request;
use App\Models\AccountMaster;
use App\Models\WastageSale;
use App\Models\DeliveryMaster;
use App\Models\GatePassOut;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GatePassOutController extends Controller
{


  public function index()
  {
    $loggedInUser = Auth::user();
    $accounts = AccountMaster::whereIn('level2_id', [4, 7,15,23])->get();

    $items = ItemMaster::all();

    $saleAccounts = AccountMaster::all();

    return view('sales.gate_pass_out.list', compact('loggedInUser', 'accounts', 'items', 'saleAccounts'));
  }



public function store(Request $request)
{
    // Validate the request
    $request->validate([
        'account' => 'required|exists:account_masters,id',
        'entries' => 'required|array',
        'entries.*.date' => 'required|date',
        'entries.*.qty' => 'required|numeric|min:0',
        'entries.*.rate' => 'required|numeric|min:0',
        'entries.*.description' => 'required|string',
        'entries.*.file' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
    ]);

    // Get the next invoice number
    $maxVNo = GatePassOut::max('v_no');
    $newInvoiceNumber = $maxVNo ? (int)$maxVNo + 1 : 1;
    $erpParam = ErpParam::first();
    $saleAccountId = $erpParam ? $erpParam->sale_ac : null;

    DB::beginTransaction();
    try {
        // Ensure upload directory exists
        if (!file_exists(public_path('storage/uploads'))) {
            mkdir(public_path('storage/uploads'), 0755, true);
        }

        foreach ($request->entries as $entry) {
            // Handle file upload - using the working approach
            $filePath = null;
            if (isset($entry['file']) && $entry['file']->isValid()) {
                $file = $entry['file'];
                $fileName = $newInvoiceNumber.'_'.time().'_'.preg_replace('/\s+/', '_', $file->getClientOriginalName());
                
                // Move file to public storage
                $file->move(public_path('storage/uploads'), $fileName);
                $filePath = 'uploads/' . $fileName;
                
                // Verify file was stored
                if (!file_exists(public_path('storage/'.$filePath))) {
                    throw new \Exception("Failed to store file: ".$fileName);
                }
            }

            // Calculate total
            $total = (float) $entry['qty'] * (float) $entry['rate'];

            // Create GatePassOut record
            $gatePassOut = GatePassOut::create([
                'v_no' => $newInvoiceNumber,
                'qty' => (float) $entry['qty'],
                'rate' => (float) $entry['rate'],
                'total' => $total,
                'file_path' => $filePath,
            ]);

            // Create TRNDTL record
            TRNDTL::create([
                'v_no' => $newInvoiceNumber,
                'date' => $entry['date'] ?? now(),
                'description' => $entry['description'] ?? '',
                'account_id' => $request->account,
                'cash_id' => $saleAccountId,
                'preparedby' => $entry['prepared_by'] ?? auth()->user()->name,
                'credit' => 0,
                'debit' => $total,
                'status' => 'unofficial',
                'v_type' => 'GPO',
                'r_id' => $gatePassOut->id,
            ]);
        }

        DB::commit();
        return redirect()
            ->route('gate_pass_out.reports')
            ->with('success', 'Voucher GPO-' . $newInvoiceNumber . ' has been saved successfully.');

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('GatePassOut Store Error: '.$e->getMessage(), [
            'exception' => $e,
            'request' => $request->all()
        ]);
        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Error saving voucher: ' . $e->getMessage());
    }
}



  public function reports(Request $request)
  {
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $status = $request->input('status');
     $v_no = $request->input('v_no');
$description = $request->input('description');
 $account_id = $request->input('account_id');

    $query = TRNDTL::where('v_type', 'GPO')
      ->with(['gatePassOut', 'accounts', 'cashes']);

    if ($startDate && $endDate) {
      $query->whereBetween('date', [$startDate, $endDate]);
    }

    if ($status) {
      $query->where('status', $status);
    }
    if ($request->filled('description')) {
        $query->where('description', 'LIKE', '%' . $description . '%');
    }
    
    if ($v_no) {
        $query->where('v_no', $v_no);
    }
     if ($account_id) {
        $query->where('account_id', $account_id);
    }
    
    
    $trndtl = $query->orderByRaw('CAST(date AS DATE) DESC')
        ->orderByRaw('CAST(v_no AS SIGNED) DESC')
        ->orderBy('id', 'desc')
        ->get();

    $accountMasters = AccountMaster::all();
     $vNoList = GatePassOut::pluck('v_no')->unique()->toArray();
     
       $accountId = AccountMaster::pluck('title', 'id');

    return view('sale_reports.index4', [
      'trndtl' => $trndtl,
      'startDate' => $startDate,
      'endDate' => $endDate,
      'status' => $status,
      'accountMasters' => $accountMasters,
      'vNoList' => $vNoList,
      'description' => $description,
              'accountId' => $accountId,
    ]);
  }
 
 public function destroy($id)
{
    $trndtl = TRNDTL::where('id', $id)->where('v_type', 'GPO')->first();

    if (!$trndtl) {
        return back()->with('error', 'Record not found or not of type GPO.');
    }

    if ($trndtl->r_id) {
        GatePassOut::where('id', $trndtl->r_id)->delete();
    }

    $trndtl->delete();

    return back()->with('success', 'Record deleted successfully.');
}

public function delete($id)
{
    $trndtl = TRNDTL::where('id', $id)->where('v_type', 'GPO')->first();

    if (!$trndtl) {
        return back()->with('error', 'Record not found or not of type GPO.');
    }

    if ($trndtl->r_id) {
        GatePassOut::where('id', $trndtl->r_id)->delete();
    }

    $trndtl->delete();

    return back()->with('success', 'Record deleted successfully.');
}


 
  public function edit($v_no)
  {
    $loggedInUser = Auth::user();

    $voucher = TRNDTL::where('v_no', $v_no)
      ->where('v_type', 'GPO')
      ->with(['accounts', 'cashes', 'gatepassout.items'])
      ->get();

    $erpParams = ErpParam::with('level2')->get();

    $accountMasters = AccountMaster::all();

    $saleAccounts = AccountMaster::all();

    if ($erpParams->isNotEmpty()) {
      $erpParam = $erpParams->first();

      if ($erpParam->sale_account) {
        $saleAccounts = AccountMaster::where('level2_id', $erpParam->sale_account)->get();
      }
    }

    $items = ItemMaster::all();

    return view('sale_reports.edit4', compact('v_no', 'loggedInUser', 'voucher', 'erpParams', 'accountMasters', 'saleAccounts', 'items'));
  }
 
  
  
  
  public function update(Request $request, $v_no)
{
    $erpParam = ErpParam::first();
    $saleAccountId = $erpParam ? $erpParam->sale_ac : null;

    if (!$saleAccountId) {
        return redirect()->back()->withErrors(['error' => 'Sale Account is not configured in ERP Params.']);
    }

    $partyAccountId = $request->account;

    $existingWastageSales = GatePassOut::where('v_no', $v_no)->get();

    if ($existingWastageSales->isEmpty()) {
        return redirect()->back()->withErrors(['error' => 'No records found for the provided v_no.']);
    }

    $preparedBy = Auth::user()->name;

    foreach ($request->entries as $entry) {
        $filePath = null;
        
        // Check if file exists and is an uploaded file
        if (!empty($entry['file']) && $entry['file'] instanceof \Illuminate\Http\UploadedFile) {
            if ($entry['file']->isValid()) {
                $filePath = $entry['file']->store('public/uploads');
                $filePath = str_replace('public/', '', $filePath);
            }
        }
        
        $gatePassOut = GatePassOut::create([
            'qty' => $entry['qty'] ?? 0,
            'rate' => $entry['rate'] ?? 0,
            'total' => ($entry['qty'] * $entry['rate']) ?? 0,
            'v_no' => $v_no,
            'file_path' => $filePath,
        ]);

        TRNDTL::create([
         
            
             'v_no' => $v_no,
        'date' => $entry['date'] ?? null,
        'description' => $entry['description'] ?? '',
        'account_id' => $partyAccountId,
        'cash_id' => $saleAccountId,
        'preparedby' => $preparedBy,
        'credit' => 0,
        'debit' => $gatePassOut->total ?? 0,
        'status' => 'unofficial',
        'v_type' => 'GPO',
        'r_id' => $gatePassOut->id,
        ]);
    }
    return redirect()->route('gate_pass_out.reports')->with('success', 'Voucher GPO has been saved successfully.');
}

}
