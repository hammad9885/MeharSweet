<?php

namespace App\Http\Controllers;

use App\Models\ItemMaster;
use App\Models\TRNDTL;
use App\Models\ErpParam;
use Illuminate\Http\Request;
use App\Models\AccountMaster;
use App\Models\DyePurchase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DyePurchaseController extends Controller
{
    // List page
    public function index()
    {
        $loggedInUser = Auth::user();
        $accounts = AccountMaster::whereIn('level2_id', [4, 23])->get();
        $saleAccounts = AccountMaster::all();
        $items = ItemMaster::all();

        return view('dye_purchase.list', compact('loggedInUser', 'items', 'accounts', 'saleAccounts'));
    }

    // Store new voucher
    public function store(Request $request)
    {
        $request->validate([
            'entries' => 'required|array',
            'entries.*.date' => 'required|date',
            'entries.*.account' => 'required|exists:account_masters,id',
            'entries.*.item' => 'required|exists:item_masters,id',
            'entries.*.description' => 'nullable|string',
            'entries.*.amount' => 'required|numeric|min:0',
            'entries.*.qty' => 'required|numeric|min:0',
            'entries.*.file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $lastEntry = DyePurchase::orderBy('v_no', 'desc')->first();
            $newVoucherNo = $lastEntry ? (int)$lastEntry->v_no + 1 : 1;

            $erpParam = ErpParam::first();
            if (!$erpParam || !$erpParam->purchase_account) {
                return redirect()->back()->with('error', 'Cash Account is not configured in ERP Params.');
            }
            $cashAccountId = $erpParam->purchase_account;

            $uploadPath = public_path('storage/uploads');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            foreach ($request->entries as $key => $entry) {
                $filePath = null;
                $fileName = null;

                if ($request->hasFile("entries.$key.file")) {
                    $file = $request->file("entries.$key.file");
                    $fileName = 'voucher_' . $newVoucherNo . '_' . time() . '_' . $file->getClientOriginalName();
                    $file->move($uploadPath, $fileName);
                    $filePath = 'uploads/' . $fileName;
                }

                $dyePurchase = DyePurchase::create([
                    'v_no' => $newVoucherNo,
                    'amount' => $entry['amount'],
                    'qty' => $entry['qty'],
                    'item_code' => $entry['item'],
                    'description' => $entry['description'] ?? null,
                    'file_path' => $filePath,
                    'file_name' => $fileName,
                ]);

                TRNDTL::create([
                    'v_no' => $newVoucherNo,
                    'date' => $entry['date'],
                    'description' => $entry['description'] ?? null,
                    'preparedby' => $entry['prepared_by'] ?? auth()->user()->name,
                    'account_id' => $entry['account'],
                    'cash_id' => $cashAccountId,
                    'credit' => $entry['amount'],
                    'debit' => 0,
                    'status' => 'unofficial',
                    'v_type' => 'DPN',
                    'r_id' => $dyePurchase->id,
                ]);
            }

            DB::commit();
            return redirect()->route('dye_purchases.reports')->with([
                'success' => 'Voucher No. ' . $newVoucherNo . ' has been saved successfully.',
                'voucher_no' => $newVoucherNo
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Dye Purchase Store Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while saving the voucher: ' . $e->getMessage());
        }
    }

    // Report list
    public function reports(Request $request)
    {
        $query = TRNDTL::where('v_type', 'DPN')
            ->where('debit', 0)
            ->where('account_id', '!=', 35)
            ->with('dyepurchases');

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

        $trndtl = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->get();

        $accountMasters = AccountMaster::all();
        $vNo = TRNDTL::where('v_type', 'DPN')->pluck('v_no')->unique()->toArray();
        $accountId = AccountMaster::whereIn('id', TRNDTL::where('v_type', 'DPN')->pluck('account_id'))
            ->where('title', '!=', 'Purchase Freight')
            ->pluck('title', 'id');

        return view('dye_purchase.index', compact('trndtl', 'accountMasters', 'vNo', 'accountId'))
            ->with([
                'startDate' => $request->start_date,
                'endDate' => $request->end_date,
                'status' => $request->status,
            ]);
    }

    // Edit voucher
   public function edit($v_no)
{
    $loggedInUser = Auth::user();
    
    // Fetch voucher details with related account and item
    $voucher = TRNDTL::where('v_no', $v_no)
        ->where('v_type', 'DPN')
        ->where('debit', 0)
        ->where('account_id', '!=', 35)
        ->with(['accounts', 'dyepurchases.items'])
        ->get();

    $accounts = AccountMaster::whereIn('level2_id', [4, 23])->get();
    $saleAccounts = AccountMaster::all();
    $items = ItemMaster::all();

    return view('dye_purchase.edit', compact('loggedInUser', 'voucher', 'items', 'accounts', 'saleAccounts'));
}


    // Update voucher
    public function update(Request $request, $v_no)
    {
        $request->validate([
            'entries' => 'required|array',
            'entries.*.date' => 'required|date',
            'entries.*.account' => 'required|exists:account_masters,id',
            'entries.*.item' => 'required|exists:item_masters,id',
            'entries.*.description' => 'nullable|string',
            'entries.*.amount' => 'required|numeric|min:0',
            'entries.*.qty' => 'required|numeric|min:0',
            'entries.*.file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $erpParam = ErpParam::first();
            if (!$erpParam || !$erpParam->purchase_account) {
                return redirect()->back()->with('error', 'Cash Account is not configured in ERP Params.');
            }
            $cashAccountId = $erpParam->purchase_account;

            // Delete existing DyePurchase & TRNDTL records for this voucher
            $existingTrndtlIds = TRNDTL::where('v_no', $v_no)->where('v_type', 'DPN')->pluck('r_id')->toArray();
            TRNDTL::whereIn('r_id', $existingTrndtlIds)->delete();
            DyePurchase::whereIn('id', $existingTrndtlIds)->delete();

            $uploadPath = public_path('storage/uploads');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            foreach ($request->entries as $key => $entry) {
                $filePath = null;
                $fileName = null;

                if ($request->hasFile("entries.$key.file")) {
                    $file = $request->file("entries.$key.file");
                    $fileName = 'voucher_' . $v_no . '_' . time() . '_' . $file->getClientOriginalName();
                    $file->move($uploadPath, $fileName);
                    $filePath = 'uploads/' . $fileName;
                }

                $dyePurchase = DyePurchase::create([
                    'v_no' => $v_no,
                    'amount' => $entry['amount'],
                    'qty' => $entry['qty'],
                    'item_code' => $entry['item'],
                    'description' => $entry['description'] ?? null,
                    'file_path' => $filePath,
                    'file_name' => $fileName,
                ]);

                TRNDTL::create([
                    'v_no' => $v_no,
                    'date' => $entry['date'],
                    'description' => $entry['description'] ?? null,
                    'preparedby' => $entry['prepared_by'] ?? auth()->user()->name,
                    'account_id' => $entry['account'],
                    'cash_id' => $cashAccountId,
                    'credit' => $entry['amount'],
                    'debit' => 0,
                    'status' => 'unofficial',
                    'v_type' => 'DPN',
                    'r_id' => $dyePurchase->id,
                ]);
            }

            DB::commit();
            return redirect()->route('dye_purchases.reports')->with('success', 'Voucher No. ' . $v_no . ' has been updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Dye Purchase Update Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while updating the voucher: ' . $e->getMessage());
        }
    }

    // Delete single record
    public function destroy($id)
    {
        $trndtl = TRNDTL::find($id);
        if (!$trndtl) {
            return redirect()->back()->with('error', 'Record not found.');
        }

        if ($trndtl->v_type == 'DPN') {
            TRNDTL::where('r_id', $trndtl->r_id)->where('v_type', 'DPN')->delete();
            DyePurchase::find($trndtl->r_id)?->delete();
        } else {
            $trndtl->delete();
        }

        return redirect()->back()->with('success', 'Record deleted successfully.');
    }

    public function delete($id)
    {
        return $this->destroy($id);
    }

    // Edit dye freight
    public function editDye($v_no)
    {
        $freightData = TRNDTL::where('v_no', $v_no)
                             ->where('v_type', 'DPN')
                             ->where('description', 'freight')
                             ->first();

        $freight = $freightData ? $freightData->credit : 0;
        $purchaseDetail = DyePurchase::where('v_no', $v_no)->first();
        $freight_type = $purchaseDetail ? $purchaseDetail->freight_type : null;
        $totalQty = DyePurchase::where('v_no', $v_no)->sum('amount');

        return view('dye_purchase.editDye', compact('freight', 'v_no', 'totalQty', 'freight_type'));
    }

    // Update dye freight
    public function updateDye(Request $request, $v_no)
    {
        $request->validate([
            'total_freight' => 'required|numeric|min:0',
            'freight_type' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $erpParam = ErpParam::firstOrFail();
            $cashAccId = $erpParam->cash_acc;
            $Purfreight = $erpParam->pur_freight;
            $PurfreightExp = $erpParam->pur_freight_exp;

            $existingFreight = TRNDTL::where('v_no', $v_no)
                                      ->where('v_type', 'DPN')
                                      ->where('description', 'Freight')
                                      ->first();

            $existingPurchaseDetail = DyePurchase::where('v_no', $v_no)->first();

            if ($request->total_freight > 0) {
                if ($existingPurchaseDetail) {
                    $existingPurchaseDetail->update([
                        'freight' => $request->total_freight,
                        'freight_type' => $request->freight_type,
                    ]);
                    $purchaseDetailId = $existingPurchaseDetail->id;
                } else {
                    $purchaseDetail = DyePurchase::create([
                        'v_no' => $v_no,
                        'freight' => $request->total_freight,
                        'freight_type' => $request->freight_type,
                    ]);
                    $purchaseDetailId = $purchaseDetail->id;
                }

                if ($existingFreight) {
                    $existingFreight->update([
                        'credit' => $request->total_freight,
                        'preparedby' => Auth::user()->name ?? null,
                        'date' => Carbon::now(),
                        'r_id' => $purchaseDetailId,
                    ]);
                } else {
                    TRNDTL::create([
                        'v_no' => $v_no,
                        'date' => Carbon::now(),
                        'account_id' => $PurfreightExp,
                        'cash_id' => $Purfreight,
                        'preparedby' => Auth::user()->name ?? null,
                        'credit' => $request->total_freight,
                        'debit' => 0,
                        'status' => 'unofficial',
                        'v_type' => 'DPN',
                        'description' => 'Freight',
                        'r_id' => $purchaseDetailId,
                    ]);
                }
            } else {
                $existingFreight?->delete();
                $existingPurchaseDetail?->update(['freight' => 0]);
            }

            DB::commit();
            return redirect()->route('dye_purchases.reports')->with('success', 'Freight updated successfully for DPN-' . $v_no);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('dye_purchases.reports')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
