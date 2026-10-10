<?php

namespace App\Http\Controllers;

use App\Models\TRNDTL;
use Illuminate\Http\Request;
use App\Models\AccountMaster;
use Illuminate\Support\Carbon;

class LedgerDetailController extends Controller
{
    public function index(Request $request)
{
    $accounts = AccountMaster::all();

    // Fetch query parameters
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $accountId = $request->input('account_title');
    $status = $request->input('status'); // Get the status filter

    // Default empty collection for transactions
    $trndtls = collect();

    // Fetch data only if the search form is submitted
    if ($startDate && $endDate && $accountId) {
        // Get transactions for the selected account within the date range and status
        $trndtls = Trndtl::where(function ($query) use ($accountId) {
                $query->where('account_id', $accountId)
                      ->orWhere('cash_id', $accountId);
            })->with('accounts')
            ->whereBetween('date', [$startDate, $endDate]);

        // Apply status filter if provided
        if ($status) {
            $trndtls->where('status', $status);
        }

        $trndtls = $trndtls->get();

        // Calculate opening balance before the start date
        $openingBalanceData = Trndtl::where(function ($query) use ($accountId) {
                $query->where('account_id', $accountId)
                      ->orWhere('cash_id', $accountId);
            })
            ->where('date', '<', $startDate);

        // Apply status filter to opening balance if provided
        if ($status) {
            $openingBalanceData->where('status', $status);
        }

        $openingBalance = $openingBalanceData->sum('debit') - $openingBalanceData->sum('credit');
    } else {
        $openingBalance = 0;
    }

    $accountTitle = AccountMaster::find($accountId)->title ?? 'N/A';

    return view('ledger_detail.list', compact('accounts', 'trndtls', 'openingBalance', 'accountTitle', 'startDate', 'endDate', 'accountId', 'status'));
}


}
