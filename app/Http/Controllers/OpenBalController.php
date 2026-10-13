<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccountMaster;
use App\Models\OpenBal;
use App\Models\Module;
use App\Models\UserPermission;
use Illuminate\Support\Facades\DB;

class OpenBalController extends Controller
{
    public function index()
    {
        $module = Module::where('name', 'Opening Balance')->first();
        $permission = UserPermission::where('module_id', $module->id)
            ->where('write', 1)
            ->where('userid', auth()->user()->id)
            ->where('cid', auth()->user()->cid);
        if ($permission->exists() || auth()->user()->is_admin == 1) {

            $user = auth()->user();
            $cid = $user->cid ?? null;
            $accounts = AccountMaster::where('cid', $cid)->get();
            if (OpenBal::where('cid', $cid)->exists()) {
                return redirect()->route('open_bal.reports')
                    ->with('error', 'Opening Balance Already Exists. Please edit or delete existing entries.');
            }
            return \Inertia\Inertia::render('Accounts/Vouchers/JournalVoucherEntry', [
                'title' => 'Opening Balance Entry',
                'accounts' => $accounts,
                'storeRoute' => '/open_bal',
                'reportsRoute' => '/open_bal/reports',
            ]);
        }
        return redirect()->back()->with('error', 'You Have not Permission');
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $cid = $user->cid ?? null;

        $request->validate([
            'entry_account_title' => 'required|array',
            'entry_account_title.*' => 'required|exists:account_masters,id',
            'entry_debit' => 'required|array',
            'entry_credit' => 'required|array',
        ]);

        if ($request->total_debit != $request->total_credit) {
            return redirect()->back()->with('error', 'Total debit and credit amounts must be equal.');
        }

        $exists = OpenBal::where('cid', $cid)->exists();
        if ($exists) {
            return redirect()
                ->route('open_bal.reports')
                ->with('error', 'Opening Balance already exists. Please edit it instead.');
        }

        foreach ($request->entry_account_title as $index => $accountTitle) {
            OpenBal::create([
                'aid' => $accountTitle,
                'op_date' => $request->entry_date[$index] ?? now(),
                'debit' => $request->entry_debit[$index] ?? 0,
                'credit' => $request->entry_credit[$index] ?? 0,
                'cid' => $cid,
            ]);
        }

        return redirect()
            ->route('open_bal.reports')
            ->with('success', 'Opening Balance has been created successfully.');
    }

    public function reports(Request $request)
    {
        $user = auth()->user();
        $cid = $user->cid ?? null;
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = OpenBal::where('cid', $cid)->with('account');

        if ($startDate && $endDate) {
            $query->whereBetween('op_date', [$startDate, $endDate]);
        }

        $openBals = $query->orderBy('op_date', 'desc')->get();

        $vNoList = $openBals->pluck('id')->unique()->values()->toArray();

        return \Inertia\Inertia::render('Accounts/OpeningBalance', [
            'openBals' => $openBals,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'vNoList' => $vNoList,
        ]);
    }

    public function edit()
    {
        $module = Module::where('name', 'Opening Balance')->first();
        $permission = UserPermission::where('module_id', $module->id)
            ->where('update', 1)
            ->where('userid', auth()->user()->id)
            ->where('cid', auth()->user()->cid);
        if ($permission->exists() || auth()->user()->is_admin == 1) {
            $accounts = AccountMaster::where('cid', auth()->user()->cid)->get();
            $entries = OpenBal::where('cid', auth()->user()->cid)->get();

            return \Inertia\Inertia::render('Accounts/Vouchers/JournalVoucherEntry', [
                'title' => 'Edit Opening Balance Entry',
                'voucher' => $entries,
                'accounts' => $accounts,
                'updateRoute' => '/open_bal/update',
                'reportsRoute' => '/open_bal/reports',
            ]);
        }
        return redirect()->back()->with('error', 'You have not permission');
    }

