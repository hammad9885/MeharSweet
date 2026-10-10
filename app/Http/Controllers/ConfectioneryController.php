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
use Carbon\Carbon;

class ConfectioneryController extends Controller
{
    public function index()
    {
        $loggedInUser = Auth::user();
        $accounts = AccountMaster::all();
        $items = ItemType::all();
        $product = ProductMaster::all();
        $data = ConfectioneryMaster::all();

        return view('sales.confectionery.list', get_defined_vars());
    }

    public function getAid($accountId)
    {
        $product = ProductMaster::where('aid', $accountId)->first();
        if ($product) {
            return response()->json(['aid' => $product->aid, 'hasAid' => true]);
        } else {
            return response()->json(['aid' => null, 'hasAid' => false]);
        }
    }

   public function store(Request $request)
{
    $lastInvoiceNumber = ConfectioneryMaster::max('v_no');
    $newInvoiceNumber = $lastInvoiceNumber ? ((int) $lastInvoiceNumber + 1) : 1;

    // FIXED: Initialize sequence counter to start from 1
    $sequenceCounter = 1;

    foreach ($request->entries as $i => $entry) {
        // FIXED: Use sequenceCounter instead of array index + 1
        $sequenceNo = $sequenceCounter;

        $confectioneryDetail = ConfectioneryDetail::create([
            'v_no' => $newInvoiceNumber,
            'sequence_no' => $sequenceNo,
            'item_code' => $entry['item'] ?? null,
            'product_name' => $entry['product'] ?? null,
            'po_no' => $entry['po_no'] ?? 0,
            'box' => $entry['box'] ?? 0,
            'pack_qty' => $entry['packing'] ?? 0,
            'total' => $entry['total'] ?? 0,
            'freight' => $entry['freight'] ?? 0,
        ]);

        ConfectioneryMaster::create([
            'v_no' => $newInvoiceNumber,
            'sequence_no' => $sequenceNo,
            'date' => Carbon::now(),
            'account_id' => $entry['supplier'] ?? null,
            'preparedby' => $entry['prepared_by'] ?? null,
            'status' => 'unofficial',
            'v_type' => 'CDC',
            'confectionery_detail_id' => $confectioneryDetail->id,
        ]);

        $erpParam = ErpParam::first();
        $Salefreight = $erpParam ? $erpParam->sale_freight : null;
        $SalefreightExp = $erpParam ? $erpParam->sale_freight_exp : null;

        if (($entry['freight'] ?? 0) > 0) {
            TRNDTL::create([
                'v_no' => $newInvoiceNumber,
                'date' => Carbon::now(),
                'account_id' => $SalefreightExp,
                'cash_id' => $Salefreight,
                'preparedby' => $entry['prepared_by'] ?? null,
                'credit' => 0,
                'debit' => $entry['freight'] ?? null,
                'status' => 'unofficial',
                'v_type' => 'CDC',
                'r_id' => $confectioneryDetail->id,
                'description' => 'Freight',
            ]);
        }

        // FIXED: Increment sequence counter for next entry
        $sequenceCounter++;
    }

    return redirect()->route('confectionery.reports')
        ->with('success', "Voucher No. $newInvoiceNumber has been created successfully.");
}

    public function reports(Request $request)
    {
        $products = ProductMaster::all();
        $itemCodes = ConfectioneryDetail::distinct()->pluck('item_code');
        $account = ConfectioneryMaster::distinct()->pluck('account_id');
        $items = ItemType::whereIn('id', $itemCodes)->get();
        $accounts = AccountMaster::whereIn('id', $account)->get();

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status');
        $itemId = $request->input('item');
        $accountId = $request->input('account');
        $po = $request->input('po_no');
        $v_no = $request->input('v_no');

        $poNumbers = ConfectioneryDetail::distinct()->pluck('po_no');
        $query = ConfectioneryMaster::with('confectioneryDetails');

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }
        if ($status) {
            $query->where('status', $status);
        }
        if ($itemId) {
            $query->whereHas('confectioneryDetails', function ($q) use ($itemId) {
                $q->where('item_code', $itemId);
            });
        }
        if ($po) {
            $query->whereHas('confectioneryDetails', function ($q) use ($po) {
                $q->where('po_no', $po);
            });
        }
        if ($v_no) {
            $query->whereHas('confectioneryDetails', function ($q) use ($v_no) {
                $q->where('v_no', $v_no);
            });
        }
        if ($accountId) {
            $query->whereHas('confectioneryDetails', function ($q) use ($accountId) {
                $q->where('account_id', $accountId);
            });
        }

