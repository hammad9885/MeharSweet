<?php

namespace App\Http\Controllers;

use App\Models\ItemType;
use Illuminate\Http\Request;
use App\Models\AccountMaster;
use App\Models\SaleInvoice;
use App\Models\DeliveryDetail;
use App\Models\DeliveryMaster;
use App\Models\ProductMaster;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ErpParam;
use App\Models\TRNDTL;

use Carbon\Carbon;

class DeliveryChallanController extends Controller
{
    public function index()
    {
        $loggedInUser = Auth::user();
        $accounts = AccountMaster::all();
        $items = ItemType::all();
        $product = ProductMaster::all();
        return view('sales.delivery_challan.list',get_defined_vars());
    }
    
    
public function getProducts($partyId)
{
    $products = ProductMaster::where('aid', $partyId)->get();
    return response()->json($products);
}

public function store(Request $request)
{
    $maxVno = DeliveryMaster::where('v_type', 'PDC')
        ->max('v_no');

if (is_numeric($maxVno)) {
    $newInvoiceNumber = (int) $maxVno + 1;
} else {
    $newInvoiceNumber = 1; 
}
        
  
    foreach ($request->entries as $index => $entry) {
        $deliveryDetail = DeliveryDetail::create([
            'v_no' => $newInvoiceNumber,
            'item_code' => $entry['item'] ?? null,
            'product_name' => $entry['product'] ?? null,
            'batch_no' => $entry['batch_no'] ?? 0,
            'box' => $entry['box'] ?? 0,
            'pack_qty' => $entry['packing'] ?? 0,
            'total' => $entry['total'] ?? 0,
            'freight' => $entry['freight'] ?? 0,
        ]);

        DeliveryMaster::create([
            'v_no' => $newInvoiceNumber,
            'date' => Carbon::now(),
            'account_id' => $entry['supplier'] ?? null,
            'preparedby' => $entry['prepared_by'] ?? null,
            'status' => 'unofficial',
            'v_type' => 'PDC',
            'delivery_detail_id' => $deliveryDetail->id,
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
        'credit' => '0',
        'debit' => $entry['freight'] ?? null,
        'status' => 'unofficial',
        'v_type' => 'PDC',
        'r_id' => $deliveryDetail->id,
        'description' => 'Freight',
    ]);
}

    }

    return redirect()->route('delivery_challan.reports')->with('success', "Voucher No. $newInvoiceNumber has been created successfully.");
}




public function reports(Request $request)
{
    $products = ProductMaster::all();

    // Get unique item codes from the DeliveryDetail table
    $itemCodes = DeliveryDetail::distinct()->pluck('item_code');
    $account = DeliveryMaster::distinct()->pluck('account_id');

    // Fetch only ItemType entries that are referenced in the DeliveryDetail table
    $items = ItemType::whereIn('id', $itemCodes)->get();
    $accounts = AccountMaster::whereIn('id', $account)->get();

    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $status = $request->input('status');
    $itemId = $request->input('item'); // New item filter
    $accountId = $request->input('account'); // New item filter
    $po = $request->input('batch_no'); // New PO filter
    $v_no = $request->input('v_no');

    // Get unique P.O numbers
    $poNumbers = DeliveryDetail::distinct()->pluck('batch_no');

    // Build the query with date range, status, item, and PO filters
    $query = DeliveryMaster::with('deliveryDetails');

    if ($startDate && $endDate) {
        $query->whereBetween('date', [$startDate, $endDate]);
    }

    if ($status) {
        $query->where('status', $status);
    }

    if ($itemId) {
        $query->whereHas('deliveryDetails', function ($q) use ($itemId) {
            $q->where('item_code', $itemId); // Match item_code instead of type_title
        });
    }
    
    if ($accountId) {
        $query->whereHas('deliveryDetails', function ($q) use ($accountId) {
            $q->where('account_id', $accountId); // Match item_code instead of type_title
        });
    }

    if ($po) {
        $query->whereHas('deliveryDetails', function ($q) use ($po) {
            $q->where('batch_no', $po);
        });
    }

    if ($v_no) {
        $query->whereHas('deliveryDetails', function ($q) use ($v_no) {
            $q->where('v_no', $v_no);
        });
    }

    
    
    $trndtl = $query->orderByRaw('CAST(date AS DATE) DESC')
        ->orderByRaw('CAST(v_no AS SIGNED) DESC')
        ->orderBy('id', 'desc')
        ->get();

    $accountMasters = AccountMaster::all();
    $vNoList = DeliveryMaster::pluck('v_no')->unique()->toArray();

    return view('sale_reports.index', [
        'trndtl' => $trndtl,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'status' => $status,
        'accountMasters' => $accountMasters,
        'items' => $items,
        'accounts' => $accounts,
        'products' => $products,
        'itemId' => $itemId, 
        'po' => $po,
        'vNoList' => $vNoList,
        'poNumbers' => $poNumbers,
    ]);
}




