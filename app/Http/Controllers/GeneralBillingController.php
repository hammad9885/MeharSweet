<?php

namespace App\Http\Controllers;

use App\Models\ItemMaster;
use App\Models\TRNDTL;
use App\Models\ErpParam;
use Illuminate\Http\Request;
use App\Models\AccountMaster;
use App\Models\DyePurchase; 
use App\Models\GeneralJobSheet; 
use App\Models\DeliveryMaster;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\GeneralDeliveryChallen;
use App\Models\GeneralBilling;
use Carbon\Carbon;

class GeneralBillingController extends Controller
{


   public function index()
{
    $loggedInUser = Auth::user();
    $accounts = AccountMaster::whereIn('level2_id', [4, 7])->get();
    return view('general_billing.list', compact('loggedInUser', 'accounts'));
}
    

public function getVoucherNumbers($partyId)
{
   try {
    // Fetch distinct voucher numbers for the given party, ordered ascending
    $vouchers = GeneralDeliveryChallen::where('party_id', $partyId)
        ->whereNotNull('v_no')  // Ensure we exclude NULL values
        ->select('v_no')
        ->distinct()
        ->orderBy('v_no')
        ->pluck('v_no');  // Directly pluck instead of get() + pluck()

    return response()->json($vouchers);
} catch (\Exception $e) {
    // Log the error for debugging (important in production)
    Log::error("Failed to fetch vouchers for party {$partyId}: " . $e->getMessage());
    
    return response()->json([
        'error' => 'Failed to retrieve voucher numbers',
    ], 500);
}
}

public function getVoucherDetails($voucherNo)
{
    try {
        $voucherDetails = GeneralDeliveryChallen::where('v_no', $voucherNo)
            ->select('v_no', 'updated_at', 'party_name', 'gjs_no', 'product_type', 'item_name', 'qty', 'rate', 'freight')
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $voucherDetails
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error fetching voucher details'
        ], 500);
    }
}


// In your controller
public function checkExistingBillings(Request $request)
{
    $query = DB::table('general_billings')
        ->where('v_type', 'GB');
    
    if ($request->has('account_id')) {
        $query->where('account_id', $request->account_id);
    }
    
    if ($request->has('v_no')) {
        $query->where('v_no', $request->v_no);
    }
    
    if ($request->has('v_nos')) {
        $query->whereIn('v_no', (array)$request->v_nos);
    }
    
    return response()->json($query->get(['v_no']));
}















