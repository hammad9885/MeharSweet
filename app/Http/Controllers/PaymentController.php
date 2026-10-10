<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\TRNDTL;
use App\Models\Payment;
use App\Models\ErpParam;
use App\Models\Customers;
use Illuminate\Http\Request;
use App\Models\AccountMaster;
use App\Models\Group;
use App\Models\Level1;
use App\Models\Level2;

class PaymentController extends Controller
{
    public function index()
    {
        $erpParams = ErpParam::with('level2')
        ->where('cid', auth()->user()->cid)
        ->get();

        // Initialize accountMasters as an empty collection to avoid errors
        $accountMasters = collect();

        // Check if there is at least one ERP Param and that cash_level is set
        if ($erpParams->isNotEmpty()) {
            // Get the cash_level from the first ERP Param
            $cashLevelId = $erpParams->first()->cash_level;

            // Fetch AccountMasters associated with the cash_level
            $accountMasters = AccountMaster::where('level2_id', $cashLevelId)
            ->where('cid', auth()->user()->cid)
            ->get();
        }
        $accounts = AccountMaster::where('cid', auth()->user()->cid)->get();

        $customerUrduTitles = Customers::where('cid', auth()->user()->cid)
            ->whereNotNull('urdu_title')
            ->pluck('urdu_title', 'acc_id');

        return view('payment.list', get_defined_vars());
    }

    public function store(Request $request)
    {
        // Validate the overall request, including file upload validation
        $request->validate([
            'v_type' => 'required|string',
            'voucher_date' => 'required|date',
            'entries' => 'required|array',
            'entries.*.cash' => 'required|string',
            'entries.*.account' => 'required|string',
      
        ]);

        $user = auth()->user();
        $cid = $user->cid ?? null;

        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $filePath = $file->storeAs('uploads', $fileName, 'public');
        }

        $lastEntry = TRNDTL::where('v_type', $request->v_type)
            ->where('cid', $cid)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastEntry && is_numeric($lastEntry->v_no)) {
            $lastInvoiceNumber = (int) $lastEntry->v_no;
        } else {
            $lastInvoiceNumber = 0;
        }

        $newInvoiceNumber = $lastInvoiceNumber + 1;
        foreach ($request->entries as $entry) {
            TRNDTL::create([
                'v_no' => $newInvoiceNumber,
                'v_type' => $request->v_type,
                'date' => $request->voucher_date,
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

        return redirect()
            ->route('payment.reports')
            ->with('success', '' . $request->v_type . '-' . $newInvoiceNumber . ' has been saved successfully.');
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

        $query = TRNDTL::where('v_type', 'CPV')->where('cid', $cid)->with([
            'accounts.level2.level1s.groups' // Eager load nested relationships
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
            ->orderBy('v_no', 'desc')
            ->get();

        $accountMasters = AccountMaster::where('cid', $cid)->get();
        $vNoList = TRNDTL::where('v_type', 'CPV')->where('cid', $cid)->pluck('v_no')->unique()->toArray();
        $accountIdList = TRNDTL::where('v_type', 'CPV')->where('cid', $cid)->pluck('account_id')->unique()->toArray();
        

        return \Inertia\Inertia::render('Accounts/Vouchers/VoucherReports', [
            'title' => 'Cash Payment Vouchers (CPV)',
            'vType' => 'CPV',
            'trndtls' => $trndtls,
            'accountMasters' => $accountMasters,
            'vNoList' => $vNoList,
            'accountIdList' => $accountIdList,
            'createRoute' => '/payment/create',
            'editRoutePrefix' => '/payment',
            'deleteRoutePrefix' => '/payment-delete',
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
        // Find the voucher and its entries by voucher number (v_no)
        $voucher = TRNDTL::where('v_no', $v_no)
             ->where('cid', auth()->user()->cid)
            ->where('v_type', 'CPV') // Assuming CRV type for Cash Receipt Voucher
            ->get(); // Fetch all entries for this voucher
        // $voucher2 = TRNDTL::where('v_no', $v_no)
        // ->where('v_type', 'CRV') // Assuming CRV type for Cash Receipt Voucher
        // ->find(); // Fetch all entries for this voucher
        // dd($voucher);
        // Fetch account master data for dropdowns
        $accounts = AccountMaster::where('cid', auth()->user()->cid)->get();
        $customerUrduTitles = Customers::where('cid', auth()->user()->cid)
            ->whereNotNull('urdu_title')
            ->pluck('urdu_title', 'acc_id');

        // Fetch ERP parameters to get account masters related to cash level
        $erpParams = ErpParam::with('level2')->where('cid', auth()->user()->cid)->get();

        $accountMasters = collect();

        if ($erpParams->isNotEmpty()) {
            $cashLevelId = $erpParams->first()->cash_level;
            $accountMasters = AccountMaster::where('level2_id', $cashLevelId)
                ->where('cid', auth()->user()->cid)
                ->get();
        }

        // Pass the voucher and entries to the view
        return view('cash_reports.edit2', compact('voucher', 'accountMasters', 'accounts', 'customerUrduTitles'));
    }

    // Handle the update
   public function update(Request $request, $id)
{
    $user = auth()->user();
    $cid = $user->cid;

    $entries = $request->input('entries', []);

    // Fetch existing entries
    $existingEntries = TRNDTL::where('v_no', $id)
        ->where('v_type', 'CPV')
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
                'debit'       => $entry['debit'],
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
                'debit'      => $entry['debit'],
                'credit'     => 0,
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
        ->route('payment.reports')
        ->with('success', 'CPV updated successfully.');
}


    // Handle the delete
      public function destroy($id)
{
    $trndtl = TRNDTL::where('v_type', 'CPV')
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
    $trndtl = TRNDTL::where('v_type', 'CPV')
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
}