public function edit($v_no)
{
    // Find the voucher and its entries by voucher number (v_no)
    $loggedInUser = Auth::user();
    $voucher = DeliveryMaster::where('v_no', $v_no)->get(); // Fetch all entries for this voucher

    // Prefill rates from SaleInvoice for each delivery detail (matched by old_vno + product/item)
    $rates = [];
    $billed = [];
    foreach ($voucher as $row) {
        $detail = $row->deliveryDetails; // relation name in model is deliveryDetails
        if ($detail) {
            $rate = SaleInvoice::where('old_vno', $v_no)
                ->when($detail->product_name, function ($q) use ($detail) {
                    $q->where('product_name', $detail->product_name);
                })
                ->when($detail->item_code, function ($q) use ($detail) {
                    $q->where('item', $detail->item_code);
                })
                ->value('rate');
            $rates[$detail->id] = $rate ?? 0;
            // Determine if this detail has any related bill
            $hasBill = SaleInvoice::where('old_vno', $v_no)
                ->when($detail->product_name, function ($q) use ($detail) {
                    $q->where('product_name', $detail->product_name);
                })
                ->when($detail->item_code, function ($q) use ($detail) {
                    $q->where('item', $detail->item_code);
                })
                ->when($detail->batch_no, function ($q) use ($detail) {
                    $q->where('batch_no', $detail->batch_no);
                })
                ->exists();
            $billed[$detail->id] = $hasBill;
        }
    }

    // Fetch account master data for dropdowns
    $accounts = AccountMaster::all();

    $items = ItemType::all();
    $product = ProductMaster::all();

    // Pass the voucher and entries to the view
    return view('sale_reports.edit', get_defined_vars());
}


