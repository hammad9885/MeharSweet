<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;
use App\Models\TRNDTL;
use App\Models\AccountMaster;
use App\Models\ErpParam;
use App\Models\UserPermission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashBookController extends Controller
{

    public function index(Request $request)
    {
        $cid = Auth::user()->cid;
        $accountId = $request->get('account_title');
        $cashLevel=ErpParam::where('cid',$cid);
        if(!$cashLevel->exists() || !$cashLevel->first()->cashes->id){
            return redirect()->route('dashboard.admin')->with('error', 'Set up Level Accounts From ERP Parameters');
        }
        $cashLevel=$cashLevel->first();
        $accounts = AccountMaster::where('cid', $cid)->where('level2_id',$cashLevel->cashes->id)->get();

        // Default empty values
        $trndtls = collect();
        $openingBalance = 0;
        $totalDebit = 0;
        $totalCredit = 0;
        $closingBalance = 0;

        // Only run query if account is selected
        if ($accountId) {
            // Normalize start/end dates (the form requires them)
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');

            // Get transactions between start and end (inclusive)
            $query = TRNDTL::where(function ($q) use ($accountId) {
        $q->where('cash_id', $accountId)
        ->where('cid', auth()->user()->cid)
          ->orWhere('account_id', $accountId);
    })
    ->when($startDate, fn($q) => $q->whereDate('date', '>=', $startDate))
    ->when($endDate, fn($q) => $q->whereDate('date', '<=', $endDate))
    ->orderBy('date')
    ->orderBy('v_no');


            $trndtls = $query->get();
            // dd($trndtls);
            // Opening balance: all entries with date < start_date for this account
            if ($startDate) {
                $openingBalance = TRNDTL::where('cash_id', $accountId)
                    ->whereDate('date', '<', $startDate)
                    ->sum(DB::raw('debit - credit'));
            } else {
                // If no start date provided, opening balance remains 0
                $openingBalance = 0;
            }
            

            // Totals for the selected range: explicit DB sums to ensure all rows in the date range are counted
            $totalDebit = TRNDTL::where('cash_id', $accountId)
                ->when($startDate, fn($q) => $q->whereDate('date', '>=', $startDate))
                ->when($endDate, fn($q) => $q->whereDate('date', '<=', $endDate))
                ->sum('debit');

            $totalCredit = TRNDTL::where('cash_id', $accountId)
                ->when($startDate, fn($q) => $q->whereDate('date', '>=', $startDate))
                ->when($endDate, fn($q) => $q->whereDate('date', '<=', $endDate))
                ->sum('credit');

            $closingBalance = $openingBalance + ($totalDebit - $totalCredit);
        }

        return \Inertia\Inertia::render('Accounts/Ledger', [
            'trndtls' => $trndtls,
            'accounts' => $accounts,
            'openingBalance' => $openingBalance,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'closingBalance' => $closingBalance,
            'accountId' => $accountId,
        ]);
    }
}
