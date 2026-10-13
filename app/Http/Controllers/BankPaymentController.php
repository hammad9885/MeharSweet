<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Module;
use App\Models\TRNDTL;
use App\Models\ErpParam;
use App\Models\BankPayment;
use App\Models\Customers;
use App\Models\Supplier;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use App\Models\AccountMaster;
use Illuminate\Support\Facades\DB;

class BankPaymentController extends Controller
{
    public function index()
    {
        $erpParams = ErpParam::with('level2')->where('cid', auth()->user()->cid)->get();

        $accountMasters = collect();

        if ($erpParams->isNotEmpty()) {
            $cashLevelId = $erpParams->first()->bank_level;

            $accountMasters = AccountMaster::where('level2_id', $cashLevelId)
                ->where('cid', auth()->user()->cid)
                ->get();
        }

        $accounts = AccountMaster::where('cid', auth()->user()->cid)->get();

        $customerUrduTitles = Customers::where('cid', auth()->user()->cid)
            ->whereNotNull('urdu_title')
            ->pluck('urdu_title', 'acc_id');

        $supplierUrduTitles = Supplier::where('cid', auth()->user()->cid)
            ->whereNotNull('mobile')
            ->pluck('mobile', 'acc_id');

        return \Inertia\Inertia::render('Accounts/Vouchers/VoucherEntry', [
            'title' => 'Bank Payment Voucher (BPV)',
            'vType' => 'BPV',
            'isPayment' => true,
            'accounts' => $accounts,
            'accountMasters' => $accountMasters,
            'customerUrduTitles' => $customerUrduTitles,
            'supplierUrduTitles' => $supplierUrduTitles,
            'storeRoute' => '/bank_payment',
            'reportsRoute' => '/bank_payment/reports',
        ]);
    }

    public function create()
    {
        return $this->index();
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $cid = $user->cid ?? null;
        $request->validate([
            'v_type' => 'required|string',
            'entries' => 'required|array',
            'entryDate' => 'required|date',
            'entries.*.cash' => 'required|string',
            'entries.*.account' => 'required|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $lastInvoiceNumber = TRNDTL::where('v_type', 'BPV')->where('cid', $cid)->max('v_no');
            $newInvoiceNumber = $lastInvoiceNumber ? ((int) $lastInvoiceNumber + 1) : 1;

            $filePath = null;
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
                $file->move(public_path('storage/uploads'), $fileName);
                $filePath = 'uploads/' . $fileName;
            }

            foreach ($request->entries as $index => $entry) {
                TRNDTL::create([
                    'v_no' => $newInvoiceNumber,
                    'v_type' => 'BPV',
                    'date' => $request->entryDate,
                    'cash_id' => $entry['cash'],
                    'account_id' => $entry['account'],
                    'description' => !empty($entry['description']) ? $entry['description'] : null,
                    'debit' => $entry['debit'],
                    'credit' => '0',
                    'preparedby' => $user->name,
                    'status' => 'N',
                    'file_id' => $filePath,
                    'cid' => $cid,
                ]);
            }

            DB::commit();
            return redirect()->route('bank_payment.reports')->with('success', 'BPV has been saved successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred while saving the voucher: ' . $e->getMessage());
        }
    }

    public function reports(Request $request)
    {
        $user = auth()->user();
        $cid = $user->cid ?? null;
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status');
        $v_no = $request->input('v_no');
        $account_id = $request->input('account_id');

        $query = TRNDTL::where('v_type', 'BPV')->where('cid', $cid)->with([
            'accounts.level2.level1s.groups'
        ]);

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($v_no) {
            $query->where('v_no', $v_no);
        }
        if ($account_id) {
            $query->where('account_id', $account_id);
        }

        $trndtls = $query
            ->orderBy('date', 'desc')
            ->orderByRaw('CAST(v_no AS UNSIGNED) DESC')
            ->get();

        $accountMasters = AccountMaster::where('cid', auth()->user()->cid)->get();
        $vNoList = TRNDTL::where('v_type', 'BPV')->where('cid', $cid)->pluck('v_no')->unique()->toArray();
        $accountIdList = TRNDTL::where('v_type', 'BPV')->where('cid', $cid)->pluck('account_id')->unique()->toArray();

        return \Inertia\Inertia::render('Accounts/Vouchers/VoucherReports', [
            'title' => 'Bank Payment Vouchers (BPV)',
            'vType' => 'BPV',
            'trndtls' => $trndtls,
            'accountMasters' => $accountMasters,
            'vNoList' => $vNoList,
            'accountIdList' => $accountIdList,
            'createRoute' => '/bank_payment/create',
            'editRoutePrefix' => '/bank_payment',
            'deleteRoutePrefix' => '/bank_payment-delete',
            'filters' => [
                'startDate' => $startDate,
                'endDate' => $endDate,
                'status' => $status,
            ],
        ]);
    }

