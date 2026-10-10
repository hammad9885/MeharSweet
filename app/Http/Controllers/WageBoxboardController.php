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
use App\Models\WageBoxboard;
use Carbon\Carbon;

class WageBoxboardController extends Controller
{


   public function index()
{
    $loggedInUser = Auth::user();
   $employees = DB::table('wage_boxboard')
                ->get();
    return view('wage.boxboard.list', compact('loggedInUser', 'employees'));
}
    

public function getVouchersByEmployee($employee_id)
{
   try {
    $vouchers = DB::table('wage_boxboard')
        ->where('employee_id', $employee_id)
        ->whereNotNull('v_no')  // Exclude NULL values
        ->select('v_no')
        ->distinct()
        ->orderBy('v_no')  // Optional: adds predictable sorting
        ->pluck('v_no');  // Directly get the values instead of full objects

    return response()->json($vouchers->values());  // Ensure sequential array keys
} catch (\Exception $e) {
    \Log::error("Failed to fetch vouchers for employee {$employee_id}: " . $e->getMessage());
    return response()->json([
        'error' => 'Failed to retrieve voucher numbers',
    ], 500);
}
}


// BoxboardWageController.php

public function getVoucherDetails($employee_id, $v_no)
{
    $voucherDetails = DB::table('wage_boxboard')
        ->where('employee_id', $employee_id)
        ->where('v_no', $v_no)
        ->get();

    return response()->json($voucherDetails);
}


public function store(Request $request)
{
    // Validate the main fields
    $validated = $request->validate([
        'date' => 'required|date',
        'prepared_by' => 'required|string|max:255',
        'account_id' => 'required|integer',
        'v_no' => 'required|array',
        'v_no.*' => 'required|string|max:255',
        'employee_name' => 'required|array',
        'employee_name.*' => 'required|string|max:255',
        'process_name' => 'required|array',
        'process_name.*' => 'required|string|max:255',
        'process_rate' => 'required|array',
        'process_rate.*' => 'required|numeric|min:0',
        'packets' => 'required|array',
        'packets.*' => 'required|numeric|min:0',
        'amount' => 'required|array',
        'amount.*' => 'required|numeric|min:0',
    ]);

    try {
        DB::transaction(function () use ($request) {
            // Format the account_id to 4 digits with leading zeros
            $formattedAccountCode = str_pad($request->account_id, 4, '0', STR_PAD_LEFT);
            
            // Find the account by account_code
            $account = AccountMaster::where('account_code', $formattedAccountCode)->first();
            
            if (!$account) {
                throw new \Exception("Account with code {$formattedAccountCode} not found in account masters");
            }
            
            $accountId = $account->id;
            
            // Calculate total amount from all entries
            $totalAmount = array_sum($request->amount);
            
            // Get the last invoice number and increment
            $lastEntry = WageBoxboard::orderBy('id', 'desc')->first();
            $newInvoiceNumber = $lastEntry ? ((int) $lastEntry->b_no + 1) : 1;
            
            // Get the salary account from ERP parameters
            $erpParam = ErpParam::first(); 
            $saleAccountId = $erpParam ? $erpParam->salary_level : null;
            
            $wageBoxboards = null;
            
            // Create wage boxboard entries for each employee
            foreach ($request->v_no as $index => $vNo) {
                $wageBoxboards = WageBoxboard::create([
                    'b_no' => $newInvoiceNumber,
                    'v_no' => $vNo,
                    'employee_name' => $request->employee_name[$index],
                    'process_name' => $request->process_name[$index],
                    'process_rate' => $request->process_rate[$index],
                    'packets' => $request->packets[$index],
                    'boxboard_wage' => $request->amount[$index],
                    'total_amount' => $totalAmount,
                    'account_id' => $accountId,
                    'date' => $request->date,
                    'prepared_by' => $request->prepared_by,
                    'v_type' => 'Salary',
                ]);
            }
            
            // Create transaction record
            TRNDTL::create([
                'v_no' => $newInvoiceNumber,
                'date' => $request->date,
                'description' => 'E-Salary',
                'account_id' => $accountId,
                'cash_id' => $saleAccountId,
                'preparedby' => auth()->user()->name,
                'credit' => $totalAmount,
                'debit' => 0,
                'status' => 'unofficial',
                'v_type' => 'Salary',
                'r_id' => $wageBoxboards->id,
            ]);
        });

        return redirect()->route('boxboard_wage.report')->with('success', 'Wage entries stored successfully.');
    } catch (\Exception $e) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Error storing wage entries: ' . $e->getMessage());
    }
}

public function report(Request $request)
{
    $validated = $request->validate([
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'status' => 'nullable|string'
    ]);

    $query = WageBoxboard::query();
    
    if (!empty($validated['start_date'])) {
        $query->whereDate('created_at', '>=', $validated['start_date']);
    }
    
    if (!empty($validated['end_date'])) {
        $query->whereDate('created_at', '<=', $validated['end_date']);
    }
    
    if (!empty($validated['status'])) {
        $query->where('status', $validated['status']);
    }
    
    $wageBoxboards = $query->get();
    
    return view('wage.boxboard.index', compact('wageBoxboards'));
}

public function destroy($id)
{
    try {
        DB::transaction(function () use ($id) {
            // Find the wage boxboard entry
            $wageBoxboard = WageBoxboard::findOrFail($id);
            
            // Get the b_no (invoice number) before deleting
            $b_no = $wageBoxboard->b_no;
            
            // Delete all wage boxboard entries with the same b_no
            WageBoxboard::where('b_no', $b_no)->delete();
            
            // Delete the associated transaction record
            TRNDTL::where('v_no', $b_no)
                  ->where('v_type', 'Salary')
                  ->delete();
        });

        return redirect()->route('boxboard_wage.report')->with('success', 'Wage entries deleted successfully.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error deleting wage entries: ' . $e->getMessage());
    }
}

}