public function store(Request $request)
{
    $validated = $this->validateRequest($request);
    
    DB::beginTransaction();

    try {
        $billingNo = $this->getNextBillingNumber();
        $accountId = $validated['account'];
        $initialBalance = $this->getAccountBalance($accountId); // Get once
        $generalBilling = null;

        foreach ($validated['entries'] as $entry) {
            $generalBilling = $this->createBillingEntry(
                $validated, $entry, $billingNo, $accountId, $initialBalance
            );
        }

        // Use same initial balance for transaction
        $this->createTransactionRecord(
            $validated, $accountId, $generalBilling, $initialBalance
        );
        
        DB::commit();

        return redirect()->route('general_billing.report')->with([
            'success' => 'General Billing created successfully!',
            'billing_no' => $billingNo
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Failed to create General Billing: ' . $e->getMessage());
    }
}

protected function validateRequest(Request $request): array
{
    return $request->validate([
        'date' => 'required|date',
        'prepared_by' => 'required|string',
        'account' => 'required|exists:account_masters,id',
        'v_type' => 'required|string',
        'total_amount' => 'required|numeric',
        'entries' => 'required|array',
        'entries.*.v_no' => 'required|string',
        'entries.*.date' => 'required|date',
        'entries.*.party_name' => 'required|string',
        'entries.*.gjs_no' => 'nullable|string',
        'entries.*.product_type' => 'required|string',
        'entries.*.item_name' => 'required|string',
        'entries.*.qty' => 'required|numeric',
        'entries.*.rate' => 'required|numeric',
        'entries.*.freight' => 'required|numeric',
        'entries.*.amount' => 'required|numeric',
    ]);
}

protected function getNextBillingNumber(): int
{
    $lastBilling = GeneralBilling::orderByDesc('billing_no')->first();
    return $lastBilling ? $lastBilling->billing_no + 1 : 1;
}



// Update the createBillingEntry method to accept preBal parameter
protected function createBillingEntry(array $validated, array $entry, int $billingNo, int $accountId, float $preBal): GeneralBilling
{
    $initials = $this->generatePartyTypeInitials($entry['party_name']);
    $partyNo = $this->getNextPartyNumber($accountId, $entry['party_name']);

    return GeneralBilling::create([
        'billing_no' => $billingNo,
        'date' => $validated['date'],
        'v_no' => $entry['v_no'],
        'v_type' => 'GB',
        'party_name' => $entry['party_name'],
        'party_type' => $initials,
        'party_no' => $partyNo,
        'gjs_no' => $entry['gjs_no'],
        'product_type' => $entry['product_type'],
        'item_name' => $entry['item_name'],
        'qty' => $entry['qty'],
        'rate' => $entry['rate'],
        'freight' => $entry['freight'],
        'amount' => $entry['amount'],
        'total_amount' => $validated['total_amount'],
        'prepared_by' => auth()->user()->name ?? null,
        'account_id' => $accountId,
        'pre_bal' => $preBal // Now using the passed parameter
]);
}

protected function generatePartyTypeInitials(string $partyName): string
{
    $initials = '';
    $words = preg_split('/\s+/', $partyName);
    
    foreach ($words as $word) {
        if (!empty($word)) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
    }
    
    return $initials;
}

protected function getNextPartyNumber(int $accountId, string $partyName): int
{
    static $partyNumbersCache = [];
    
    $cacheKey = $accountId . '_' . $partyName;
    
    if (!isset($partyNumbersCache[$cacheKey])) {
        $lastParty = GeneralBilling::where('account_id', $accountId)
            ->where('party_name', $partyName)
            ->orderByDesc('party_no')
            ->first();
            
        $partyNumbersCache[$cacheKey] = $lastParty ? $lastParty->party_no + 1 : 1;
    }
    
    return $partyNumbersCache[$cacheKey];
}



// Update createTransactionRecord to accept preBal parameter
protected function createTransactionRecord(array $validated, int $accountId, GeneralBilling $generalBilling, float $preBal): void
{
    $saleAccountId = optional(ErpParam::first())->sale_ac;

    TRNDTL::create([
        'v_no' => $generalBilling->party_no,
        'date' => now(),
        'description' => 'General-Bill',
        'account_id' => $accountId,
        'cash_id' => $saleAccountId,
        'preparedby' => auth()->user()->name,
        'credit' => 0,
        'debit' => $validated['total_amount'],
        'status' => 'unofficial',
        'v_type' => 'GB',
        'r_id' => $generalBilling->id,
        'pre_bal' => $preBal,
    ]);
}

protected function getAccountBalance(int $accountId): float
{
    return TRNDTL::where('account_id', $accountId)
        ->get()
        ->reduce(function ($carry, $item) {
            return $carry + (($item->debit ?? 0) - ($item->credit ?? 0));
        }, 0);
}



















public function report(Request $request)
{
   $query = GeneralBilling::with(['account', 'trnDetails']);

// Filter on general_billings columns:
if ($request->has('start_date') && $request->start_date != '') {
    $query->whereDate('created_at', '>=', $request->start_date);
}

if ($request->has('end_date') && $request->end_date != '') {
    $query->whereDate('created_at', '<=', $request->end_date);
}

// Filter on related trnDetails.status column:
if ($request->has('status') && $request->status != '') {
    $query->whereHas('trnDetails', function($q) use ($request) {
        $q->where('status', $request->status);
    });
}

// Other filters on general_billings:
if ($request->has('billing_no') && $request->billing_no != '') {
    $query->where('party_no', $request->billing_no);
}

if ($request->has('product_type') && $request->product_type != '') {
    $query->where('product_type', $request->product_type);
}

if ($request->has('party_name') && $request->party_name != '') {
    $query->where('party_name', $request->party_name);
}

$generalBillings = $query->get();

$showPrevBalance = $request->input('show_prev_balance', '1');
    
return view('general_billing.index', compact('generalBillings', 'showPrevBalance'));
}


public function destroy($id)
{
    DB::beginTransaction();

    try {
        $billing = GeneralBilling::findOrFail($id);
        
        // Delete associated transaction record
        TRNDTL::where('v_type', 'GB')
              ->where('r_id', $billing->id)
              ->delete();
        
        // Delete the billing entry
        $billing->delete();
        
        DB::commit();

        return redirect()->route('general_billing.report')->with([
            'success' => 'General Billing entry deleted successfully!',
            'billing_no' => $billing->billing_no
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Failed to delete General Billing: ' . $e->getMessage());
    }
}


}
