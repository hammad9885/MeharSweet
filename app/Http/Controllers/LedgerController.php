<?php

namespace App\Http\Controllers;

use App\Models\AccountMaster;
use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LedgerController extends Controller
{
    public function index(Request $request)
    {
        $cid = Auth::user()->cid;
        PackingLotPostingController::autoGenerateCompletedLots($cid);
        ContractorLotPostingController::autoGenerateLots($cid);

        $accountId = $request->get('account_title');
        $customers = Customer::where('cid', $cid)->get();
        $suppliers = Supplier::where('cid', $cid)->get();

        $erpParams = \App\Models\ErpParam::where('cid', $cid)->first();
        $contractorLevel = $erpParams ? $erpParams->contractor_level : null;
        if (!$contractorLevel) {
            $contractorLevel = DB::table('level2s')->where('cid', $cid)->where('title', 'like', '%Contractor%')->value('id');
        }

        $contractors = collect();
        if ($contractorLevel) {
            $contractors = AccountMaster::where('cid', $cid)->where('level2_id', $contractorLevel)->get();
        }

        $contractorModels = \App\Models\Contractor::where('cid', $cid)->get();
        foreach ($contractorModels as $cm) {
            $acc = null;
            if (isset($cm->acc_id) && $cm->acc_id) {
                $acc = AccountMaster::where('cid', $cid)->where('id', $cm->acc_id)->first();
            }
            if (!$acc) {
                $acc = AccountMaster::where('cid', $cid)->where('title', $cm->name)->first();
            }

            if ($acc) {
                if (!$contractors->contains('id', $acc->id)) {
                    $contractors->push($acc);
                }
            } else {
                $contractors->push($cm);
            }
        }

        // Deduplicate contractors
        $uniqueContractors = collect();
        $seenKeys = [];
        foreach ($contractors as $c) {
            $cAccId = $c->acc_id ?? $c->id;
            $cName = strtolower(trim($c->title ?? $c->name ?? ''));
            $key = $cAccId . '_' . $cName;
            if (!in_array($key, $seenKeys)) {
                $seenKeys[] = $key;
                $uniqueContractors->push($c);
            }
        }
        $contractors = $uniqueContractors;

        // Collect all used account IDs from customers, suppliers, and contractors to exclude from general accounts
        $usedAccIds = [];
        foreach ($customers as $cust) {
            if (!empty($cust->acc_id)) {
                $usedAccIds[] = (int)$cust->acc_id;
            }
        }
        foreach ($suppliers as $supp) {
            if (!empty($supp->acc_id)) {
                $usedAccIds[] = (int)$supp->acc_id;
            }
        }
        foreach ($contractors as $con) {
            $cId = $con->acc_id ?? $con->id;
            if (!empty($cId)) {
                $usedAccIds[] = (int)$cId;
            }
        }

        $usedAccIds = array_unique(array_filter($usedAccIds));

        $accounts = AccountMaster::where('cid', $cid)
            ->whereNotIn('id', $usedAccIds)
            ->get();

        // Default empty values
        $trndtls = collect();
        $openingBalance = 0;
        $totalDebit = 0;
        $totalCredit = 0;
        $closingBalance = 0;

        // Only run query if account is selected
        if ($accountId) {
            $trnDetails = DB::table('trn_dtl_views')
                ->leftJoin('trn_dtl', 'trn_dtl.id', '=', 'trn_dtl_views.id')
                ->select(
                    'trn_dtl_views.id',
                    'trn_dtl_views.aid',
                    'trn_dtl_views.v_date',
                    'trn_dtl_views.v_no',
                    'trn_dtl_views.v_type',
                    'trn_dtl_views.descr',
                    'trn_dtl_views.debit',
                    'trn_dtl_views.credit',
                    'trn_dtl_views.cid',
                    'trn_dtl.r_id'
                )
                ->where('trn_dtl_views.aid', $accountId)
                ->when($request->start_date, fn ($q) => $q->where('trn_dtl_views.v_date', '>=', $request->start_date))
                ->when($request->end_date, fn ($q) => $q->where('trn_dtl_views.v_date', '<=', $request->end_date))
                ->where('trn_dtl_views.cid', $cid)
                ->get();

            $clpQuery = DB::table('contractor_lot_posting_details as d')
                ->join('contractor_lot_posting_masters as m', function ($join) {
                    $join->on('m.voucher_no', '=', 'd.voucher_no')
                         ->on('m.cid', '=', 'd.cid');
                })
                ->leftJoin('product_masters as p', 'p.id', '=', 'm.product_id')
                ->select(
                    DB::raw('MAX(m.id) as id'),
                    'd.contractor_id as aid',
                    'm.date as v_date',
                    'm.voucher_no as v_no',
                    DB::raw("'CLP' as v_type"),
                    DB::raw("TRIM(CONCAT(COALESCE(p.product_name, ''), IF(m.lot_no IS NOT NULL AND m.lot_no != '', CONCAT(' - Lot # ', m.lot_no), ''))) as descr"),
                    DB::raw('SUM(d.wages_amount) as debit'),
                    DB::raw('0 as credit'),
                    'd.cid as cid',
                    DB::raw('MAX(m.id) as r_id')
                )
                ->where('d.contractor_id', $accountId)
                ->where('d.cid', $cid)
                ->whereNull('m.deleted_at')
                ->when($request->start_date, fn ($q) => $q->where('m.date', '>=', $request->start_date))
                ->when($request->end_date, fn ($q) => $q->where('m.date', '<=', $request->end_date))
                ->groupBy('m.voucher_no', 'd.contractor_id', 'm.date', 'm.lot_no', 'p.product_name', 'd.cid')
                ->havingRaw('SUM(d.wages_amount) > 0');

            $clpDetails = $clpQuery->get();

            $plpQuery = DB::table('packing_lot_posting_details as d')
                ->join('packing_lot_posting_masters as m', function ($join) {
                    $join->on('m.voucher_no', '=', 'd.voucher_no')
                         ->on('m.cid', '=', 'd.cid');
                })
                ->leftJoin('product_masters as p', 'p.id', '=', 'm.product_id')
                ->select(
                    DB::raw('MAX(m.id) as id'),
                    'd.contractor_id as aid',
                    'm.date as v_date',
                    'm.voucher_no as v_no',
                    DB::raw("'PLP' as v_type"),
                    DB::raw("TRIM(CONCAT(COALESCE(p.product_name, ''), IF(m.lot_no IS NOT NULL AND m.lot_no != '', CONCAT(' - Lot # ', m.lot_no), ''))) as descr"),
                    DB::raw('SUM(d.wages_amount) as debit'),
                    DB::raw('0 as credit'),
                    'd.cid as cid',
                    DB::raw('MAX(m.id) as r_id')
                )
                ->where('d.contractor_id', $accountId)
                ->where('d.cid', $cid)
                ->whereNull('m.deleted_at')
                ->when($request->start_date, fn ($q) => $q->where('m.date', '>=', $request->start_date))
                ->when($request->end_date, fn ($q) => $q->where('m.date', '<=', $request->end_date))
                ->groupBy('m.voucher_no', 'd.contractor_id', 'm.date', 'm.lot_no', 'p.product_name', 'd.cid')
                ->havingRaw('SUM(d.wages_amount) > 0');

            $plpDetails = $plpQuery->get();

            $conDetails = collect();
            $conOpening = 0;

            $isZamZam = (Auth::check() && (strtolower(Auth::user()->email) === 'zamzamtoys.admin@gmail.com' || Auth::user()->cid == 19));
            if ($isZamZam) {
                $conQuery = DB::table('consumption_details as d')
                    ->join('consumption_masters as m', function ($join) {
                        $join->on('m.v_no', '=', 'd.v_no')
                             ->on('m.cid', '=', 'd.cid');
                    })
                    ->leftJoin('product_masters as p', 'p.id', '=', 'm.item_id')
                    ->select(
                        DB::raw('MAX(m.id) as id'),
                        DB::raw('COALESCE(d.contractor_account_id, m.contractor_account_id) as aid'),
                        'm.date as v_date',
                        'm.v_no as v_no',
                        DB::raw("'CON' as v_type"),
                        DB::raw("TRIM(CONCAT(
                            COALESCE(p.product_name, ''),
                            IF(m.lot_no IS NOT NULL AND m.lot_no != '', CONCAT(' - Lot # ', m.lot_no), ''),
                            IF(m.descr IS NOT NULL AND m.descr != '' AND m.descr != p.product_name, CONCAT(' - ', m.descr), '')
                        )) as descr"),
                        DB::raw('SUM(CASE WHEN COALESCE(d.mat_amount, 0) > 0 THEN d.mat_amount ELSE COALESCE(d.amount, 0) END) as debit'),
                        DB::raw('0 as credit'),
                        'd.cid as cid',
                        DB::raw('MAX(m.id) as r_id')
                    )
                    ->where(function ($q) use ($accountId) {
                        $q->where('d.contractor_account_id', $accountId)
                          ->orWhere(function ($sq) use ($accountId) {
                              $sq->whereNull('d.contractor_account_id')
                                 ->where('m.contractor_account_id', $accountId);
                          });
                    })
                    ->where('d.cid', $cid)
                    ->when($request->start_date, fn ($q) => $q->where('m.date', '>=', $request->start_date))
                    ->when($request->end_date, fn ($q) => $q->where('m.date', '<=', $request->end_date))
                    ->groupBy('m.v_no', DB::raw('COALESCE(d.contractor_account_id, m.contractor_account_id)'), 'm.date', 'm.lot_no', 'p.product_name', 'm.descr', 'd.cid')
                    ->havingRaw('SUM(CASE WHEN COALESCE(d.mat_amount, 0) > 0 THEN d.mat_amount ELSE COALESCE(d.amount, 0) END) > 0');

                $conDetails = $conQuery->get();

                $conOpening = 0;
                if ($request->filled('start_date')) {
                    $conOpening = DB::table('consumption_details as d')
                        ->join('consumption_masters as m', function ($join) {
                            $join->on('m.v_no', '=', 'd.v_no')
                                 ->on('m.cid', '=', 'd.cid');
                        })
                        ->where(function ($q) use ($accountId) {
                            $q->where('d.contractor_account_id', $accountId)
                              ->orWhere(function ($sq) use ($accountId) {
                                  $sq->whereNull('d.contractor_account_id')
                                     ->where('m.contractor_account_id', $accountId);
                              });
                        })
                        ->where('d.cid', $cid)
                        ->whereDate('m.date', '<', $request->start_date)
                        ->sum(DB::raw('CASE WHEN COALESCE(d.mat_amount, 0) > 0 THEN d.mat_amount ELSE COALESCE(d.amount, 0) END'));
                }
            }

            $trndtls = $trnDetails->concat($clpDetails)->concat($plpDetails)->concat($conDetails)
                ->filter(function ($row) {
                    return (float)($row->debit ?? 0) != 0 || (float)($row->credit ?? 0) != 0;
                })
                ->sortBy([
                    ['v_date', 'asc'],
                    ['v_no', 'asc']
                ])->values();

            // Calculate opening balance (before start date)
            $openingBalance = 0;
            if ($request->filled('start_date')) {
                $trnRowsBeforeStart = DB::table('trn_dtl_views')
                    ->where('aid', $accountId)
                    ->whereDate('v_date', '<', $request->start_date)
                    ->where('cid', $cid)
                    ->get();

                $trnOpening = 0;
                foreach ($trnRowsBeforeStart as $trn) {
                    $rowDebit = (float)($trn->debit ?? 0);
                    $rowCredit = (float)($trn->credit ?? 0);

                    $displayDebit = 0;
                    $displayCredit = 0;

                    switch ($trn->v_type) {
                        case 'PIN':  // Purchase = Debit
                            $displayDebit = $rowDebit ?: $rowCredit;
                            break;
                        case 'SIN':  // Sale = Credit
                            $displayCredit = $rowCredit ?: $rowDebit;
                            break;
                        case 'BPV':  // BPV = Credit
                            $displayCredit = $rowCredit ?: $rowDebit;
                            break;
                        case 'CPV':  // CPV = Credit
                            $displayCredit = $rowCredit ?: $rowDebit;
                            break;
                        case 'BRV':  // BRV = Debit
                            $displayDebit = $rowDebit ?: $rowCredit;
                            break;
                        case 'CRV':  // CRV = Debit
                            $displayDebit = $rowDebit ?: $rowCredit;
                            break;
                        case 'CLP':  // Contractor Lot Posting = Debit
                        case 'PLP':  // Packing Lot Posting = Debit
                        case 'CON':  // Consumption = Debit
                            $displayDebit = $rowDebit;
                            $displayCredit = 0;
                            break;
                        case 'OPN':  // Opening Balance = as stored
                        default:     // Other types = as stored
                            $displayDebit = $rowDebit;
                            $displayCredit = $rowCredit;
                            break;
                    }

                    $trnOpening += ($displayCredit - $displayDebit);
                }

                $clpOpening = DB::table('contractor_lot_posting_details as d')
                    ->join('contractor_lot_posting_masters as m', function ($join) {
                        $join->on('m.voucher_no', '=', 'd.voucher_no')
                             ->on('m.cid', '=', 'd.cid');
                    })
                    ->where('d.contractor_id', $accountId)
                    ->where('d.cid', $cid)
                    ->whereNull('m.deleted_at')
                    ->whereDate('m.date', '<', $request->start_date)
                    ->sum('d.wages_amount');

                $plpOpening = DB::table('packing_lot_posting_details as d')
                    ->join('packing_lot_posting_masters as m', function ($join) {
                        $join->on('m.voucher_no', '=', 'd.voucher_no')
                             ->on('m.cid', '=', 'd.cid');
                    })
                    ->where('d.contractor_id', $accountId)
                    ->where('d.cid', $cid)
                    ->whereNull('m.deleted_at')
                    ->whereDate('m.date', '<', $request->start_date)
                    ->sum('d.wages_amount');

                $openingBalance = $trnOpening - $clpOpening - $plpOpening - $conOpening;
            }

            $totalDebit = $trndtls->sum('debit');
            $totalCredit = $trndtls->sum('credit');
            $closingBalance = $openingBalance + ($totalCredit - $totalDebit);
        }
        
        

        return \Inertia\Inertia::render('Accounts/Ledger', [
            'trndtls' => $trndtls ?? [],
            'openingBalance' => $openingBalance ?? 0,
            'totalDebit' => $totalDebit ?? 0,
            'totalCredit' => $totalCredit ?? 0,
            'closingBalance' => $closingBalance ?? 0,
            'customers' => $customers ?? [],
            'suppliers' => $suppliers ?? [],
            'contractors' => $contractors ?? [],
            'accounts' => $accounts ?? [],
            'accountId' => $accountId ?? null,
        ]);
    }
}