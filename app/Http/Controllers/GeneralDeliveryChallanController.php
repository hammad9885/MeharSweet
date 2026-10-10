<?php

namespace App\Http\Controllers;

use App\Models\ItemType;
use Illuminate\Http\Request;
use App\Models\AccountMaster;
use App\Models\ConfectioneryDetail;
use App\Models\ConfectioneryMaster;
use App\Models\ProductMaster;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ConfectBilling;
use App\Models\ErpParam;
use App\Models\TRNDTL;
use App\Models\GeneralJobSheet;
use App\Models\GeneralDeliveryChallen;

use Carbon\Carbon;

class GeneralDeliveryChallanController extends Controller
{
    public function index()
    {
        $loggedInUser = Auth::user();
        $accounts = AccountMaster::all();
        $items = ItemType::all();
        $product = ProductMaster::all();
        $generals = GeneralJobSheet::all();
       $selectedGjsNos = GeneralDeliveryChallen::pluck('gjs_no')->toArray();

        return view('general_delivery_challan.list',get_defined_vars());
    }
    
  public function getGeneralJobSheetData(Request $request)
{
    try {
        $v_no = $request->input('v_no');
        
        // Fetch the general job sheet data with the account relationship
        $generalJobSheet = GeneralJobSheet::with('account')->where('v_no', $v_no)->first();
        
        if (!$generalJobSheet) {
            return response()->json([
                'success' => false,
                'message' => 'No data found for the selected GJS No.'
            ]);
        }
        
        // Get the account name properly
        $partyName = $generalJobSheet->account ? $generalJobSheet->account->title : '';
        
        $item_name = $generalJobSheet->item_name;
        
        // Format item_name based on product_type (your existing logic)
        switch ($generalJobSheet->product_type) {
            case 'Purchase Boxboard':
                $item_name = $item_name . ' | L:' . $generalJobSheet->length . ' x W:' . $generalJobSheet->width;
                break;
            case 'Purchase Plate':
                $item_name = $item_name . ' | ' . $generalJobSheet->product_name . ' | ' . $generalJobSheet->country_name;
                break;
            case 'Lamination Purchase':
            case 'Corrugation Purchase':
                $item_name = $item_name . ' | ' . $generalJobSheet->size;
                break;
            // Other cases remain the same
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'party_name' => $partyName,
                'item_name' => $item_name,
                'product_type' => $generalJobSheet->product_type,
                'qty' => $generalJobSheet->qty,
                'rate' => $generalJobSheet->rate,
                'account_id' => $generalJobSheet->account_id // Include account_id if needed
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error fetching data: ' . $e->getMessage()
        ]);
    }
}





public function store(Request $request)
{
    // Validate the request data
    $validatedData = $request->validate([
        'v_type' => 'required|string',
        'date' => 'required|date',
        'prepared_by' => 'required|string',
        'gjs_no' => 'required|string|exists:general_job_sheets,v_no',
        'party_name' => 'required|string',
        'product_type' => 'required|string',
        'item_name' => 'required|string',
        'qty' => 'required|numeric',
        'rate' => 'required|numeric',
        'freight' => 'required|numeric|min:0',
    ]);

    try {
        // Get the original job sheet or fail
        $jobSheet = GeneralJobSheet::where('v_no', $validatedData['gjs_no'])->firstOrFail();

        // Generate the next voucher number
        $nextVno = GeneralDeliveryChallen::where('v_type', 'GDC')->max('v_no') + 1 ?? 1;

        // Create the delivery challan
        $deliveryChallan = GeneralDeliveryChallen::create([
            'v_type' => 'GDC',
            'v_no' => $nextVno,
            'date' => $validatedData['date'],
            'prepared_by' => $validatedData['prepared_by'],
            'gjs_no' => $validatedData['gjs_no'],
            'party_id' => $jobSheet->account_id,
            'party_name' => $validatedData['party_name'],
            'product_type' => $validatedData['product_type'],
            'item_name' => $validatedData['item_name'],
            'qty' => $validatedData['qty'],
            'rate' => $validatedData['rate'],
            'freight' => $validatedData['freight'],
        ]);

        // Handle freight transaction if applicable
        if ($validatedData['freight'] > 0) {
            $erpParam = ErpParam::first();
            TRNDTL::create([
                'v_no' => $nextVno,
                'date' => $validatedData['date'],
                'account_id' => $erpParam->sale_freight_exp ?? null,
                'cash_id' => $erpParam->sale_freight ?? null,
                'preparedby' => $validatedData['prepared_by'],
                'credit' =>  $validatedData['freight'],
                'debit' => '0',
                'status' => 'unofficial',
                'v_type' => 'GDC',
                'r_id' => $deliveryChallan->id,
                'description' => 'General-DC Freight',
            ]);
        }

        return redirect()->route('general_delivery_challan.report')
            ->with('success', 'General Delivery Challan created successfully!');

    } catch (ModelNotFoundException $e) {
        return redirect()->back()->with('error', 'Job sheet not found');
    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Error creating delivery challan: ' . $e->getMessage());
    }
}