public function update(Request $request, $id)
{
    $entries = $request->input('entries', []);
    if (!is_array($entries)) {
        return back()->withErrors(['entries' => 'Entries must be an array.']);
    }

    $erpParam     = ErpParam::first();
    $saleFreight  = $erpParam?->sale_freight;
    $saleFreightExp = $erpParam?->sale_freight_exp;

    // Handle deleted rows
    $deletedIds = $request->input('deleted_ids', []);
    if (is_array($deletedIds) && !empty($deletedIds)) {
        foreach ($deletedIds as $deletedId) {
            $detail = DeliveryDetail::find($deletedId);
            if ($detail) {
                // Prevent deleting only if billing exists
                $hasRelatedBill = SaleInvoice::where('old_vno', $id)
                    ->when($detail->product_name, fn($q) => $q->where('product_name', $detail->product_name))
                    ->when($detail->item_code, fn($q) => $q->where('item', $detail->item_code))
                    ->when($detail->batch_no, fn($q) => $q->where('batch_no', $detail->batch_no))
                    ->exists();

                if ($hasRelatedBill) {
                    return back()->with('error', 'To delete this voucher entry, delete its billing first.');
                }

                DeliveryMaster::where('delivery_detail_id', $detail->id)
                    ->where('v_no', $id)
                    ->delete();

                TRNDTL::where('r_id', $detail->id)
                    ->where('v_no', $id)
                    ->where('v_type', 'PDC')
                    ->delete();

                SaleInvoice::where('old_vno', $id)
                    ->when($detail->product_name, fn($q) => $q->where('product_name', $detail->product_name))
                    ->when($detail->item_code, fn($q) => $q->where('item', $detail->item_code))
                    ->delete();

                $detail->delete();
            }
        }
    }


    $grandTotal = 0;
    $hasBilling = SaleInvoice::where('old_vno', $id)->exists();

    foreach ($entries as $key => $entry) {
        $entryDate    = $entry['date'] ?? now();
        $entryFreight = floatval($entry['freight'] ?? 0);
        $entryBox     = intval($entry['box'] ?? 0);
        $entryPacking = intval($entry['packing'] ?? 0);
        $entryBatch   = $entry['batchNo'] ?? '';

        // Always fetch rate from ProductMaster according to item
        $entryRate = 0.0;
        if (!empty($entry['item'])) {
            $entryRate = ProductMaster::where('item_id', $entry['item'])->value('rate') ?? 0.0;
        }
        // Fallback: if not found by item, try by product
        if ($entryRate == 0 && !empty($entry['product'])) {
            $entryRate = ProductMaster::where('id', $entry['product'])->value('rate') ?? 0.0;
        }

        $entryTotal   = (float) ($entryBox * $entryPacking);
        $entryAmount  = (float) ($entryTotal * $entryRate);
        $preparedBy   = $entry['prepared_by'] ?? '';

        $grandTotal += $entryAmount;

        // --- New Entry ---
        if (strpos($key, 'new_') === 0) {
            $newDetail = DeliveryDetail::create([
                'item_code'    => $entry['item'] ?? null,
                'product_name' => $entry['product'] ?? null,
                'batch_no'     => $entryBatch,
                'box'          => $entryBox,
                'pack_qty'     => $entryPacking,
                'total'        => $entryTotal,
                'v_no'         => $id,
                'freight'      => $entryFreight,
            ]);

            DeliveryMaster::create([
                'v_no'               => $id,
                'date'               => $entryDate,
                'account_id'         => $entry['supplier'] ?? null,
                'preparedby'         => $preparedBy,
                'status'             => 'unofficial',
                'v_type'             => 'PDC',
                'delivery_detail_id' => $newDetail->id,
            ]);

            if ($entryFreight > 0) {
                TRNDTL::create([
                    'v_no'        => $id,
                    'date'        => $entryDate,
                    'account_id'  => $saleFreightExp,
                    'cash_id'     => $saleFreight,
                    'preparedby'  => $preparedBy,
                    'credit'      => 0,
                    'debit'       => $entryFreight,
                    'status'      => 'unofficial',
                    'v_type'      => 'PDC',
                    'description' => 'Freight',
                    'r_id'        => $newDetail->id,
                ]);
            }

            // Propagate to SaleInvoice and TRNDTL only if billing exists
            if ($hasBilling) {
                $invoiceQuery = SaleInvoice::where('old_vno', $id);
                if (!empty($entry['product'])) $invoiceQuery->where('product_name', $entry['product']);
                if (!empty($entry['item']))    $invoiceQuery->where('item', $entry['item']);
                if ($entryBatch !== '')        $invoiceQuery->where('batch_no', $entryBatch);

                $relatedInvoices = $invoiceQuery->get();
                if ($relatedInvoices->isEmpty()) {
                    $referenceInvoice = SaleInvoice::where('old_vno', $id)->orderBy('id')->first();
                    SaleInvoice::create([
                        'billing_no'   => $referenceInvoice->billing_no ?? null,
                        'v_no'         => $referenceInvoice->v_no ?? null,
                        'old_vno'      => $id,
                        'product_name' => $entry['product'] ?? null,
                        'item'         => $entry['item'] ?? null,
                        'box'          => $entryBox,
                        'packing'      => $entryPacking,
                        'batch_no'     => $entryBatch,
                        'total'        => $entryTotal,
                        'rate'         => $entryRate,
                        'account_id'   => $entry['supplier'] ?? ($referenceInvoice->account_id ?? null),
                        'created_at'   => $entryDate,
                        'updated_at'   => $entryDate,
                    ]);
                } else {
                    foreach ($relatedInvoices as $inv) {
                        $inv->update([
                            'product_name' => $entry['product'] ?? $inv->product_name,
                            'item'         => $entry['item'] ?? $inv->item,
                            'box'          => $entryBox,
                            'packing'      => $entryPacking,
                            'batch_no'     => $entryBatch,
                            'total'        => $entryTotal,
                            'rate'         => $entryRate,
                            'account_id'   => $entry['supplier'] ?? $inv->account_id,
                            'updated_at'   => $entryDate,
                        ]);
                    }
                }
            }
        } 
        // --- Existing Entry ---
        else {
            $detail = DeliveryDetail::find($key);
            if ($detail) {
                $detail->update([
                    'item_code'    => $entry['item'] ?? null,
                    'product_name' => $entry['product'] ?? null,
                    'batch_no'     => $entryBatch,
                    'box'          => $entryBox,
                    'pack_qty'     => $entryPacking,
                    'total'        => $entryTotal,
                    'freight'      => $entryFreight,
                ]);

                DeliveryMaster::where('delivery_detail_id', $detail->id)
                    ->where('v_no', $id)
                    ->update([
                        'date'       => $entryDate,
                        'account_id' => $entry['supplier'] ?? null,
                        'preparedby' => $preparedBy,
                    ]);

                // Freight TRNDTL update
                if ($entryFreight > 0) {
                    $trndtl = TRNDTL::where('v_no', $id)
                        ->where('v_type', 'PDC')
                        ->where('r_id', $detail->id)
                        ->first();

                    if ($trndtl) {
                        $trndtl->update([
                            'date'       => $entryDate,
                            'account_id' => $saleFreightExp,
                            'cash_id'    => $saleFreight,
                            'preparedby' => $preparedBy,
                            'debit'      => $entryFreight,
                        ]);
                    } else {
                        TRNDTL::create([
                            'v_no'        => $id,
                            'date'        => $entryDate,
                            'account_id'  => $saleFreightExp,
                            'cash_id'     => $saleFreight,
                            'preparedby'  => $preparedBy,
                            'credit'      => 0,
                            'debit'       => $entryFreight,
                            'status'      => 'unofficial',
                            'v_type'      => 'PDC',
                            'description' => 'Freight',
                            'r_id'        => $detail->id,
                        ]);
                    }
                } else {
                    TRNDTL::where('v_no', $id)
                        ->where('v_type', 'PDC')
                        ->where('r_id', $detail->id)
                        ->delete();
                }

                // Propagate to SaleInvoice and TRNDTL only if billing exists
                if ($hasBilling) {
                    $invoiceQuery = SaleInvoice::where('old_vno', $id);
                    if (!empty($entry['product'])) $invoiceQuery->where('product_name', $entry['product']);
                    if (!empty($entry['item']))    $invoiceQuery->where('item', $entry['item']);
                    if ($entryBatch !== '')        $invoiceQuery->where('batch_no', $entryBatch);

                    $relatedInvoices = $invoiceQuery->get();
                    foreach ($relatedInvoices as $inv) {
                        $inv->update([
                            'product_name' => $entry['product'] ?? $inv->product_name,
                            'item'         => $entry['item'] ?? $inv->item,
                            'box'          => $entryBox,
                            'packing'      => $entryPacking,
                            'batch_no'     => $entryBatch,
                            'total'        => $entryTotal,
                            'rate'         => $entryRate,
                            'account_id'   => $entry['supplier'] ?? $inv->account_id,
                            'updated_at'   => $entryDate,
                        ]);
                    }
                }
            }
        }
    }

    // After loop: update PBILL TRNDTL if billing exists
    if ($hasBilling) {
        $pBill = TRNDTL::where('v_no', $id)
            ->where('v_type', 'PBILL')
            ->first();

        if ($pBill) {
            $pBill->update([
                'debit'      => $grandTotal,
                'updated_at' => now(),
            ]);
        } else {
            TRNDTL::create([
                'v_no'        => $id,
                'date'        => now(),
                'debit'       => $grandTotal,
                'credit'      => 0,
                'status'      => 'unofficial',
                'v_type'      => 'PBILL',
                'description' => 'Delivery Billing',
                'r_id'        => null,
                'preparedby'  => auth()->user()->name ?? '',
            ]);
        }
    }

    return redirect()->route('delivery_challan.reports')
        ->with('success', "Voucher PDC-$id updated successfully.");
}