    public function update(Request $request)
    {
        $request->validate([
            'voucher_date' => 'required|date',
            'entries' => 'required|array|min:1',
            'entries.*.account_title' => 'required|exists:account_masters,id',
        ]);

        $debitTotal = 0;
        $creditTotal = 0;

        foreach ($request->entries as $entry) {
            $debitTotal += floatval($entry['debit'] ?? 0);
            $creditTotal += floatval($entry['credit'] ?? 0);
        }

        if (abs($debitTotal - $creditTotal) > 0.01) {
            return back()->with('error', 'Total debit (' . number_format($debitTotal, 2) . ') and credit (' . number_format($creditTotal, 2) . ') amounts must be equal.')->withInput();
        }

        $user = auth()->user();
        $cid = $user->cid ?? null;

        try {
            DB::transaction(function () use ($request, $cid) {
                if (!empty($request->delete_entry_ids)) {
                    OpenBal::whereIn('id', $request->delete_entry_ids)
                        ->where('cid', $cid)
                        ->delete();
                }

                foreach ($request->entries as $entryData) {
                    $entryId = $entryData['id'] ?? null;
                    $debit = floatval($entryData['debit'] ?? 0);
                    $credit = floatval($entryData['credit'] ?? 0);

                    if ($debit == 0 && $credit == 0) {
                        if ($entryId) {
                            OpenBal::where('id', $entryId)->delete();
                        }
                        continue;
                    }

                    $data = [
                        'aid' => $entryData['account_title'],
                        'op_date' => $request->voucher_date,
                        'debit' => $debit,
                        'credit' => $credit,
                        'cid' => $cid,
                    ];

                    if ($entryId) {
                        OpenBal::where('id', $entryId)->update($data);
                    } else {
                        OpenBal::create($data);
                    }
                }

                OpenBal::where('cid', $cid)->update(['op_date' => $request->voucher_date]);
            });

            return redirect()->route('open_bal.reports')->with('success', 'Opening balance has been updated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error updating: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy()
    {
        $module = Module::where('name', 'Opening Balance')->first();
        $permission = UserPermission::where('module_id', $module->id)
            ->where('delete', 1)
            ->where('userid', auth()->user()->id)
            ->where('cid', auth()->user()->cid);
        if ($permission->exists() || auth()->user()->is_admin == 1) {
            $user = auth()->user();
            $cid = $user->cid ?? null;

            $exists = OpenBal::where('cid', $cid)->exists();
            if (!$exists) {
                return redirect()->route('open_bal.reports')
                    ->with('error', 'Balance not found.');
            }

            OpenBal::where('cid', $cid)->delete();

            return redirect()->route('open_bal.reports')
                ->with('success', 'Opening Balance has been deleted successfully!');
        }
        return redirect()->back()->with('error', 'You Have not permission');
    }

    public function delete()
    {
        $module = Module::where('name', 'Opening Balance')->first();
        $permission = UserPermission::where('module_id', $module->id)
            ->where('delete', 1)
            ->where('userid', auth()->user()->id)
            ->where('cid', auth()->user()->cid);
        if ($permission->exists() || auth()->user()->is_admin == 1) {
            $user = auth()->user();
            $cid = $user->cid ?? null;

            $exists = OpenBal::where('cid', $cid)->exists();
            if (!$exists) {
                return redirect()->route('open_bal.reports')
                    ->with('error', 'Balance not found.');
            }

            OpenBal::where('cid', $cid)->delete();

            return redirect()->route('open_bal.reports')
                ->with('success', 'Opening Balance has been deleted successfully!');
        }
        return redirect()->back()->with('error', 'You Have not permission');
    }

    public function getBalance(Request $request, $accountId)
    {
        $user = auth()->user();
        $cid = $user->cid ?? null;

        // Optional date parameter (Y-m-d format) - if provided, compute balance up to that date inclusive
        $date = $request->query('date');
        $query = DB::table('trn_dtl_views')
            ->where('aid', $accountId)
            ->where('cid', $cid);

        if ($date) {
            try {
                $date = \Carbon\Carbon::parse($date)->format('Y-m-d');
                $query->where('v_date', '<=', $date);
            } catch (\Exception $e) {
                // ignore invalid date, fall back to all-time
            }
        }

        $closingBalance = $query->sum(DB::raw('CAST(credit AS DECIMAL(15,2)) - CAST(debit AS DECIMAL(15,2))'));

        return response()->json(['balance' => $closingBalance ?? 0]);
    }
}
