<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use App\Models\TRNDTL;
use App\Models\AccountMaster;
use Illuminate\Support\Facades\DB;

class PayableController extends Controller 
{
public function index(Request $request)
{
    $endDate = $request->get('end_date');
    $cid = auth()->user()->cid;

    // ✅ Step 1: Get Payable Accounts (non-cash, non-bank, in group 2 or 3)
    $payableAccountIds = AccountMaster::whereIn('level2_id', function ($query) use ($cid) {
            $query->select('id')
                  ->from('level2s')
                  ->where('cid', $cid)
                  ->whereIn('level1_id', function ($subquery) use ($cid) {
                      $subquery->select('id')
                               ->from('level1s')
                               ->where('cid', $cid)
                               ->whereIn('group_id', [2, 3]);
                  })
                  ->whereNotIn('id', function ($subquery) {
                      $subquery->select('cash_level')
                               ->from('erp_params')
                               ->whereNotNull('cash_level')
                               ->union(
                                   DB::table('erp_params')
                                       ->select('bank_level')
                                       ->whereNotNull('bank_level')
                               );
                  });
        })
        ->pluck('id'); // get IDs only

    // ✅ Step 2: Get balances only for those accounts
    $trndtls = TRNDTL::select('account_id')
        ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
        ->where('cid', $cid)
        ->whereIn('account_id', $payableAccountIds)
        ->when($endDate, fn($q) => $q->whereDate('date', '<=', $endDate))
        ->groupBy('account_id')
        ->havingRaw('SUM(debit) - SUM(credit) < 0')
        ->get();

    // ✅ Step 3: Attach account titles (avoid N+1)
    // ✅ Step 3: Load AccountMaster models for the selected accounts
    // Note: there is no `acc_codes` relationship on AccountMaster, so we load the
    // AccountMaster models themselves and attach the model as `acc_codes` so the
    // blade can call ->acc_codes->full_code (the `full_code` accessor exists).
    $accounts = AccountMaster::whereIn('id', $trndtls->pluck('account_id'))
        ->get()
        ->keyBy('id');

    // Map balances and attach the AccountMaster model as `acc_codes`.
    $balances = $trndtls->map(function ($row) use ($accounts) {
        $account = $accounts->get($row->account_id);

        return (object) [
            'id' => $row->account_id,
            'title' => $account->title ?? 'Unknown',
            'total_debit' => $row->total_debit,
            'total_credit' => $row->total_credit,
            'balance' => $row->total_debit - $row->total_credit,
            'acc_codes' => $account ?: null,
        ];
    });

    return \Inertia\Inertia::render('Accounts/PayablesReceivables', [
        'title' => 'Accounts Payable Summary',
        'type' => 'payables',
        'balances' => $balances,
        'endDate' => $endDate,
    ]);
}

}
