<?php

namespace App\Http\Controllers;

use App\Models\TRNDTL;
use App\Models\ItemMaster;
use Illuminate\Http\Request;
use App\Models\AccountMaster;
use App\Models\JournalVoucher;
use App\Models\Module;
use App\Models\UserPermission;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class JournalVoucherController extends Controller
{
    public function index()
    {
        $user = auth()->user();
     $moduleId=Module::where('name','Journal Voucher')->first();
        $permiision=UserPermission::where('module_id',$moduleId->id)->where('cid',$user->cid)
 ->where('userid',$user->id)
        ->where('write',1);
        if(!$permiision->exists()){
            return redirect()->back()->with('error','You have not Permission. Please Contact with your admin');
        }
    $user = auth()->user();
    $cid = $user->cid ?? null;
    $accounts = AccountMaster::where('cid', $cid)->get();
        return \Inertia\Inertia::render('Accounts/Vouchers/JournalVoucherEntry', [
            'title' => 'Journal Voucher (JV)',
            'accounts' => $accounts,
            'storeRoute' => '/journal_voucher',
            'reportsRoute' => '/journal_voucher/reports',
        ]);
    }
    public function store(Request $request)
{

    $user = auth()->user();
    $cid = $user->cid ?? null;
    $request->validate([
        'entry_account_title' => 'required|array',
        'entry_debit' => 'required|array',
        'voucher_date' => 'required|date',
        'entry_credit' => 'required|array',
    ]);

    if ($request->total_debit != $request->total_credit) {
        return redirect()->back()->with('error', 'Total debit and credit amounts must be equal.');
    }

    $lastEntry = TRNDTL::where('v_type', 'JV')->where('cid', $cid)
        ->orderBy('id', 'desc')
        ->first();

    $lastInvoiceNumber = $lastEntry && is_numeric($lastEntry->v_no) ? (int) $lastEntry->v_no : 0;
    $newInvoiceNumber = $lastInvoiceNumber + 1;

    foreach ($request->entry_account_title as $index => $accountTitle) {
        TRNDTL::create([
            'v_no' => $newInvoiceNumber,
            'account_id' => $accountTitle,
            'status' => 'N',
            'debit' => $request->entry_debit[$index] ?? 0,
            'credit' => $request->entry_credit[$index] ?? 0,
            'date' => $request->voucher_date,
            'v_type' => 'JV',
            'preparedby' => $user->name,
            'description' => $request->entry_description[$index] ?? '',
            'cid' => $cid,
        ]);
    }

    return redirect()->route('journal_voucher.reports')->with('success', $request->v_type . '-' . $newInvoiceNumber . ' has been saved successfully.');
}

    public function reports(Request $request)
{
      $user = auth()->user();
     $moduleId=Module::where('name','Journal Voucher')->first();
        $permiision=UserPermission::where('module_id',$moduleId->id)->where('cid',$user->cid)
 ->where('userid',$user->id)
        ->where('read',1);
        if(!$permiision->exists()){
            return redirect()->back()->with('error','You have not Permission. Please Contact with your admin');
        }
    $user = auth()->user();
    $cid = $user->cid ?? null;
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $status = $request->input('status');
    $v_no = $request->input('v_no');
    $description = $request->input('description');

    $query = TRNDTL::where('v_type', 'JV')->where('cid', $cid)->with('accounts');

    if ($startDate && $endDate) {
        $query->whereBetween('date', [$startDate, $endDate]);
    }

    if ($status) {
        $query->where('status', $status);
    }

    if ($v_no) {
        $query->where('v_no', $v_no);
    }
    if ($description) {
        $query->where('description', 'LIKE', "%$description%");
    }

    $trndtls = $query->orderByRaw('DATE(date) DESC')
        ->orderBy('v_no', 'desc')
        ->get();

    $vNoList = TRNDTL::where('v_type', 'JV')->where('cid', $cid)->pluck('v_no')->unique()->toArray();
    $descriptionList = TRNDTL::where('v_type', 'JV')->where('cid', $cid)->pluck('description')->unique()->toArray();
    $accountMasters = AccountMaster::where('cid', $cid)->get();

    return \Inertia\Inertia::render('Accounts/Vouchers/VoucherReports', [
        'title' => 'Journal Vouchers (JV)',
        'vType' => 'JV',
        'trndtls' => $trndtls,
        'accountMasters' => $accountMasters,
        'vNoList' => $vNoList,
        'accountIdList' => [],
        'createRoute' => '/journal_voucher',
        'editRoutePrefix' => '/journal_voucher',
        'deleteRoutePrefix' => '/journal_voucher/delete',
        'filters' => [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'status' => $status,
        ],
    ]);
}



    // Show the edit form for all entries of a voucher
    public function edit($v_no)
    {
          $user = auth()->user();
     $moduleId=Module::where('name','Journal Voucher')->first();
        $permiision=UserPermission::where('module_id',$moduleId->id)->where('cid',$user->cid)
 ->where('userid',$user->id)
        ->where('update',1);
        if(!$permiision->exists()){
            return redirect()->back()->with('error','You have not Permission. Please Contact with your admin');
        }
        $accounts = AccountMaster::where('cid', auth()->user()->cid)->get();
        $voucher = TRNDTL::where('v_no', $v_no)->where('v_type', 'JV')
         ->where('cid', auth()->user()->cid)
        ->get();
        return \Inertia\Inertia::render('Accounts/Vouchers/JournalVoucherEntry', [
            'title' => 'Edit Journal Voucher (JV)',
            'voucher' => $voucher,
            'v_no' => $v_no,
            'accounts' => $accounts,
            'updateRoute' => '/journal_voucher/' . $v_no . '/update',
            'reportsRoute' => '/journal_voucher/reports',
        ]);
    }

   public function update(Request $request, $v_no)
{
    // Validate the request
    $request->validate([
        'voucher_date' => 'required|date',
        'entries' => 'required|array|min:1',
        'entries.*.id' => 'nullable|integer|exists:trn_dtl,id',
        'entries.*.date' => 'required|date',
        'entries.*.account_title' => 'required|exists:account_masters,id',
        'entries.*.debit' => 'nullable|numeric|min:0',
        'entries.*.credit' => 'nullable|numeric|min:0',
        'entries.*.description' => 'nullable|string|max:255',
        'delete_entry_ids' => 'sometimes|array',
        'delete_entry_ids.*' => 'integer|exists:trn_dtl,id',
    ]);

    // Calculate totals
    $debitTotal = 0;
    $creditTotal = 0;
    
    foreach ($request->entries as $entry) {
        $debitTotal += floatval($entry['debit'] ?? 0);
        $creditTotal += floatval($entry['credit'] ?? 0);
    }

    // Check if debit and credit totals are equal
    if (abs($debitTotal - $creditTotal) > 0.01) {
        return back()
            ->with('error', 'Total debit ('.number_format($debitTotal, 2).') and credit ('.number_format($creditTotal, 2).') amounts must be equal.')
            ->withInput();
    }

    $user = auth()->user();
    $cid = $user->cid ?? null;

    try {
        DB::transaction(function () use ($request, $v_no, $cid, $user) {
            // Delete entries marked for deletion
            if (!empty($request->delete_entry_ids)) {
                TRNDTL::whereIn('id', $request->delete_entry_ids)
                    ->where('v_no', $v_no)
                    ->where('v_type', 'JV')
                    ->where('cid', $cid)
                    ->delete();
            }

            // Process each entry
            foreach ($request->entries as $entryData) {
                $entryId = $entryData['id'] ?? null;
                $debit = floatval($entryData['debit'] ?? 0);
                $credit = floatval($entryData['credit'] ?? 0);

                // Skip if both debit and credit are zero
                if ($debit == 0 && $credit == 0) {
                    // If it's an existing entry with both zero, delete it
                    if ($entryId) {
                        TRNDTL::where('id', $entryId)
                            ->where('cid', $cid)
                            ->delete();
                    }
                    continue;
                }

                // Prepare data for update/insert
                $data = [
                    'v_no'        => $v_no,
                    'account_id'  => $entryData['account_title'],
                    'debit'       => $debit,
                    'credit'      => $credit,
                    'status'      => 'N',
                    'date'        => $entryData['date'],
                    'v_type'      => 'JV',
                    'preparedby'  => $user->name,
                    'description' => $entryData['description'] ?? '',
                    'cid'         => $cid,
                ];

                if ($entryId) {
                    // Update existing entry
                    TRNDTL::where('id', $entryId)
                        ->where('cid', $cid)
                        ->update($data);
                } else {
                    // Create new entry
                    TRNDTL::create($data);
                }
            }

            // Update voucher date for all entries
            TRNDTL::where('v_no', $v_no)
                ->where('v_type', 'JV')
                ->where('cid', $cid)
                ->update(['date' => $request->voucher_date]);
        });

        return redirect()
            ->route('journal_voucher.reports')
            ->with('success', 'JV-' . $v_no . ' has been updated successfully.');

    } catch (\Exception $e) {
        return back()
            ->with('error', 'Error updating voucher: ' . $e->getMessage())
            ->withInput();
    }
}


    public function destroy($v_no)
    {
          $user = auth()->user();
     $moduleId=Module::where('name','Journal Voucher')->first();
        $permiision=UserPermission::where('module_id',$moduleId->id)->where('cid',$user->cid)
 ->where('userid',$user->id)
        ->where('delete',1);
        if(!$permiision->exists()){
            return redirect()->back()->with('error','You have not Permission. Please Contact with your admin');
        }
        // Delete all rows for the voucher number
        $exists = TRNDTL::where('v_type', 'JV')
         ->where('cid', auth()->user()->cid)
            ->where('v_no', $v_no)
            ->exists();

        if (!$exists) {
            return redirect()->route('journal_voucher.reports')
                ->with('error', 'Voucher not found.');
        }

    DB::transaction(function () use ($v_no) {
            TRNDTL::where('v_type', 'JV')
             ->where('cid', auth()->user()->cid)
                ->where('v_no', $v_no)
                ->delete();
        });

        return redirect()->route('journal_voucher.reports')
            ->with('success', 'JV-' . $v_no . ' has been deleted successfully!');
    }
    
     public function delete($id)
    {
          $user = auth()->user();
     $moduleId=Module::where('name','Journal Voucher')->first();
        $permiision=UserPermission::where('module_id',$moduleId->id)->where('cid',$user->cid)
 ->where('userid',$user->id)
        ->where('delete',1);
        if(!$permiision->exists()){
            return redirect()->back()->with('error','You have not Permission. Please Contact with your admin');
        }
        // Find the transaction by ID
        $trndtl = TRNDTL::where('v_type', 'JV')
         ->where('cid', auth()->user()->cid)
                    ->where('id', $id)
                    ->firstOrFail();

        // Delete the transaction
        $trndtl->delete();

        // Redirect back with a success message
        return redirect()->route('journal_voucher.reports')->with('success', 'The JV transaction has been deleted successfully!');
    }
    
}