        $trndtl = $query->orderBy('date', 'desc')
            ->orderBy('v_no', 'desc')
            ->orderBy('sequence_no', 'asc')
            ->get();

        $accountMasters = AccountMaster::all();
        $vNoList = ConfectioneryMaster::pluck('v_no')->unique()->toArray();

        return view('sale_reports.index5', [
            'trndtl' => $trndtl,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'status' => $status,
            'accountMasters' => $accountMasters,
            'items' => $items,
            'products' => $products,
            'itemId' => $itemId,
            'po' => $po,
            'vNoList' => $vNoList,
            'poNumbers' => $poNumbers,
            'accounts' => $accounts,
        ]);
    }

public function edit($v_no)
{
    $loggedInUser = Auth::user();
    $voucher = ConfectioneryMaster::where('v_no', $v_no)->get();

    // Remove rate pre-filling logic
    // $rates = [];
    // $billed = [];
    // foreach ($voucher as $row) {
    //     $detail = $row->confectioneryDetails;
    //     if ($detail) {
    //         // First try to get rate from ProductMaster based on product name
    //         $rate = ProductMaster::where('prod_name', $detail->prod_name)
    //                             ->value('rate');
    //
    //         // If no rate found and we have item_code, try finding by item_code
    //         if (!$rate && $detail->item_code) {
    //             $rate = ProductMaster::where('item_id', $detail->item_id)
    //                                 ->value('rate');
    //         }
    //
    //         // Fallback to existing ConfectBilling rate if product rate not found
    //         if (!$rate) {
    //             $rate = ConfectBilling::where('old_vno', $v_no)
    //                 ->when($detail->product_name, function ($q) use ($detail) {
    //                     $q->where('product_name', $detail->product_name);
    //                 })
    //                 ->when($detail->item_code, function ($q) use ($detail) {
    //                     $q->where('item', $detail->item_code);
    //                 })
    //                 ->value('rate');
    //         }
    //
    //         $rates[$detail->id] = $rate ?? 0;
    //         
    //         // Check if detail has related bill
    //         $hasBill = ConfectBilling::where('old_vno', $v_no)
    //             ->when($detail->product_name, function ($q) use ($detail) {
    //                 $q->where('product_name', $detail->product_name);
    //             })
    //             ->when($detail->item_code, function ($q) use ($detail) {
    //                 $q->where('item', $detail->item_code);
    //             })
    //             ->when($detail->po_no, function ($q) use ($detail) {
    //                 $q->where('po_no', $detail->po_no);
    //             })
    //             ->exists();
    //         $billed[$detail->id] = $hasBill;
    //     }
    // }

    // Overall flag for this voucher
    $hasBilling = ConfectBilling::where('old_vno', $v_no)->exists();

    // Fetch data for dropdowns
    $accounts = AccountMaster::all();
    $items = ItemType::all();
    $products = ProductMaster::all();

    return view('sale_reports.edit5', get_defined_vars());
}

  
public function update(Request $request, $id)
{
    $entries = $request->input('entries', []);

    if (!is_array($entries)) {
        return back()->withErrors(['entries' => 'Entries must be an array.']);
    }

    $erpParam = ErpParam::first();
    $saleFreight = $erpParam ? $erpParam->sale_freight : null;
    $saleFreightExp = $erpParam ? $erpParam->sale_freight_exp : null;

    // Handle deleted rows
    $deletedIds = $request->input('deleted_ids', []);
    if (is_array($deletedIds) && !empty($deletedIds)) {
        foreach ($deletedIds as $deletedId) {
            $detail = ConfectioneryDetail::find($deletedId);
            if ($detail) {
                // Server-side guard: prevent deleting if a related ConfectBilling exists
                $hasRelatedBill = ConfectBilling::where('old_vno', $id)
                    ->when($detail->product_name, function ($q) use ($detail) {
                        $q->where('product_name', $detail->product_name);
                    })
                    ->when($detail->item_code, function ($q) use ($detail) {
                        $q->where('item', $detail->item_code);
                    })
                    ->when($detail->po_no, function ($q) use ($detail) {
                        $q->where('po_no', $detail->po_no);
                    })
                    ->exists();

                if ($hasRelatedBill) {
                    return back()->with('error', 'To delete this voucher entry, delete its billing first.');
                }

                ConfectioneryMaster::where('confectionery_detail_id', $detail->id)
                    ->where('v_no', $id)
                    ->delete();

                TRNDTL::where('r_id', $detail->id)
                    ->where('v_no', $id)
                    ->where('v_type', 'CDC')
                    ->delete();

                ConfectBilling::where('old_vno', $id)
                    ->when($detail->product_name, fn($q) => $q->where('product_name', $detail->product_name))
                    ->when($detail->item_code, fn($q) => $q->where('item', $detail->item_code))
                    ->delete();

                $detail->delete();
            }
        }
    }

    // FIXED: After deletions, renumber ALL existing entries to ensure continuous sequence
    $existingDetails = ConfectioneryDetail::where('v_no', $id)
        ->orderBy('sequence_no', 'asc')
        ->get();
    
    $sequenceCounter = 1;
    foreach ($existingDetails as $detail) {
        $detail->update(['sequence_no' => $sequenceCounter]);
        
        // Also update the corresponding ConfectioneryMaster sequence_no
        ConfectioneryMaster::where('confectionery_detail_id', $detail->id)
            ->where('v_no', $id)
            ->update(['sequence_no' => $sequenceCounter]);
            
        $sequenceCounter++;
    }

    // Now set the starting sequence for new entries
    $newSequenceCounter = $sequenceCounter; // Continue from where existing entries left off

    // Grand Total accumulator
    $grandTotal = 0;
    $hasBilling = ConfectBilling::where('old_vno', $id)->exists();

    foreach ($entries as $key => $entry) {
        $entryDate = $entry['date'] ?? now();
        $entryFreight = floatval($entry['freight'] ?? 0);
        $entryBox = intval($entry['box'] ?? 0);
        $entryPacking = intval($entry['packing'] ?? 0);
        $entryPO = $entry['po_no'] ?? '';
        
        // Always fetch rate from ProductMaster according to the item
        $entryRate = 0.0;
        if (!empty($entry['item'])) {
            $entryRate = ProductMaster::where('item_id', $entry['item'])->value('rate') ?? 0.0;
        }
        // Fallback: if not found by item, try by product
        if ($entryRate == 0 && !empty($entry['product'])) {
            $entryRate = ProductMaster::where('id', $entry['product'])->value('rate') ?? 0.0;
        }
        $entryTotal = (float) ($entryBox * $entryPacking);
        $entryAmount = (float) ($entryTotal * $entryRate);
        $preparedBy = $entry['prepared_by'] ?? '';

        // FIXED: Determine sequence_no properly
        if (strpos($key, 'new_') === 0) {
            // New entry: assign next available sequence number
            $sequenceNo = $newSequenceCounter++;
        } else {
            // Existing entry: preserve the sequence number from the form
            $sequenceNo = isset($entry['sequence_no']) && is_numeric($entry['sequence_no'])
                ? (int) $entry['sequence_no']
                : $newSequenceCounter++; // fallback for corrupted data
        }

        // Add each row's amount to overall total
        $grandTotal += $entryAmount;

        if (strpos($key, 'new_') === 0) {
            // CREATE NEW ENTRY
            $newDetail = ConfectioneryDetail::create([
                'item_code' => $entry['item'] ?? null,
                'product_name' => $entry['product'] ?? null,
                'po_no' => $entryPO,
                'box' => $entryBox,
                'pack_qty' => $entryPacking,
                'total' => $entryTotal,
                'v_no' => $id,
                'freight' => $entryFreight,
                'sequence_no' => $sequenceNo,
            ]);

            ConfectioneryMaster::create([
                'v_no' => $id,
                'date' => $entryDate,
                'account_id' => $entry['supplier'] ?? null,
                'preparedby' => $preparedBy,
                'status' => 'unofficial',
                'v_type' => 'CDC',
                'confectionery_detail_id' => $newDetail->id,
                'sequence_no' => $sequenceNo,
            ]);

            // Handle freight and billing for NEW entries
            if ($hasBilling) {
                if ($entryFreight > 0) {
                    TRNDTL::create([
                        'v_no' => $id,
                        'date' => $entryDate,
                        'account_id' => $saleFreightExp,
                        'cash_id' => $saleFreight,
                        'preparedby' => $preparedBy,
                        'credit' => 0,
                        'debit' => $entryFreight,
                        'status' => 'unofficial',
                        'v_type' => 'CDC',
                        'description' => 'Freight',
                        'r_id' => $newDetail->id,
                    ]);
                }

                // Propagate to related billing (ConfectBilling)
                $billingQuery = ConfectBilling::where('old_vno', $id);
                if (!empty($entry['product'])) {
                    $billingQuery->where('product_name', $entry['product']);
                }
                if (!empty($entry['item'])) {
                    $billingQuery->where('item', $entry['item']);
                }
                if ($entryPO !== '') {
                    $billingQuery->where('po_no', $entryPO);
                }
                $relatedBillings = $billingQuery->get();

                if ($relatedBillings->isEmpty()) {
                    $referenceBilling = ConfectBilling::where('old_vno', $id)->orderBy('id')->first();
                    if ($referenceBilling) {
                        $billingNo = $referenceBilling->billing_no;
                        $billingVNo = $referenceBilling->v_no;
                        $billingAccountId = $referenceBilling->account_id;
                    } else {
                        $billingNo = (ConfectBilling::max('billing_no') ?? 0) + 1;
                        $billingVNo = $id;
                        $billingAccountId = $entry['supplier'] ?? null;
                    }
                    ConfectBilling::create([
                        'billing_no' => $billingNo,
                        'v_no' => $billingVNo,
                        'old_vno' => $id,
                        'product_name' => $entry['product'] ?? null,
                        'item' => $entry['item'] ?? null,
                        'box' => $entryBox,
                        'packing' => $entryPacking,
                        'po_no' => $entryPO,
                        'total' => $entryTotal,
                        'rate' => $entryRate,
                        'account_id' => $billingAccountId,
                        'created_at' => $entryDate,
                        'updated_at' => $entryDate,
                    ]);
                } else {
                    foreach ($relatedBillings as $bill) {
                        $bill->update([
                            'product_name' => $entry['product'] ?? $bill->product_name,
                            'item' => $entry['item'] ?? $bill->item,
                            'box' => $entryBox,
                            'packing' => $entryPacking,
                            'po_no' => $entryPO,
                            'total' => $entryTotal,
                            'rate' => $entryRate,
                            'account_id' => $entry['supplier'] ?? $bill->account_id,
                            'updated_at' => $entryDate,
                        ]);
                    }
                }
            }
        } else {
            // UPDATE EXISTING ENTRY
            $detail = ConfectioneryDetail::find($key);
            if ($detail) {
                $detail->update([
                    'item_code' => $entry['item'] ?? null,
                    'product_name' => $entry['product'] ?? null,
                    'po_no' => $entryPO,
                    'box' => $entryBox,
                    'pack_qty' => $entryPacking,
                    'total' => $entryTotal,
                    'freight' => $entryFreight,
                    'sequence_no' => $sequenceNo, // FIXED: Update sequence number
                ]);

                ConfectioneryMaster::where('confectionery_detail_id', $detail->id)
                    ->where('v_no', $id)
                    ->update([
                        'date' => $entryDate,
                        'account_id' => $entry['supplier'] ?? null,
                        'preparedby' => $preparedBy,
                        'sequence_no' => $sequenceNo, // FIXED: Update sequence number
                    ]);

                // Handle freight and billing for EXISTING entries
                if ($hasBilling) {
                    // Update freight in TRNDTL
                    if ($entryFreight > 0) {
                        $trndtl = TRNDTL::where('v_no', $id)
                            ->where('v_type', 'CDC')
                            ->where('r_id', $detail->id)
                            ->first();

                        if ($trndtl) {
                            $trndtl->update([
                                'date' => $entryDate,
                                'account_id' => $saleFreightExp,
                                'cash_id' => $saleFreight,
                                'preparedby' => $preparedBy,
                                'debit' => $entryFreight,
                            ]);
                        } else {
                            TRNDTL::create([
                                'v_no' => $id,
                                'date' => $entryDate,
                                'account_id' => $saleFreightExp,
                                'cash_id' => $saleFreight,
                                'preparedby' => $preparedBy,
                                'credit' => 0,
                                'debit' => $entryFreight,
                                'status' => 'unofficial',
                                'v_type' => 'CDC',
                                'description' => 'Freight',
                                'r_id' => $detail->id,
                            ]);
                        }
                    } else {
                        TRNDTL::where('v_no', $id)
                            ->where('v_type', 'CDC')
                            ->where('r_id', $detail->id)
                            ->delete();
                    }

                    // Update ConfectBilling entries with ProductMaster rates
                    $billingQuery = ConfectBilling::where('old_vno', $id);
                    if (!empty($entry['product'])) {
                        $billingQuery->where('product_name', $entry['product']);
                    }
                    if (!empty($entry['item'])) {
                        $billingQuery->where('item', $entry['item']);
                    }
                    if ($entryPO !== '') {
                        $billingQuery->where('po_no', $entryPO);
                    }
                    $relatedBillings = $billingQuery->get();
                    foreach ($relatedBillings as $bill) {
                        $rateToApply = $entryRate;
                        if (empty($rateToApply)) {
                            $rateToApply = ProductMaster::where('item_id', $entry['item'] ?? 0)->value('rate')
                                ?? ProductMaster::where('id', $entry['product'] ?? 0)->value('rate')
                                ?? $bill->rate;
                        }
                        $bill->update([
                            'product_name' => $entry['product'] ?? $bill->product_name,
                            'item' => $entry['item'] ?? $bill->item,
                            'box' => $entryBox,
                            'packing' => $entryPacking,
                            'po_no' => $entryPO,
                            'total' => $entryTotal,
                            'rate' => $rateToApply,
                            'account_id' => $entry['supplier'] ?? $bill->account_id,
                            'updated_at' => $entryDate,
                        ]);
                    }
                }
            }
        }
    }

    // Only update/create CBILL row in TRNDTL if billing exists
    if ($hasBilling) {
        $cBill = TRNDTL::where('v_no', $id)
            ->where('v_type', 'CBILL')
            ->first();

        if ($cBill) {
            $cBill->update([
                'debit' => $grandTotal,
                'updated_at' => now(),
            ]);
        } else {
            // Optionally create a new CBILL record if it doesn't exist
            TRNDTL::create([
                'v_no' => $id,
                'date' => now(),
                'debit' => $grandTotal,
                'credit' => 0,
                'status' => 'unofficial',
                'v_type' => 'CBILL',
                'description' => 'Confectionery Billing',
                'r_id' => null,
                'preparedby' => auth()->user()->name ?? '',
            ]);
        }
    }

    return redirect()->route('confectionery.reports')
        ->with('success', "Voucher CDC-$id updated successfully.");
}

    public function destroy($id)
{
    // Find the ConfectioneryMaster record by ID
    $confectioneryMaster = ConfectioneryMaster::findOrFail($id);
    
    // Get the v_no and confectionery_detail_id from the master record
    $vNo = $confectioneryMaster->v_no;
    $detailId = $confectioneryMaster->confectionery_detail_id;

    // Check billing records first
    if (ConfectBilling::where('old_vno', $vNo)->exists()) {
        return redirect()->back()->with('error', 'Cannot delete record. Delete related billing entries first.');
    }

    // Delete related records
    $confectioneryMaster->confectioneryDetails()->delete();
    
    // Delete TRNDTL records using the detail_id as r_id
    $trndtlDeleted = TRNDTL::where('r_id', $detailId)  // Changed from $id to $detailId
                         ->where('v_no', $vNo)
                         ->where('v_type', 'CDC')
                         ->where('description', 'freight')
                         ->delete();
    
    // Debug output (remove after testing)
    if ($trndtlDeleted === 0) {
        \Log::warning('No TRNDTL records deleted', [
            'detail_id' => $detailId,
            'v_no' => $vNo,
            'exists' => TRNDTL::where('r_id', $detailId)
                           ->where('v_no', $vNo)
                           ->exists()
        ]);
    }

    $confectioneryMaster->delete();

    return redirect()->back()->with('success', 'Record deleted successfully from all tables.');
}