public function destroy($id)
{
    // Find the DeliveryMaster record by ID
    $deliveryMaster = DeliveryMaster::findOrFail($id);

    // Get the v_no and delivery_detail_id from the master record
    $vNo = $deliveryMaster->v_no;
    $detailId = $deliveryMaster->delivery_detail_id;

    // Check if a bill exists for this voucher
    if (SaleInvoice::where('old_vno', $vNo)->exists()) {
        return redirect()->back()->with('error', 'Cannot delete voucher. Please delete the related bill first.');
    }

    // Delete related DeliveryDetail records
    $deliveryMaster->deliveryDetails()->delete();

    // Delete related TRNDTL records
    TRNDTL::where('r_id', $detailId)
        ->where('v_no', $vNo)
        ->where('v_type', 'PDC')
        ->where('description', 'freight')
        ->delete();

    // Delete the DeliveryMaster record
    $deliveryMaster->delete();

    return redirect()->back()->with('success', 'Voucher deleted successfully.');
}


public function delete($id)
{
    
    return $this->destroy($id);
}




public function editCon($v_no)
{
    // 1. Get freight data from TRNDTL
    $freightData = TRNDTL::where('v_no', $v_no)
        ->where('v_type', 'PDC')
        ->where('description', 'freight')
        ->first();
    $freight = $freightData ? $freightData->credit : 0;

    // 2. Get freight_type and sum boxes (FIXED: Use correct model)
    $confectBilling = DeliveryDetail::where('v_no', $v_no)->first();
    $freight_type = $confectBilling ? $confectBilling->freight_type : null;

    // 3. Sum boxes (FIXED: Ensure correct model and column)
    $totalbox = DeliveryDetail::where('v_no', (string)$v_no)->sum('box');

    return view('sale_reports.editDelDc', compact('freight', 'v_no', 'totalbox', 'freight_type'));
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
                                 ->where('v_type', 'PDC')
                                 ->where('description', 'Freight')
                                 ->first();

        // Check if a record with vorcher_no == $id exists in PurchaseDetail
        $existingPurchaseDetail = DeliveryDetail::where('v_no', $id)->first();

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
                $purchaseDetail = DeliveryDetail::create([
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
                    'v_type' => 'PDC',
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

       return redirect()->route('delivery_challan.reports')->with('success', 'Freight updated successfully for PDC-' . $id);

    } catch (\Exception $e) {
        // Handle any exceptions and return an error message
        return redirect()->route('delivery_challan.reports')->with('error', 'An error occurred: ' . $e->getMessage());
    }
}





}
