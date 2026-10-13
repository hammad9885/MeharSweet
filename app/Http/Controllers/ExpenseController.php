<?php

namespace App\Http\Controllers;

use App\Models\TRNDTL;
use App\Models\ChequeMaster;
use Illuminate\Http\Request;
use App\Models\AccountMaster;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{


public function reports(Request $request)
{
    $user = auth()->user();
    $cid = $user->cid ?? null;

    $firstDayOfMonth = Carbon::now()->firstOfMonth()->format('Y-m-d');
    $latestDate = TRNDTL::where('cid', $cid)->max('date');

    $query = TRNDTL::query()
        ->where('cid', $cid)
        ->join('COA as c', 't_r_n_d_t_l_s.account_id', '=', 'c.acc_id')
        ->join('account_masters as a', 't_r_n_d_t_l_s.account_id', '=', 'a.id')
        ->whereIn('c.group_id', [2, 5])
        ->selectRaw('MAX(t_r_n_d_t_l_s.date) as latest_date, c.LEVEL2_TITLE, a.title as account_title, SUM(t_r_n_d_t_l_s.debit) as total_amount')
        ->groupBy('c.LEVEL2_TITLE', 'a.title')
        ->orderBy('latest_date', 'desc');

    if ($request->filled('start_date') && $request->filled('end_date')) {
        $startDate = Carbon::parse($request->start_date)->format('Y-m-d');
        $endDate = Carbon::parse($request->end_date)->format('Y-m-d');
        $query->whereBetween('t_r_n_d_t_l_s.date', [$startDate, $endDate]);
    } else {
        $query->whereBetween('t_r_n_d_t_l_s.date', [$firstDayOfMonth, $latestDate]);
    }

    if ($request->filled('status')) {
        $query->where('t_r_n_d_t_l_s.status', $request->status);
    }

    if ($request->filled('level2_title')) {
        $query->where('c.LEVEL2_TITLE', $request->level2_title);
    }

    $result = $query->get();

    $level2Titles = DB::table('COA')
        ->whereIn('group_id', [2, 5])
        ->pluck('LEVEL2_TITLE')
        ->unique();

    return \Inertia\Inertia::render('Accounts/Reports/ExpenseReport', [
        'result'       => $result,
        'level2Titles' => $level2Titles,
        'filters'      => $request->only(['start_date', 'end_date', 'status', 'level2_title']),
    ]);
}
}

