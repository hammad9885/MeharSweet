<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\TRNDTL;
use App\Models\ErpParam;
use App\Models\OfficeCash;
use Illuminate\Http\Request;
use App\Models\AccountMaster;
use Carbon\Carbon;

class OfficeCashController extends Controller
{
    public function index()
    {
        $erpParams = ErpParam::with('level2')->get();
        $accountMasters = collect();
        if ($erpParams->isNotEmpty()) {
            $cashLevelId = $erpParams->first()->cash_level;
            $accountMasters = AccountMaster::where('level2_id', $cashLevelId)->get();
        }
        $accounts = AccountMaster::all();
        
        return \Inertia\Inertia::render('Accounts/Vouchers/VoucherEntry', [
            'title' => 'Office Cash Voucher (OC)',
            'vType' => 'OC',
            'isPayment' => true,
            'accounts' => $accounts,
            'accountMasters' => $accountMasters,
            'storeRoute' => '/office_cash',
            'reportsRoute' => '/office_cash/reports',
        ]);
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
           
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $filePath = $file->storeAs('uploads', $fileName, 'public');
        }


$lastEntry = TRNDTL::where('v_type', 'OC')
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
    'v_no' => $newInvoiceNumber,
    'v_type' => 'OC',
    'preparedby' => auth()->user()->name, // Get the user's name instead of ID
    'date' => now(), // Using Carbon's now()
    'cash_id' => $entry['cash'],
    'account_id' => $entry['account'],
    'description' => $entry['description'],
    'debit' => $entry['debit'],
    'credit' => '0',
    'status' => 'unofficial',
    'file_id' => $filePath,
]);
        }

        return redirect()->route('office_cash.reports')->with('success', '' . $request->v_type . '-' . $newInvoiceNumber . ' has been saved successfully.');
    }


    public function reports(Request $request)
{
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $status = $request->input('status');
    $v_no = $request->input('v_no');
    $account_id = $request->input('account_id');

    $query = TRNDTL::where('v_type', 'OC')->with('accounts');

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
    
   $trndtls = $query->orderBy('date', 'desc')  // First sort by date
                 ->orderBy('v_no', 'desc')  // Then sort by v_no
                 ->get();



    $accountMasters = AccountMaster::all();
       $vNoList = TRNDTL::where('v_type', 'OC')->pluck('v_no')->unique()->toArray();
     $accountIdList = TRNDTL::where('v_type', 'OC')->pluck('account_id')->unique()->toArray();

    return \Inertia\Inertia::render('Accounts/Vouchers/VoucherReports', [
        'title' => 'Office Cash Vouchers (OC)',
        'vType' => 'OC',
        'trndtls' => $trndtls,
        'accountMasters' => $accountMasters,
        'vNoList' => $vNoList,
        'accountIdList' => $accountIdList,
        'createRoute' => '/office_cash',
        'editRoutePrefix' => '/office_cash',
        'deleteRoutePrefix' => '/office_cash-delete',
        'filters' => [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'status' => $status,
        ],
    ]);
}


public function edit($v_no)
{
    $voucher = TRNDTL::where('v_no', $v_no)
                ->where('v_type', 'OC')
                ->get(); 
                
    $accounts = AccountMaster::whereIn('level2_id', [17, 10, 11, 13, 15, 19, 4 ,18, 8, 23])->get();

    $erpParams = ErpParam::with('level2')->get();

    $accountMasters = collect();

    if ($erpParams->isNotEmpty()) {
        $cashLevelId = $erpParams->first()->cash_level;
        $accountMasters = AccountMaster::where('level2_id', $cashLevelId)->get();
    }

    return \Inertia\Inertia::render('Accounts/Vouchers/VoucherEntry', [
        'title' => 'Edit Office Cash Voucher (OC)',
        'vType' => 'OC',
        'isPayment' => true,
        'voucher' => $voucher,
        'v_no' => $v_no,
        'accounts' => $accounts,
        'accountMasters' => $accountMasters,
        'updateRoute' => '/office_cash/' . $v_no . '/update',
        'reportsRoute' => '/office_cash/reports',
    ]);
}


public function update(Request $request, $id)
{
     $entries = $request->input('entries', []);

    $fileRecord = null;
    if ($request->hasFile('files')) {
        $file = $request->file('files');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = 'uploads/' . $fileName;
        $file->storeAs('uploads', $fileName, 'public');
        $fileRecord = File::create(['files' => $filePath]);
    }

   
    $trndtl = TRNDTL::where('v_no', $id)
                ->where('v_type', 'OC') 
                ->firstOrFail();


foreach ($entries as $entry) {
            $filePath = null;

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
                $filePath = $file->storeAs('uploads', $fileName, 'public');
            }

        TRNDTL::create([
            'v_no' => $trndtl->v_no,
            'v_type' => $request->v_type,
            'preparedby' => auth()->user()->name, 
    'date' => now(), // Using Carbon's now()
            'cash_id' => $entry['cash'],
            'account_id' => $entry['account'],
            'description' => $entry['description'],
            'debit' => $entry['debit'],
            'status' => 'unofficial',
            'credit' => '0',
            'file_id' => $filePath,
        ]);
    }

    return back();
}


public function destroy($id)
{
    $trndtl = TRNDTL::where('v_type', 'OC')
                    ->where('id', $id)
                    ->firstOrFail();

    $trndtl->delete();

    return redirect()->route('office_cash.reports')->with('success', 'Transaction deleted successfully!');
}

public function delete($id)
{
    $trndtl = TRNDTL::where('v_type', 'OC')
                    ->where('id', $id)
                    ->firstOrFail();

    $trndtl->delete();
    
    return redirect()->route('office_cash.reports')->with('success', 'Transaction deleted successfully!');
}
}
