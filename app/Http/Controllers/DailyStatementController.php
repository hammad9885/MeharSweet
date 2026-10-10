<?php

namespace App\Http\Controllers;

use App\Models\TRNDTL;
use App\Models\ChequeMaster;
use Illuminate\Http\Request;
use App\Models\AccountMaster;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DailyStatementController extends Controller
{
   public function reports(Request $request)
{
    // Set default to today (proper timezone handling)
    $defaultEndDate = now()->format('Y-m-d');

    // Get date from request or use default
    $endDate = $request->get('end_date', $defaultEndDate);

    $level5Accounts = AccountMaster::where('level2_id', 5)->get();
    $level5AccountIds = $level5Accounts->pluck('id')->toArray();

    $level5TransactionsQuery = \DB::table('trn_dtl_views')
        ->whereIn('aid', $level5AccountIds);

    if ($endDate) {
        $level5TransactionsQuery->whereDate('v_date', '<=', $endDate);
    }

    $level5Transactions = $level5TransactionsQuery
        ->select(
            'aid',
            \DB::raw('IFNULL(SUM(debit), 0) - IFNULL(SUM(credit), 0) AS balance'),
            \DB::raw('MAX(v_date) as last_transaction_date')
        )
        ->groupBy('aid')
        ->get();

    $level5Accounts = $level5Accounts->sortBy(function ($account) use ($level5Transactions) {
        $transaction = $level5Transactions->firstWhere('aid', $account->id);
        return $transaction ? 0 : 1;
    })->values();

    $level7Accounts = AccountMaster::where('level2_id', 7)->get();
    $level7AccountIds = $level7Accounts->pluck('id')->toArray();

    $level7TransactionsQuery = \DB::table('trn_dtl_views')
        ->whereIn('aid', $level7AccountIds);

    if ($endDate) {
        $level7TransactionsQuery->whereDate('v_date', '<=', $endDate);
    }

    $level7Transactions = $level7TransactionsQuery
        ->select(
            'aid',
            \DB::raw('IFNULL(SUM(debit), 0) - IFNULL(SUM(credit), 0) AS balance'),
            \DB::raw('MAX(v_date) as last_transaction_date')
        )
        ->groupBy('aid')
        ->get();

    $level7Accounts = $level7Accounts->sortBy(function ($account) use ($level7Transactions) {
        $transaction = $level7Transactions->firstWhere('aid', $account->id);
        return $transaction ? 0 : 1;
    })->values();

    $level6Accounts = AccountMaster::where('level2_id', 6)->get();
    $level6AccountIds = $level6Accounts->pluck('id')->toArray();

    $level6TransactionsQuery = \DB::table('trn_dtl_views')
        ->whereIn('aid', $level6AccountIds);

    if ($endDate) {
        $level6TransactionsQuery->whereDate('v_date', '<=', $endDate);
    }

    $level6Transactions = $level6TransactionsQuery
        ->select(
            'aid',
            \DB::raw('IFNULL(SUM(debit), 0) - IFNULL(SUM(credit), 0) AS balance'),
            \DB::raw('MAX(v_date) as last_transaction_date')
        )
        ->groupBy('aid')
        ->get();

    $level6Accounts = $level6Accounts->sortBy(function ($account) use ($level6Transactions) {
        $transaction = $level6Transactions->firstWhere('aid', $account->id);
        return $transaction ? 0 : 1;
    })->values();

    $level14Accounts = AccountMaster::where('level2_id', 14)->get();
    $level14AccountIds = $level14Accounts->pluck('id')->toArray();

    $level14TransactionsQuery = \DB::table('trn_dtl_views')
        ->whereIn('aid', $level14AccountIds);

    if ($endDate) {
        $level14TransactionsQuery->whereDate('v_date', '<=', $endDate);
    }

    $level14Transactions = $level14TransactionsQuery
        ->select(
            'aid',
            \DB::raw('IFNULL(SUM(debit), 0) - IFNULL(SUM(credit), 0) AS balance'),
            \DB::raw('MAX(v_date) as last_transaction_date')
        )
        ->groupBy('aid')
        ->get();

    $level14Accounts = $level14Accounts->sortBy(function ($account) use ($level14Transactions) {
        $transaction = $level14Transactions->firstWhere('aid', $account->id);
        return $transaction ? 0 : 1;
    })->values();

    // Level 4 23
$level4Accounts = AccountMaster::whereIn('level2_id', [4, 23])->get();
$level4AccountIds = $level4Accounts->pluck('id')->toArray();

$level4TransactionsQuery = \DB::table('trn_dtl_views')
    ->whereIn('aid', $level4AccountIds);

if ($endDate) {
    $level4TransactionsQuery->whereDate('v_date', '<=', $endDate);
}

$level4Transactions = $level4TransactionsQuery
    ->select(
        'aid',
        \DB::raw('IFNULL(SUM(debit), 0) - IFNULL(SUM(credit), 0) AS balance'),
        \DB::raw('MAX(v_date) as last_transaction_date')
    )
    ->groupBy('aid')
    ->get();

// Sort accounts - those with transactions first, then those without
$level4Accounts = $level4Accounts->sortBy(function ($account) use ($level4Transactions) {
    $transaction = $level4Transactions->firstWhere('aid', $account->id);
    return $transaction ? 0 : 1; // 0 comes before 1 in sorting
})->values();

    // Cheque
    $pendingChequesQuery = ChequeMaster::where('chq_status', 'Pending');

    if ($endDate) {
        $pendingChequesQuery->whereDate('created_at', '<=', $endDate);
    }

    $pendingCheques = $pendingChequesQuery
        ->select(
            'aid',
            DB::raw('SUM(chq_amt) as chq_amt'),
            DB::raw('MAX(DATE(created_at)) as last_transaction_date')
        )
        ->groupBy('aid')
        ->get();

    return view('daily_statement.index', compact(
        'level7Accounts',
        'level4Accounts',
        'level6Accounts',
        'level14Accounts',
        'level14Transactions',
        'level6Transactions',
        'level7Transactions',
        'level4Transactions',
        'level5Accounts',
        'level5Transactions',
        'pendingCheques',
        'endDate'
    ));
}

}
