<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\TRNDTL;
use Illuminate\Http\Request;
use App\Models\AccountMaster;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BankController extends Controller
{
    public function index()
    {
        $cid = Auth::user()->fk_cid ?? Auth::user()->cid;
        $accounts = AccountMaster::where('cid', $cid)->get();
        return \Inertia\Inertia::render('Accounts/Vouchers/JournalVoucherEntry', [
            'title' => 'Bank Opening Balance Entry',
            'accounts' => $accounts,
            'storeRoute' => '/bank',
            'reportsRoute' => '/journal_voucher/reports',
        ]);
    }
    public function store(Request $request)
    {
          $cid = Auth::User()->fk_cid;
        if ($request->total_debit != $request->total_credit) {
            return redirect()->back()->with('error', 'Total debit and credit amounts must be equal.');
        }
       
foreach ($request->entry_account_title as $index => $accountTitle) {
    
    $debitEntry = $request->entry_debit[$index] ?? 0;
    $creditEntry = $request->entry_credit[$index] ?? 0;
    $entryDate = $request->entry_date[$index]; 

    $maxVoucherNo = TRNDTL::where('v_type', 'OPN')->where('cid', $cid)->max('v_no');
    if($maxVoucherNo){
        $maxVoucherNo = $maxVoucherNo + 1;
    }else{
        $maxVoucherNo = 1;
    }
    $voucher = TRNDTL::create([
        'v_no' => $maxVoucherNo,
        'account_id' => $accountTitle,
        'debit' => $debitEntry,
        'credit' => $creditEntry,
        'date' => $entryDate, 
        'cid' => $cid,
        'v_type' => 'OPN',
    ]);

}
        foreach ($request->entry_account_title as $index => $accountTitle) {
         
            $debitEntry = $request->entry_debit[$index] ?? 0;
            $creditEntry = $request->entry_credit[$index] ?? 0;
            $entryDate = $request->entry_date[$index]; 
           
            $voucher = Bank::create([
                'account_id' => $accountTitle,
                'debit' => $debitEntry,
                'credit' => $creditEntry,
                'date' => $entryDate, 
                'cid'=> $cid,
            ]);
        }

        return redirect()->back()->with('success', 'OPN-1 has been saved successfully');
    }




}
