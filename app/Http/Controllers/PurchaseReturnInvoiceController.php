<?php

namespace App\Http\Controllers;

use App\Models\ErpParam;
use App\Models\PurchaseReturnInvoice;
use App\Models\ItemMaster;
use App\Models\PurchaseReturnMaster;
use App\Models\Supplier;
use App\Models\TRNDTL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PurchaseReturnInvoiceController extends Controller
{
    // Display a listing of purchase return invoices
    public function index()
    {
        $user = Auth::user()->cid;
        $query = PurchaseReturnMaster::where('cid', $user)
            ->with(['items' => function ($q) {
                $q->where('cid', auth()->user()->cid);
            }]);
        // dd($query->get());

        // Filter by Voucher No
        if (request('voucher_no')) {
            $query->where('v_no', request('voucher_no'));
        }

        // Filter by Supplier
        if (request('supplier_id')) {
            $query->whereHas('items', function ($q) {
                $q->where('supplier_id', request('supplier_id'));
            });
        }

        // Filter by Item
        if (request('item_id')) {
            $query->whereHas('items', function ($q) {
                $q->where('item_id', request('item_id'));
            });
        }

        // Filter by Date
        if (request('date')) {
            $query->whereDate('created_at', request('date'));
        }

        $invoices = $query->orderByDesc('v_no')->get();

        return view('purchase_return_invoices.index', compact('invoices'));
    }

    // Show the form for creating a new purchase return invoice
    public function create()
    {
        $user = Auth::user()->cid;
        $suppliers = Supplier::where('cid', $user)->get();
        $items = ItemMaster::where('cid', $user)->get();

        return view('purchase_return_invoices.create', compact('suppliers', 'items'));
    }

    // Store a newly created purchase return invoice (master-detail)
    public function store(Request $request)
    {
        if (!$request->has('items') || empty($request->items)) {
            return redirect()->back()->withInput()->withErrors(['items' => 'Please add at least one item.']);
        }
        // dd($request->items);
        $user = Auth::user()->cid;
        //   dd(  $request->all());
        $request->validate([
            'supplier_id' => 'required|exists:supplier,acc_id',
            'items.*.item_id' => 'required|exists:item_masters,id',
            'items.*.qty' => 'required|numeric',
            'items.*.rate' => 'required|numeric',
            'items.*.tax_rate' => 'required|numeric',
            'items.*.discount' => 'nullable|numeric',
            'grand_discount' => 'nullable|numeric'
        ]);
        $filePath = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->move(public_path('storage/uploads'), $fileName); // Store in public/storage/uploads
            $filePath = 'uploads/' . $fileName; // Save the relative path
        }
        // Generate new voucher number
        $latestVNo = PurchaseReturnMaster::where('cid', $user)->latest('v_no')->value('v_no');
        $v_no = $latestVNo ? $latestVNo + 1 : 1;

        $grandTotal = 0;
        $erpParams = ErpParam::where('cid', $user)->first();

        if (!$erpParams || !$erpParams->purchaseDebitAccount) {
            return back()->withErrors(['error' => 'Supplier Chart of Account configuration is missing.']);
        }

        // Calculate totals
        foreach ($request->items as $item) {
            $exclusive = $item['qty'] * $item['rate'];
            $taxAmount = $exclusive * ($item['tax_rate'] / 100);
            $inclusive = $exclusive + $taxAmount;
            $finalAfterDiscount = $inclusive - ($item['discount'] ?? 0);
            $grandTotal += $finalAfterDiscount;
        }

        $grandDiscount = $request->grand_discount ?? 0;
        $grandTotalAfterDiscount = $grandTotal - $grandDiscount;

        // Store PurchaseReturnMaster
        $master = PurchaseReturnMaster::create([
            'v_no' => $v_no,
            'gate_pass_no' => $request->gatepass_no,
            'grand_total' => $grandTotalAfterDiscount,
            'supplier_id' => $request->supplier_id,
            'discount' => $grandDiscount ?? 0,
            'cid' => $user,
            'v_date' => $request->v_date
        ]);

        // Ledger entry for return (Debit instead of Credit)
        $supplier = Supplier::where('acc_id', $request->supplier_id)->first();
        $maxVoucherNo = TRNDTL::where('v_type', 'PRN')->where('cid', $user)->max('v_no');
        $maxVoucherNo = $maxVoucherNo ? $maxVoucherNo + 1 : 1;

        TRNDTL::create([
            'v_no' => $maxVoucherNo,
            'account_id' => $supplier->acc_id,
            'cash_id' => $erpParams->purchaseDebitAccount->id,
            'debit' => 0,
            'credit' => $grandTotalAfterDiscount,
            'date' => $request->v_date,
            'v_type' => 'PRN',
            'preparedby' => Auth::user()->name,
            'r_id' => $v_no,
            'file_id' => $filePath,
            'cid' => $user
        ]);

        // Store PurchaseReturnInvoice items
        foreach ($request->items as $item) {
            $exclusive = $item['qty'] * $item['rate'];
            $taxAmount = $exclusive * ($item['tax_rate'] / 100);
            $inclusive = $exclusive + $taxAmount;

            PurchaseReturnInvoice::create([
                'v_no' => $v_no,
                'supplier_id' => $request->supplier_id,
                'item_id' => $item['item_id'],
                'qty' => $item['qty'],
                'rate' => $item['rate'],
                'exclusive' => $exclusive,
                'tax_rate' => $item['tax_rate'],
                'discount_perc' => $item['discount_perc'] ?? 0,
                'discount' => $item['discount'] ?? 0,
                'tax_amount' => $taxAmount,
                'inclusive' => $inclusive,
                'cid' => $user
            ]);
        }

        return redirect()->route('purchase_return_invoices.index')->with('success', "Purchase Return Invoice #$v_no created successfully.");
    }

    // Show the form for editing the specified purchase return invoice
    public function edit($v_no)
    {
        $user = Auth::user()->cid;

        $purchaseMaster = PurchaseReturnMaster::where('v_no', $v_no)
            ->where('cid', $user)
            ->firstOrFail();

        $items = ItemMaster::where('cid', $user)->get();
        $suppliers = Supplier::where('cid', $user)->get();
        $invoiceItems = PurchaseReturnInvoice::where('v_no', $v_no)
            ->where('cid', $user)
            ->get();
        $maxVoucherNo = TRNDTL::where('v_type', 'PRN')->where('r_id', $v_no)->where('cid', $user)->firstOrFail();

        return view('purchase_return_invoices.edit', compact('purchaseMaster', 'invoiceItems', 'items', 'suppliers', 'maxVoucherNo'));
    }

    // Update the specified purchase return invoice
    public function update(Request $request, $v_no)
    {
        if (!$request->has('items') || empty($request->items)) {
            return redirect()->back()->withInput()->withErrors(['items' => 'Please add at least one item.']);
        }
        $user = Auth::user()->cid;

        $request->validate([
            'supplier_id' => 'required|exists:supplier,acc_id',
            'items.*.item_id' => 'required|exists:item_masters,id',
            'items.*.qty' => 'required|numeric',
            'items.*.rate' => 'required|numeric',
            'items.*.tax_rate' => 'required|numeric',
            'items.*.discount' => 'nullable|numeric',
            'grand_discount' => 'nullable|numeric'
        ]);
        $filePath = $request->old_file ?? null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->move(public_path('storage/uploads'), $fileName); // Store in public/storage/uploads
            $filePath = 'uploads/' . $fileName; // Save the relative path
        }
        $master = PurchaseReturnMaster::where('v_no', $v_no)->where('cid', $user)->firstOrFail();

        $maxVoucherNo = TRNDTL::where('v_type', 'PRN')->where('r_id', $v_no)->where('cid', $user)->firstOrFail();
        $grandTotal = 0;
        foreach ($request->items as $item) {
            $exclusive = $item['qty'] * $item['rate'];
            $taxAmount = $exclusive * ($item['tax_rate'] / 100);
            $inclusive = $exclusive + $taxAmount;
            $finalAfterDiscount = $inclusive - ($item['discount'] ?? 0);
            $grandTotal += $finalAfterDiscount;
        }

        $grandDiscount = $request->grand_discount ?? 0;
        $grandTotalAfterDiscount = $grandTotal - $grandDiscount;

        $master->update([
            'gate_pass_no' => $request->gatepass_no,
            'grand_total' => $grandTotalAfterDiscount,
            'supplier_id' => $request->supplier_id,
            'discount' => $grandDiscount ?? 0,
            'v_date' => $request->v_date
        ]);
        $maxVoucherNo->update([
            'credit' => $grandTotalAfterDiscount,
            'date' => $request->v_date,
            'file_id' => $filePath,
        ]);

        // Delete existing items and re-insert
        PurchaseReturnInvoice::where('v_no', $v_no)->where('cid', auth()->user()->cid)->delete();

        foreach ($request->items as $item) {
            $exclusive = $item['qty'] * $item['rate'];
            $taxAmount = $exclusive * ($item['tax_rate'] / 100);
            $inclusive = $exclusive + $taxAmount;

            PurchaseReturnInvoice::create([
                'v_no' => $v_no,
                'supplier_id' => $request->supplier_id,
                'item_id' => $item['item_id'],
                'qty' => $item['qty'],
                'rate' => $item['rate'],
                'exclusive' => $exclusive,
                'tax_rate' => $item['tax_rate'],
                'discount_perc' => $item['discount_perc'] ?? 0,
                'discount' => $item['discount'] ?? 0,
                'tax_amount' => $taxAmount,
                'inclusive' => $inclusive,

                'cid' => $user
            ]);
        }

        return redirect()->route('purchase_return_invoices.index')->with('success', "Purchase Return Invoice #$v_no updated successfully.");
    }

    // Remove the specified purchase return invoice
    public function destroy($v_no)
    {
        $user = Auth::user()->cid;

        PurchaseReturnMaster::where('v_no', $v_no)->where('cid', $user)->delete();
        PurchaseReturnInvoice::where('v_no', $v_no)->where('cid', $user)->delete();

        TRNDTL::where('r_id', $v_no)->where('v_type', 'PRN')->where('cid', $user)->delete();
        return redirect()->route('purchase_return_invoices.index')->with('success', "Purchase Return Invoice #$v_no deleted successfully.");
    }
    public function print($v_no)
    {
        $user = Auth::user()->cid;

        $master = \App\Models\PurchaseReturnMaster::where('v_no', $v_no)
            ->where('cid', $user)
            ->firstOrFail();

        $items = \App\Models\PurchaseReturnInvoice::with('item')
            ->where('v_no', $v_no)
            ->where('cid', $user)
            ->get();

        $supplier = \App\Models\Supplier::where('acc_id', $items->first()->supplier_id ?? null)->first();

        return view('purchase_return_invoices.print', compact('master', 'items', 'supplier'));
    }
}
