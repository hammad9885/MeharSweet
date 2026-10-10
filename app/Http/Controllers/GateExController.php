<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\TRNDTL;
use App\Models\ErpParam;
use App\Models\GateEx;
use Illuminate\Http\Request;
use App\Models\AccountMaster;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class GateExController extends Controller
{
    public function index()
    {
        $erpParams = ErpParam::with('level2')->get();
        $accountMasters = AccountMaster::all();
        
        $accounts = AccountMaster::whereIn('level2_id', [ 13, 19, 18])->get();
        return view('gate_ex.list',get_defined_vars());
    }
    
    
 public function store(Request $request)
{
    $request->validate([
        'v_type' => 'required|string',
        'entries' => 'required|array',
        'entries.*.date' => 'required|date',
        'entries.*.cash' => 'required|string',
        'entries.*.account' => 'required|string',
        'entries.*.description' => 'required|string',
        'entries.*.file' => 'nullable|file|mimes:jpg,jpeg,png', // Add validation
    ]);

    DB::beginTransaction();
    try {
        $lastEntry = TRNDTL::where('v_type', 'GE')
            ->orderBy('id', 'desc')
            ->first();
        $newInvoiceNumber = ($lastEntry && is_numeric($lastEntry->v_no)) ? $lastEntry->v_no + 1 : 1;

        foreach ($request->entries as $index => $entry) {
            $filePath = null;
            
            if ($request->hasFile("entries.$index.file")) {
                $file = $request->file("entries.$index.file");
                $fileName = $newInvoiceNumber.'_'.time().'_'.preg_replace('/\s+/', '_', $file->getClientOriginalName());
                
                // Use the same method as your working function
                $file->move(public_path('storage/uploads'), $fileName);
                $filePath = 'uploads/' . $fileName;
                
                // Verify file exists
                if (!file_exists(public_path('storage/'.$filePath))) {
                    throw new \Exception("File storage failed: ".$fileName);
                }
            }

            TRNDTL::create([
                'v_no' => $newInvoiceNumber,
                'v_type' => 'GE',
                'preparedby' => auth()->user()->name,
                'date' => $entry['date'],
                'cash_id' => $entry['cash'],
                'account_id' => $entry['account'],
                'description' => $entry['description'],
                'debit' => $entry['debit'],
                'credit' => '0',
                'status' => 'unofficial',
                'file_id' => $filePath,
            ]);
        }

        DB::commit();
        return redirect()->route('gate_ex.reports')
            ->with('success', $request->v_type.'-'.$newInvoiceNumber.' saved successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->with('error', 'Error saving voucher: '.$e->getMessage());
    }
}


    public function reports(Request $request)
{
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $status = $request->input('status');
    $v_no = $request->input('v_no');
    $account_id = $request->input('account_id');

    $query = TRNDTL::where('v_type', 'GE')->with('accounts');

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
   $trndtls = $query->orderBy('date', 'desc')
                 ->orderBy('v_no', 'desc')
                 ->get();


    $accountMasters = AccountMaster::all();
       $vNoList = TRNDTL::where('v_type', 'GE')->pluck('v_no')->unique()->toArray();
     $accountIdList = TRNDTL::where('v_type', 'GE')->pluck('account_id')->unique()->toArray();

    return view('gate_ex.index', [
        'trndtls' => $trndtls,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'status' => $status, 
        'accountMasters' => $accountMasters,
          'vNoList' => $vNoList,
        'accountIdList' => $accountIdList,
    ]);
}


public function edit($v_no)
{
    $voucher = TRNDTL::where('v_no', $v_no)
                ->where('v_type', 'GE')
                ->get(); 
                
    $accounts = AccountMaster::whereIn('level2_id', [13, 19, 18])->get();

    $erpParams = ErpParam::with('level2')->get();

    $accountMasters = AccountMaster::all();

    return view('gate_ex.edit', compact('voucher', 'accountMasters', 'accounts'));
}


 public function update(Request $request, $id)
    {
        
        $request->validate([
            'v_type' => 'required|string',
            'entries' => 'required|array',
            'entries.*.date' => 'required|date',
            'entries.*.cash' => 'required|string',
            'entries.*.account' => 'required|string',
            'entries.*.description' => 'required|string',
           
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $filePath = $file->storeAs('uploads', $fileName, 'public');
        }


$lastEntry = TRNDTL::where('v_type', 'GE')
        ->orderBy('id', 'desc')
        ->first();

    if ($lastEntry && is_numeric($lastEntry->v_no)) {
        $lastInvoiceNumber = (int) $lastEntry->v_no;
    } else {
        $lastInvoiceNumber = 0;
    }

    $newInvoiceNumber = $lastInvoiceNumber + 1;



        foreach ($request->entries as $index => $entry) {
            TRNDTL::create([
                'v_no' => $id,
                'v_type' => 'GE',
                'preparedby' => auth()->user()->name, // Get the user's name instead of ID
                'date' => $entry['date'],
                'cash_id' => $entry['cash'],
                'account_id' => $entry['account'],
                'description' => $entry['description'],
                'debit' => $entry['debit'],
                'credit' => '0',
                'status' => 'unofficial',
                'file_id' => $filePath,
            ]);
        }

        return redirect()->route('gate_ex.reports')->with('success', '' . $request->v_type . '-' . $id . ' has been saved successfully.');
    }




public function destroy($id)
{
$trndtl = TRNDTL::where('id', $id)
->where('v_type', 'GE')
->firstOrFail();

$trndtl->delete();

return redirect()->route('gate_ex.reports')
->with('success', 'Transaction deleted successfully!');
}

public function delete($id)
{
$trndtl = TRNDTL::where('id', $id)
->where('v_type', 'GE')
->firstOrFail();

$trndtl->delete();

return redirect()->route('gate_ex.reports')
->with('success', 'Transaction deleted successfully!');
}



}