public function report(Request $request)
{
    $query = GeneralDeliveryChallen::query()->with('account');
    
    // Apply filters
    if ($request->has('start_date') && $request->start_date) {
        $query->whereDate('created_at', '>=', $request->start_date);
    }

    if ($request->has('end_date') && $request->end_date) {
        $query->whereDate('created_at', '<=', $request->end_date);
    }

    if ($request->has('v_no') && $request->v_no) {
        $query->where('v_no', $request->v_no);
    }

    if ($request->has('party_id') && $request->party_id) {  // Changed from account_id to party_id
        $query->where('party_id', $request->party_id);
    }
    
    if ($request->has('employee') && $request->employee) {
        $query->where('employee_type', $request->employee);
    }
    
    // Get only v_nos and party_ids that exist in delivery challans
    $vNos = GeneralDeliveryChallen::distinct()->pluck('v_no');
    $partyIds = GeneralDeliveryChallen::with('account')
        ->select('party_id')  // Changed from account_id to party_id
        ->distinct()
        ->get()
        ->pluck('account.title', 'party_id')  // Changed to use party_id as key
        ->filter();
        
    $generalDeliveryChallens = $query
        ->orderBy('updated_at', 'desc')
        ->orderBy('v_no')
        ->get();
        
    return view('general_delivery_challan.index', compact(
        'generalDeliveryChallens', 
        'vNos', 
        'partyIds'
    ));
}


public function destroy($id)
{
    try {
        // Find the delivery challan by ID with its associated transactions
        $deliveryChallan = GeneralDeliveryChallen::findOrFail($id);
        
        // Get the voucher details before deletion
        $vNo = $deliveryChallan->v_no;
        $vType = $deliveryChallan->v_type;
        
        // Delete associated freight transactions first
        TRNDTL::where([
            'v_type' => 'GDC',
            'v_no' => $vNo,
            'r_id' => $id,
            'description' => 'General-DC Freight'
        ])->delete();
        
        // Delete the delivery challan
        $deliveryChallan->delete();
        
        return redirect()->route('general_delivery_challan.report')
            ->with('success', 'Delivery Challan and associated records deleted successfully!');
    
    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Error deleting delivery challan: ' . $e->getMessage());
    }
}

public function edit($id)
{
    $deliveryChallan = GeneralDeliveryChallen::findOrFail($id);
    return view('general_delivery_challan.edit', compact('deliveryChallan'));
}
        
        
      public function update(Request $request, $id)
{
    $validatedData = $request->validate([
        'freight' => 'required|numeric|min:0',
    ]);

    try {
        // Find the delivery challan
        $deliveryChallan = GeneralDeliveryChallen::findOrFail($id);
        
        // Update the delivery challan
        $deliveryChallan->update([
            'freight' => $validatedData['freight'],
        ]);

        // Handle freight transaction
        $erpParam = ErpParam::first();
        $existingFreightTransaction = TRNDTL::where([
            'v_type' => 'GDC',
            'v_no' => $deliveryChallan->v_no,
            'r_id' => $deliveryChallan->id,
            'description' => 'General-DC Freight'
        ])->first();

        if ($validatedData['freight'] > 0) {
            $transactionData = [
                'date' => Carbon::now(),
                'account_id' => $erpParam->sale_freight_exp ?? null,
                'cash_id' => $erpParam->sale_freight ?? null,
                'preparedby' => $deliveryChallan->prepared_by,
                'credit' =>   $validatedData['freight'],
                'debit' => '0' ,
                'status' => 'unofficial',
                'description' => 'General-DC Freight'
            ];

            if ($existingFreightTransaction) {
                // Update existing transaction
                $existingFreightTransaction->update($transactionData);
            } else {
                // Create new transaction
                TRNDTL::create(array_merge([
                    'v_no' => $deliveryChallan->v_no,
                    'v_type' => 'GDC',
                    'r_id' => $deliveryChallan->id,
                ], $transactionData));
            }
        } elseif ($existingFreightTransaction) {
            // Remove transaction if freight is 0 or negative
            $existingFreightTransaction->delete();
        }

        return redirect()->route('general_delivery_challan.report')
            ->with('success', 'Freight updated successfully!');

    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Error updating freight: ' . $e->getMessage());
    }
}

}
