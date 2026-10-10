<?php

namespace App\Http\Controllers;

use App\Models\AccountMaster;
use App\Models\Customers;
use App\Models\ErpParam;
use App\Models\Module;
use App\Models\TRNDTL;
use App\Models\UserPermission;
use Illuminate\Http\Request;

class CashController extends Controller
{
    public function index()
    {

        $accounts = AccountMaster::where('cid', auth()->user()->cid)->get();
        $erpParams = ErpParam::with('level2')
            ->where('cid', auth()->user()->cid)
            ->get();

        $accountMasters = collect();

        if ($erpParams->isNotEmpty()) {
            $cashLevelId = $erpParams->first()->cash_level;
            $accountMasters = AccountMaster::where('cid', auth()->user()->cid)
                ->where('level2_id', $cashLevelId)
                ->get();
        }

        $customerUrduTitles = Customers::where('cid', auth()->user()->cid)
            ->whereNotNull('urdu_title')
            ->pluck('urdu_title', 'acc_id');

        return view('cash.list', get_defined_vars());
    }

    public function generateVoucherType($baseType)
    {
        $latestVoucher = TRNDTL::where('v_type', 'like', "{$baseType}%")
            ->where('cid', auth()->user()->cid)
            ->orderBy('id', 'desc')
            ->first();

        if (! $latestVoucher) {
            return "{$baseType}1";
        }

        preg_match('/(\d+)$/', $latestVoucher->v_type, $matches);
        $nextNumber = isset($matches[1]) ? (int) $matches[1] + 1 : 1;

        return "{$baseType}{$nextNumber}";
    }

    public function store(Request $request)
    {
        $request->validate([
            'v_type' => 'required|string',
            'entries' => 'required|array',
            'voucher_date' => 'required|date',
            'entries.*.cash' => 'required|string',
            'entries.*.account' => 'required|string',
            'entries.*.credit' => 'required|numeric',
        ]);

        $user = auth()->user();
        $cid = $user->cid ?? null;

        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time().'_'.preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $filePath = $file->storeAs('uploads', $fileName, 'public');
        }

        $lastEntry = TRNDTL::where('v_type', $request->v_type)
            ->where('cid', $cid)
            ->orderBy('id', 'desc')
            ->first();

        $lastInvoiceNumber = $lastEntry && is_numeric($lastEntry->v_no) ? (int) $lastEntry->v_no : 0;
        $newInvoiceNumber = $lastInvoiceNumber + 1;

        foreach ($request->entries as $entry) {
            TRNDTL::create([
                'v_no' => $newInvoiceNumber,
                'v_type' => $request->v_type,
                'date' => $request->voucher_date,
                'cash_id' => $entry['cash'],
                'account_id' => $entry['account'],
                'description' => !empty($entry['description']) ? $entry['description'] : null,
                'credit' => $entry['credit'],
                'preparedby' => $user->name,
                'debit' => 0,
                'status' => 'N',
                'file_id' => $filePath,
                'cid' => $cid,
            ]);
        }

