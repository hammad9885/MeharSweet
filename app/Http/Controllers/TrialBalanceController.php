<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TRNDTL;
use App\Models\AccountMaster;
use App\Models\Module;
use App\Models\UserPermission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
class TrialBalanceController extends Controller
{
 public function index(Request $request)
{
    if (!$request->filled('start_date') || !$request->filled('end_date')) {
        return \Inertia\Inertia::render('Accounts/TrialBalance', [
            'trialBalance' => [],
            'startDate'    => null,
            'endDate'      => null,
        ]);
    }

    $cid = auth()->user()->cid;

    $startDate = $request->get('start_date');
    $endDate   = $request->get('end_date', date('Y-m-d'));

    /* ----------------------------------------------------
       1️⃣ OPENING BALANCE (before start date)
    ---------------------------------------------------- */
    $opening = DB::table('trn_dtl_views')
        ->where('cid', $cid)
        ->when($startDate, fn($q) => $q->where('v_date', '<', $startDate))
        ->groupBy('aid')
        ->select(
            'aid',
            DB::raw('SUM(debit) as opening_debit'),
            DB::raw('SUM(credit) as opening_credit')
        )
        ->get()
        ->keyBy('aid');

    /* ----------------------------------------------------
       2️⃣ TRANSACTIONS (within date range)
    ---------------------------------------------------- */
    $transactions = DB::table('trn_dtl_views')
        ->where('cid', $cid)
        ->when($startDate, fn($q) => $q->where('v_date', '>=', $startDate))
        ->when($endDate,   fn($q) => $q->where('v_date', '<=', $endDate))
        ->groupBy('aid')
        ->select(
            'aid',
            DB::raw('SUM(debit) as trans_debit'),
            DB::raw('SUM(credit) as trans_credit')
        )
        ->get()
        ->keyBy('aid');

    /* ----------------------------------------------------
       3️⃣ Collect ALL affected account IDs
    ---------------------------------------------------- */
    $allAccountIds = $opening->keys()
        ->merge($transactions->keys())
        ->unique()
        ->values();

    /* ----------------------------------------------------
       4️⃣ Build Final Trial Balance Rows
    ---------------------------------------------------- */
    $trialBalance = $allAccountIds->map(function ($aid) use ($opening, $transactions) {

        $acc = AccountMaster::find($aid);

        if (!$acc) return null; // prevents crash if deleted account exists

        $op = $opening[$aid] ?? (object) ['opening_debit' => 0, 'opening_credit' => 0];
        $tr = $transactions[$aid] ?? (object) ['trans_debit' => 0, 'trans_credit' => 0];

        /* --------------------------
           Opening (final Debit/Cr)
        --------------------------- */
        $openingNet = $op->opening_debit - $op->opening_credit;

        $openingDebit  = $openingNet > 0 ? $openingNet : 0;
        $openingCredit = $openingNet < 0 ? abs($openingNet) : 0;

        /* --------------------------
           Transaction
        --------------------------- */
        $transDebit  = $tr->trans_debit;
        $transCredit = $tr->trans_credit;

        /* --------------------------
           Closing = (Opening + Transactions)
        --------------------------- */
        $closingNet = ($openingDebit + $transDebit) - ($openingCredit + $transCredit);

        $closingDebit  = $closingNet > 0 ? $closingNet : 0;
        $closingCredit = $closingNet < 0 ? abs($closingNet) : 0;

        return [
            'account_id'    => $aid,
            'account_code'  => $acc->getFullCodeAttribute() ?? '',
            'account_title' => $acc->title ?? '',

            'opening_debit'  => $openingDebit,
            'opening_credit' => $openingCredit,

            'trans_debit'  => $transDebit,
            'trans_credit' => $transCredit,

            'closing_debit'  => $closingDebit,
            'closing_credit' => $closingCredit,
        ];
    })
    ->filter()        // remove nulls
    ->sortBy('account_code') // alphabetically
    ->values();

    return \Inertia\Inertia::render('Accounts/TrialBalance', [
        'trialBalance' => $trialBalance,
        'startDate'    => $startDate,
        'endDate'      => $endDate,
    ]);
}

  public function summary(Request $request)
{
    // When no end date is provided → return empty view
   if (!$request->filled('end_date')) {
    return view('trialbalance.summary', [
        'balances' => collect(), // ✅ empty collection
        'endDate'  => null,
        'totalDebit' => 0,
        'totalCredit' => 0,
    ]);
}

    $endDate = $request->get('end_date');
    $user = auth()->user();
    $cid = $user->cid;

    // Check module
    $module = Module::where('name', 'Trial Balance')->first();
    if (!$module) {
        return redirect()->back()->with('error', 'Module "Trial Balance" not found.');
    }

    // Check permission
    $permission = UserPermission::where('module_id', $module->id)
        ->where('cid', $cid)
        ->where('userid', $user->id)
        ->where('read', 1)
        ->exists();

    if (!$permission) {
        return redirect()->back()->with('error', 'You do not have permission. Please contact your admin.');
    }

    // Get all accounts for company
    $accounts = AccountMaster::where('cid', $cid)->get();

    // Build balances safely
    $balances = $accounts->map(function ($account) use ($endDate, $cid) {

        $totals = DB::table('trn_dtl_views')
    ->where('cid', $cid)
    ->where('aid', $account->id)
    ->selectRaw('
        SUM(debit) AS total_debit,
        SUM(credit) AS total_credit
    ')
    ->first();
      

        return (object)[
            'account_id'   => $account->id,
            'title'        => $account->title,
            'account_code' => $account->getFullCodeAttribute() ?? '',
            'total_debit'  => $totals->total_debit ?? 0,
            'total_credit' => $totals->total_credit ?? 0,
        ];
    });
    // dd($balances);
    // Totals
    $totalDebit  = $balances->sum('total_debit');
    $totalCredit = $balances->sum('total_credit');

    // Debug
    // dd($balances);
    $balances = $balances->sortBy('account_code')->values();

    return view('trialbalance.summary', [
        'balances' => $balances,
        'endDate'  => $endDate,
        'totalDebit' => $totalDebit,
        'totalCredit' => $totalCredit,
    ]);
}

    
    public function main(Request $request)
    {
        return $this->index($request);
    }
}