    // Show the edit form
    public function edit($v_no)
    {
        $voucher = TRNDTL::where('v_no', $v_no)
            ->where('v_type', 'BPV')
            ->where('cid', auth()->user()->cid)
            ->get();

        $accounts = AccountMaster::where('cid', auth()->user()->cid)->get();

        $customerUrduTitles = Customers::where('cid', auth()->user()->cid)
            ->whereNotNull('urdu_title')
            ->pluck('urdu_title', 'acc_id');

        $supplierUrduTitles = Supplier::where('cid', auth()->user()->cid)
            ->whereNotNull('mobile')
            ->pluck('mobile', 'acc_id');

        // 🔹 Fetch ERP parameters to get account masters related to bank level
        $erpParams = ErpParam::with('level2')->where('cid', auth()->user()->cid)->get();

        $accountMasters = collect();

        if ($erpParams->isNotEmpty()) {
            $cashLevelId = $erpParams->first()->bank_level;
            $accountMasters = AccountMaster::where('level2_id', $cashLevelId)
                ->where('cid', auth()->user()->cid)
                ->get();
        }

        return \Inertia\Inertia::render('Accounts/Vouchers/VoucherEntry', [
            'title' => 'Edit Bank Payment Voucher (BPV)',
            'vType' => 'BPV',
            'isPayment' => true,
            'voucher' => $voucher,
            'v_no' => $v_no,
            'accounts' => $accounts,
            'accountMasters' => $accountMasters,
            'customerUrduTitles' => $customerUrduTitles,
            'supplierUrduTitles' => $supplierUrduTitles,
            'updateRoute' => '/bank_payment/' . $v_no . '/update',
            'reportsRoute' => '/bank_payment/reports',
        ]);
    }

    // Handle the update
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $cid = $user->cid;

        $entries = $request->input('entries', []);

        $existingEntries = TRNDTL::where('v_no', $id)
            ->where('v_type', 'BPV')
            ->where('cid', $cid)
            ->get()
            ->keyBy('id');

        $updatedIds = [];

        foreach ($entries as $key => $entry) {

            $isExisting = isset($entry['id']) && is_numeric($entry['id']);

            $filePath = null;

            if ($request->hasFile("entries.$key.file")) {
                $file = $request->file("entries.$key.file");
                $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
                $filePath = $file->storeAs('uploads', $fileName, 'public');
            }

            if ($isExisting && $existingEntries->has($entry['id'])) {

                $trn = $existingEntries[$entry['id']];

                $updateData = [
                    'date'        => $entry['date'],
                    'cash_id'     => $entry['cash'],
                    'account_id'  => $entry['account'],
                    'description' => $entry['description'] ?? null,
                    'debit'       => $entry['debit'],
                ];

                if ($filePath) {
                    $updateData['file_id'] = $filePath;
                }

                $trn->update($updateData);

                $updatedIds[] = $entry['id'];

            } else {
                $new = TRNDTL::create([
                    'v_no'        => $id,
                    'v_type'      => $request->v_type,
                    'date'        => $entry['date'],
                    'cash_id'     => $entry['cash'],
                    'account_id'  => $entry['account'],
                    'description' => $entry['description'] ?? null,
                    'credit'      => 0,
                    'debit'       => $entry['debit'],
                    'preparedby'  => $user->name,
                    'status'      => 'N',
                    'file_id'     => $filePath,
                    'cid'         => $cid
                ]);

                $updatedIds[] = $new->id;
            }
        }

        $deleteIds = $existingEntries->keys()->diff($updatedIds);

        if ($deleteIds->count() > 0) {
            TRNDTL::whereIn('id', $deleteIds)->delete();
        }

        return redirect()
            ->route('bank_payment.reports')
            ->with('success', 'BPV updated successfully.');
    }

    // Handle the delete
    public function destroy($id)
    {
        $trndtl = TRNDTL::where('v_type', 'BPV')
            ->where('cid', auth()->user()->cid)
            ->where('id', $id)
            ->firstOrFail();

        if ($trndtl->file_id) {
            $filePath = public_path('storage/' . $trndtl->file_id);
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        $trndtl->delete();

        return redirect()->route('bank_payment.reports')->with('success', 'Transaction and file deleted successfully!');
    }

    public function delete($id)
    {
        $trndtl = TRNDTL::where('v_type', 'BPV')
            ->where('cid', auth()->user()->cid)
            ->where('id', $id)
            ->firstOrFail();

        if ($trndtl->file_id) {
            $filePath = public_path('storage/' . $trndtl->file_id);
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        $trndtl->delete();

        return redirect()->route('bank_payment.reports')->with('success', 'Transaction and file deleted successfully!');
    }
}