public function delete($id)
{
    
    return $this->destroy($id);
}




public function editCon($v_no)
{
    // 1. Get freight data from TRNDTL
    $freightData = TRNDTL::where('v_no', $v_no)
        ->where('v_type', 'CDC')
        ->where('description', 'freight')
        ->first();
    $freight = $freightData ? $freightData->credit : 0;

    // 2. Get freight_type and sum boxes (FIXED: Use correct model)
    $confectBilling = ConfectioneryDetail::where('v_no', $v_no)->first();
    $freight_type = $confectBilling ? $confectBilling->freight_type : null;

    // 3. Sum boxes (FIXED: Ensure correct model and column)
    $totalbox = ConfectioneryDetail::where('v_no', (string)$v_no)->sum('box');

    return view('sale_reports.editConDc', compact('freight', 'v_no', 'totalbox', 'freight_type'));
}

public function updateCon(Request $request, $id)
{
    // Validate the request
    $validatedData = $request->validate([
        'total_freight' => 'required|numeric|min:0',
        'freight_type' => 'required|string', // Add validation for freight_type
    ]);

    try {
        // Fetch ERP parameters
        $erpParam = ErpParam::first();
        if (!$erpParam) {
            throw new \Exception('ERP parameters not found.');
        }

        $cashAccId = $erpParam->cash_acc;
        $Purfreight = $erpParam->sale_freight;
        $PurfreightExp = $erpParam->sale_freight_exp;
        
        $Salefreight = $erpParam ? $erpParam->sale_freight : null;
        $SalefreightExp = $erpParam ? $erpParam->sale_freight_exp : null;

        // Check if a record with v_type == 'BPN' and description == 'Freight' exists in TRNDTL
        $existingFreight = TRNDTL::where('v_no', $id)
                                 ->where('v_type', 'CDC')
                                 ->where('description', 'Freight')
                                 ->first();

        // Check if a record with vorcher_no == $id exists in PurchaseDetail
        $existingPurchaseDetail = ConfectioneryDetail::where('v_no', $id)->first();

        // If total_freight is greater than 0
        if ($validatedData['total_freight'] > 0) {
            // Update or create PurchaseDetail record
            if ($existingPurchaseDetail) {
                // Update the existing PurchaseDetail record
                $existingPurchaseDetail->update([
                    'freight' => $validatedData['total_freight'],
                    'freight_type' => $validatedData['freight_type'], // Add this line
                ]);

                // Get the id of the updated PurchaseDetail record
                $purchaseDetailId = $existingPurchaseDetail->id;
            } else {
                // Create a new PurchaseDetail record
                $purchaseDetail = ConfectioneryDetail::create([
                    'v_no' => $id,
                    'freight' => $validatedData['total_freight'],
                    'freight_type' => $validatedData['freight_type'], // Add this line
                    // Add other necessary fields here
                ]);

                // Get the id of the newly created PurchaseDetail record
                $purchaseDetailId = $purchaseDetail->id;
            }

            // Update or create TRNDTL record
            if ($existingFreight) {
                // Update the existing TRNDTL record
                $existingFreight->update([
                    'credit' => $validatedData['total_freight'],
                    'preparedby' => Auth::user()->name ?? null,
                    'date' => Carbon::now(),
                    'r_id' => $purchaseDetailId, // Set r_id to the PurchaseDetail id
                ]);
            } else {
                // Create a new TRNDTL record
                TRNDTL::create([
                    'v_no' => $id,
                    'date' => Carbon::now(),
                    'account_id' => $SalefreightExp,
        'cash_id' => $Salefreight,
                    'preparedby' => Auth::user()->name ?? null,
                    'credit' => $validatedData['total_freight'],
                    'debit' => '0',
                    'status' => 'unofficial',
                    'v_type' => 'CDC',
                    'description' => 'Freight',
                    'r_id' => $purchaseDetailId, // Set r_id to the PurchaseDetail id
                ]);
            }
        } else {
            // If total_freight is 0, delete the existing TRNDTL record (if any)
            if ($existingFreight) {
                $existingFreight->delete();
            }

            // If total_freight is 0, update the PurchaseDetail record (if any)
            if ($existingPurchaseDetail) {
                $existingPurchaseDetail->update([
                    'freight' => 0,
                ]);
            }
        }

       return redirect()->route('confectionery.reports')->with('success', 'Freight updated successfully for CDC-' . $id);

    } catch (\Exception $e) {
        // Handle any exceptions and return an error message
        return redirect()->route('confectionery.reports')->with('error', 'An error occurred: ' . $e->getMessage());
    }
}
}
