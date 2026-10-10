<?php

namespace App\Http\Controllers;

use App\Models\TRNDTL;
use App\Models\ErpParam;
use App\Models\ItemMaster;
use Illuminate\Http\Request;
use App\Models\AccountMaster;
use App\Models\LaminationReturn;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LaminationReturnController extends Controller
{
  public function index()
  {
    $loggedInUser = Auth::user();
    $items = ItemMaster::all();
    $erpParams = ErpParam::with('level2')->get();
    $accountMasters = collect();
    $accountSuppliers = collect();
    $purchaseAccount = null;
    if ($erpParams->isNotEmpty()) {
      $cashLevelId = $erpParams->first()->cash_level;
      $supplierLevelId = $erpParams->first()->supplier_level;
      $accountMasters = AccountMaster::where('level2_id', $cashLevelId)->get();
      $accountSuppliers = AccountMaster::whereIn('level2_id', [4, 23])->get();
      $purchaseAccountId = $erpParams->first()->purchase_account;
      $purchaseAccount = AccountMaster::find($purchaseAccountId);
    }
    return view('lamination_return.list', get_defined_vars());
  }
  public function store(Request $request)
  {
    $lastEntry = TRNDTL::where('v_type', 'Lamination-Return')
      ->orderBy('id', 'desc')
      ->first();

    if ($lastEntry && is_numeric($lastEntry->v_no)) {
      $lastInvoiceNumber = (int) $lastEntry->v_no;
    } else {
      $lastInvoiceNumber = 0;
    }

    $newInvoiceNumber = $lastInvoiceNumber + 1;
    
    
    // Get ERP parameters
        $erpParam = ErpParam::first();
        if (!$erpParam || !$erpParam->purchase_return_account) {
            return redirect()->back()->with('error', 'Cash Account is not configured in ERP Params.');
        }
        $cashAccountId = $erpParam->purchase_return_account;

    foreach ($request->entries as $index => $entry) {

      $itemCode = DB::table('item_masters')
        ->where('id', $entry['item'] ?? 0)
        ->value('item_code');
      $purchaseGlue = LaminationReturn::create([
        'item_id' => $entry['item'] ?? null,
        'qty' => $entry['quantity'] ?? 0,
        'size' => $entry['size'] ?? 0,
        'rate' => $entry['rate'] ?? 0,
        'amount' => $entry['amount'] ?? 0,
        'vorcher_no' => $newInvoiceNumber,
      ]);

      $trndtl = TRNDTL::create([
        'v_no' => $newInvoiceNumber,
        'date' => Carbon::now(),
        'account_id' => $entry['supplier'] ?? null,
        'preparedby' => $entry['prepared_by'] ?? null,
        'cash_id' => $cashAccountId,
        'debit' => $entry['amount'] ?? null,
        'status' => 'unofficial',
        'credit' => '0', 
        'v_type' => 'Lamination-Return',
        'description' =>
          ($itemCode) . 'x' .
          ($entry['size'] ?? 0) . '"' .
          'x' . ($entry['quantity'] ?? 0) . 'Rolls' .
          '@' . ($entry['rate'] ?? 0),
        'r_id' => $purchaseGlue->id
      ]);

    }

    return redirect()->route('lamination_return.reports')->with('success', 'Lamination-Return ' . $newInvoiceNumber . ' has been saved successfully.');
  }
  public function reports(Request $request)
  {
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $status = $request->input('status');
    $v_no = $request->input('v_no');
    $account_id = $request->input('account_id');

    $query = TRNDTL::where('v_type', 'Lamination-Return')->where('credit', 0)->where('account_id', '!=', 35)->with('leminationpurchases');

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

    $trndtl = $query
      ->orderBy('date', 'desc')

      ->orderBy('v_no', 'desc')
      ->orderBy('id', 'desc')
      ->get();

    $accountMasters = AccountMaster::all();
    $items = ItemMaster::all();


    $vNo = TRNDTL::where('v_type', 'Lamination-Return')->pluck('v_no')->unique()->toArray();
    $accountId = AccountMaster::whereIn('id', TRNDTL::where('v_type', 'Lamination-Return')->pluck('account_id'))
      ->where('title', '!=', 'Purchase Freight') // Exclude "Purchase Freight"
      ->pluck('title', 'id');

    return view('lamination_return.index', [
      'trndtl' => $trndtl,
      'startDate' => $startDate,
      'endDate' => $endDate,
      'status' => $status, // Pass status to view
      'accountMasters' => $accountMasters,
      'vNo' => $vNo,
      'accountId' => $accountId,
    ]);
  }

  public function destroy($id)
  {
    try {
      DB::beginTransaction();

      $laminationReturn = LaminationReturn::findOrFail($id);

      TRNDTL::where('v_type', 'Lamination-Return')
        ->where('r_id', $laminationReturn->id)
        ->delete();

      // Delete the PlateReturn record
      $laminationReturn->delete();

      DB::commit();

      return redirect()->route('lamination_return.reports')
        ->with('success', 'Lamination Return record deleted successfully.');
    } catch (\Exception $e) {
      DB::rollBack();
      return redirect()->route('lamination_return.reports')
        ->with('error', 'Failed to delete record: ' . $e->getMessage());
    }
  }

}