        return redirect()->route('cash.reports')
            ->with('success', $request->v_type.'-'.$newInvoiceNumber.' has been saved successfully.');
    }

    public function reports(Request $request)
    {

        $user = auth()->user();
        $cid = $user->cid ?? null;

        $query = TRNDTL::where('v_type', 'CRV')
            ->where('cid', $cid)
            ->with([
            'accounts.level2.level1s.groups' // Eager load nested relationships
        ]);

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->v_no) {
            $query->where('v_no', $request->v_no);
        }
        if ($request->account_id) {
            $query->where('account_id', $request->account_id);
        }

        $trndtls = $query->orderBy('date', 'desc')
            ->orderBy('v_no', 'desc')
            ->take(10)
            ->get();

        $accountMasters = AccountMaster::where('cid', $cid)->get();
        $vNoList = TRNDTL::where('v_type', 'CRV')->where('cid', $cid)->pluck('v_no')->unique()->toArray();
        $accountIdList = TRNDTL::where('v_type', 'CRV')->where('cid', $cid)->pluck('account_id')->unique()->toArray();

        return \Inertia\Inertia::render('Accounts/Vouchers/VoucherReports', [
            'title' => 'Cash Receipt Vouchers (CRV)',
            'vType' => 'CRV',
            'trndtls' => $trndtls,
            'accountMasters' => $accountMasters,
            'vNoList' => $vNoList,
            'accountIdList' => $accountIdList,
            'createRoute' => '/cash/create',
            'editRoutePrefix' => '/cash',
            'deleteRoutePrefix' => '/cash-delete',
            'filters' => [
                'startDate' => $request->start_date,
                'endDate' => $request->end_date,
                'status' => $request->status,
            ],
        ]);
    }

    public function edit($v_no)
    {

        $voucher = TRNDTL::where('v_no', $v_no)
            ->where('cid', auth()->user()->cid)
            ->where('v_type', 'CRV')
            ->get();

        $accounts = AccountMaster::where('cid', auth()->user()->cid)->get();
        $erpParams = ErpParam::with('level2')->where('cid', auth()->user()->cid)->get();

        $accountMasters = collect();
        if ($erpParams->isNotEmpty()) {
            $cashLevelId = $erpParams->first()->cash_level;
            $accountMasters = AccountMaster::where('level2_id', $cashLevelId)
                ->where('cid', auth()->user()->cid)
                ->get();
        }

        $customerUrduTitles = Customers::where('cid', auth()->user()->cid)
            ->whereNotNull('urdu_title')
            ->pluck('urdu_title', 'acc_id');

        return view('cash_reports.edit', get_defined_vars());
    }

     public function update(Request $request, $id)
{
    $user = auth()->user();
    $cid = $user->cid;

    $entries = $request->input('entries', []);

    // Fetch existing entries
    $existingEntries = TRNDTL::where('v_no', $id)
        ->where('v_type', 'CRV')
        ->where('cid', $cid)
        ->get()
        ->keyBy('id');

    $updatedIds = [];

    foreach ($entries as $key => $entry) {

        // Detect if this is an existing entry or a new one
        $isExisting = isset($entry['id']) && is_numeric($entry['id']);

        $filePath = null;

        // --------------------
        // FILE UPLOAD
        // --------------------
        if ($request->hasFile("entries.$key.file")) {

            $file = $request->file("entries.$key.file");

            $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());

            $filePath = $file->storeAs('uploads', $fileName, 'public');
        }

        // ----------------------
        // UPDATE EXISTING ENTRY
        // ----------------------
        if ($isExisting && $existingEntries->has($entry['id'])) {

            $trn = $existingEntries[$entry['id']];

            $updateData = [
                'date'        => $entry['date'],
                'cash_id'     => $entry['cash'],
                'account_id'  => $entry['account'],
                'description' => $entry['description'] ?? null,
                'credit'       => $entry['credit'],
            ];

            if ($filePath) {
                // Update file only if uploaded
                $updateData['file_id'] = $filePath;
            }

            $trn->update($updateData);

            $updatedIds[] = $entry['id'];

        }
        else {
            // ----------------------
            // CREATE NEW ENTRY
            // ----------------------
            $new = TRNDTL::create([
                'v_no'       => $id,
                'v_type'     => $request->v_type,
                'date'       => $entry['date'],
                'cash_id'    => $entry['cash'],
                'account_id' => $entry['account'],
                'description'=> $entry['description'] ?? null,
                'debit'      => 0,
                'credit'     => $entry['credit'],
                'preparedby' => $user->name,
                'status'     => 'N',
                'file_id'    => $filePath,
                'cid'        => $cid
            ]);

            $updatedIds[] = $new->id;
        }
    }

    // ------------------------
    // DELETE REMOVED ENTRIES
    // ------------------------
    $deleteIds = $existingEntries->keys()->diff($updatedIds);

    if ($deleteIds->count() > 0) {
        TRNDTL::whereIn('id', $deleteIds)->delete();
    }

    return redirect()
        ->route('cash.reports')
        ->with('success', 'CRV updated successfully.');
}

    public function destroy($id)
{
    
    $trndtl = TRNDTL::where('v_type', 'CRV')
        ->where('cid', auth()->user()->cid)
        ->where('id', $id)
        ->firstOrFail();

    // Delete the associated file if it exists
    if ($trndtl->file_id) {
        $filePath = public_path('storage/' . $trndtl->file_id);
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }

    $trndtl->delete();

    return redirect()->back()->with('success', 'Transaction and file deleted successfully!');
}


   public function delete($id)
{
   
    $trndtl = TRNDTL::where('v_type', 'CRV')
        ->where('cid', auth()->user()->cid)
        ->where('id', $id)
        ->firstOrFail();

    // Delete the associated file if it exists
    if ($trndtl->file_id) {
        $filePath = public_path('storage/' . $trndtl->file_id);
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }

    $trndtl->delete();

    return redirect()->back()->with('success', 'Transaction and file deleted successfully!');
}


    public function updateStatus(Request $request, $id)
    {
        $transaction = TRNDTL::findOrFail($id);
        $transaction->status = $request->status;
        $transaction->save();

        return response()->json(['success' => true]);
    }
}
