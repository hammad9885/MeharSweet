<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Module;
use App\Models\TRNDTL;
use App\Models\ErpParam;
use App\Models\BankRecipt;
use App\Models\Customers;
use Illuminate\Http\Request;
use App\Models\AccountMaster;
use App\Models\UserPermission;
use Illuminate\Support\Facades\DB;

class BankReciptController extends Controller
{
    public function index()
    {
        
        // $erp_params = ErpParam::all();
        $accounts = AccountMaster::where('cid', auth()->user()->cid)->get();
$customerUrduTitles = Customers::where('cid', auth()->user()->cid)
            ->whereNotNull('urdu_title')
            ->pluck('urdu_title', 'acc_id');

        $erpParams = ErpParam::with('level2') ->where('cid', auth()->user()->cid)->get();

        // Initialize accountMasters as an empty collection to avoid errors
        $accountMasters = collect();

        // Check if there is at least one ERP Param and that cash_level is set
        if ($erpParams->isNotEmpty()) {
            // Get the cash_level from the first ERP Param
            $cashLevelId = $erpParams->first()->bank_level;

            // Fetch AccountMasters associated with the cash_level
            $accountMasters = AccountMaster::where('level2_id', $cashLevelId)
                ->where('cid', auth()->user()->cid)
                ->get();
        }
        return view('bank_recipt.list',get_defined_vars());
    }
   public function store(Request $request)
{
    $request->validate([
        'v_type' => 'required|string',
        'entries' => 'required|array',
        'voucher_date' => 'required|date',
        'entries.*.cash' => 'required|string',
        'entries.*.account' => 'required|string',
        'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Changed to match reference
        'entries.*.credit' => 'required|numeric',

    ]);

    $user = auth()->user();
    $cid = $user->cid ?? null;
    DB::beginTransaction();
    try {
        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->move(public_path('storage/uploads'), $fileName);
            $filePath = 'uploads/' . $fileName;
        }
        $lastInvoiceNumber = TRNDTL::where('v_type', 'BRV')->where('cid', $cid)->max('v_no');
        $newInvoiceNumber = $lastInvoiceNumber ? ((int) $lastInvoiceNumber + 1) : 1;
        foreach ($request->entries as $entry) {
            TRNDTL::create([
                'v_no' => $newInvoiceNumber,
                'v_type' => 'BRV',
                'date' => $request->voucher_date,
                'cash_id' => $entry['cash'],
                'account_id' => $entry['account'],
                'description' => !empty($entry['description']) ? $entry['description'] : null,


                'credit' => $entry['credit']??0,
                'debit' => '0',
                'status' => 'N',
                'preparedby' => $user->name,
                'file_id' => $filePath,
                'cid' => $cid,
            ]);
        }
        DB::commit();
        return redirect()->route('bank_recipt.reports')->with('success', '' . $request->v_type . '-' . $newInvoiceNumber . ' has been saved successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'An error occurred while saving the voucher: ' . $e->getMessage());
    }
}
    
   public function reports(Request $request)
{
      
    // Get the filter parameters from the request
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $status = $request->input('status');
    $v_no = $request->input('v_no');
    $account_id = $request->input('account_id');

    $user = auth()->user();
    $cid = $user->cid ?? null;
    $query = TRNDTL::with('accounts')->where('v_type', 'BRV')->where('cid', $cid);
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
    $trndtls = $query->orderBy('date', 'desc')
                     ->orderBy('v_no', 'desc')
                     ->get();
    $accountMasters = AccountMaster::where('cid', $cid)->get();
    $vNoList = TRNDTL::where('v_type', 'BRV')->where('cid', $cid)->pluck('v_no')->unique()->toArray();
    $accountIdList = TRNDTL::where('v_type', 'BRV')->where('cid', $cid)->pluck('account_id')->unique()->toArray();
    return \Inertia\Inertia::render('Accounts/Vouchers/VoucherReports', [
        'title' => 'Bank Receipt Vouchers (BRV)',
        'vType' => 'BRV',
        'trndtls' => $trndtls,
        'accountMasters' => $accountMasters,
        'vNoList' => $vNoList,
        'accountIdList' => $accountIdList,
        'createRoute' => '/bank_recipt',
        'editRoutePrefix' => '/bank_recipt',
        'deleteRoutePrefix' => '/bank_recipt-delete',
        'filters' => [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'status' => $status,
        ],
    ]);
}

    public function edit($v_no)
{
     
    // Find the voucher and its entries by voucher number (v_no)
    $voucher = TRNDTL::where('v_no', $v_no)
    ->where('cid', auth()->user()->cid)
                ->where('v_type', 'BRV') // Assuming CRV type for Cash Receipt Voucher
                ->get(); // Fetch all entries for this voucher
                // $voucher2 = TRNDTL::where('v_no', $v_no)
                // ->where('v_type', 'CRV') // Assuming CRV type for Cash Receipt Voucher
                // ->find(); // Fetch all entries for this voucher

    // Fetch account master data for dropdowns
    $accounts = AccountMaster::where('cid', auth()->user()->cid)->get();

    $customerUrduTitles = Customers::where('cid', auth()->user()->cid)
        ->whereNotNull('urdu_title')
        ->pluck('urdu_title', 'acc_id');

    // Fetch ERP parameters to get account masters related to cash level
    $erpParams = ErpParam::with('level2')->where('cid', auth()->user()->cid)->get();

    $accountMasters = collect();

    if ($erpParams->isNotEmpty()) {
        $cashLevelId = $erpParams->first()->bank_level;
        $accountMasters = AccountMaster::where('level2_id', $cashLevelId)
            ->where('cid', auth()->user()->cid)
            ->get();
    }

    // Pass the voucher and entries to the view
    return view('bank_reports.edit2', compact('voucher', 'accountMasters', 'accounts', 'customerUrduTitles'));
}



// Handle the update
 public function update(Request $request, $id)
{
    $user = auth()->user();
    $cid = $user->cid;

    $entries = $request->input('entries', []);

    // Fetch existing entries
    $existingEntries = TRNDTL::where('v_no', $id)
        ->where('v_type', 'BRV')
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
        ->route('bank_recipt.reports')
        ->with('success', 'BRV updated successfully.');
}



// Handle the delete

public function destroy($id)
{
    
    $trndtl = TRNDTL::where('v_type', 'BRV')
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

    return redirect()->route('bank_recipt.reports')->with('success', 'Transaction and file deleted successfully!');
}

public function delete($id)
{
    $trndtl = TRNDTL::where('v_type', 'BRV')
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

    return redirect()->route('bank_recipt.reports')->with('success', 'Transaction and file deleted successfully!');
}


}
