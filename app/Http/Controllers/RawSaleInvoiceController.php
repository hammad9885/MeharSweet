<?php

namespace App\Http\Controllers;

use App\Models\ErpParam;
use App\Models\RawSaleInvoice;
use App\Models\ItemMaster;
use App\Models\RawSaleMaster;
use App\Models\Customers as Customer;
use App\Models\TRNDTL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RawSaleInvoiceController extends Controller
{
    public function index()
    {
        $user = Auth::user()->cid;
        $startDate = request('startDate');
        $endDate = request('endDate');
        $query = RawSaleMaster::where('cid', $user)->with(['items', 'customer']);

        if (request('voucher_no')) {
            $query->where('v_no', request('voucher_no'));
        }

        if (request('customer_id')) {
            $query->where('customer_id', request('customer_id'));
        }

        if ($startDate || $endDate) {
            if ($startDate && $endDate)
                $query->whereBetween('v_date', [$startDate, $endDate]);
            elseif ($startDate)
                $query->whereDate('v_date', '>=', $startDate);
            elseif ($endDate)
                $query->whereDate('v_date', '<=', $endDate);
        }

        $invoices = $query->orderByDesc('v_no')->get();
        return view('raw_sale_invoices.index', compact('invoices'));
    }

    public function create()
    {
        $user = Auth::User()->cid;
        $customers = Customer::where('cid', $user)->get();
        $items = ItemMaster::where('cid', $user)->get();

        return view('raw_sale_invoices.create', compact('customers', 'items'));
    }

    public function store(Request $request)
    {
        $user = Auth::User()->cid;
        if (!$request->has('items') || empty($request->items)) {
            return redirect()->back()->withInput()->withErrors(['items' => 'Please add at least one item.']);
        }

        $request->validate([
            'supplier_id' => 'required',
            'items.*.item_id' => 'required|exists:item_masters,id',
            'items.*.qty' => 'required|numeric',
            'items.*.rate' => 'nullable|numeric',
            'items.*.tax_rate' => 'required|numeric',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->move(public_path('storage/uploads'), $fileName);
            $filePath = 'uploads/' . $fileName;
        }

        $latestVNo = RawSaleMaster::where('cid', $user)->max('v_no');
        $v_no = $latestVNo ? $latestVNo + 1 : 1;

        $grandTotal = 0;
        $erpParams = ErpParam::where('cid', $user)->first();

        if (!$erpParams || !$erpParams->saleCreditAccount) {
            return back()->withErrors(['error' => 'Customer Chart of Account configuration is missing.']);
        }

        foreach ($request->items as $item) {
            $exclusive = $item['qty'] * $item['rate'];
            $taxAmount = $exclusive * ($item['tax_rate'] / 100);
            $inclusive = $exclusive + $taxAmount;
            $finalAfterDiscount = $inclusive - ($item['discount'] ?? 0);
            $grandTotal += $finalAfterDiscount;
        }

        $grandDiscount = $request->grand_discount ?? 0;
        $grandTotalAfterDiscount = $grandTotal - $grandDiscount;

        RawSaleMaster::create([
            'v_no' => $v_no,
            'gate_pass_no' => $request->gatepass_no,
            'grand_total' => $grandTotalAfterDiscount,
            'discount' => $grandDiscount,
            'cid' => $user,
            'customer_id' => $request->supplier_id,
            'v_date' => $request->v_date
        ]);

        $maxVoucherNo = TRNDTL::where('v_type', 'RSIN')->where('cid', $user)->max('v_no');
        $maxVoucherNo = $maxVoucherNo ? $maxVoucherNo + 1 : 1;

        TRNDTL::create([
            'v_no' => $maxVoucherNo,
            'account_id' => $request->supplier_id,
            'cash_id' => $erpParams->saleCreditAccount,
            'credit' => $grandTotalAfterDiscount,
            'debit' => 0,
            'file_id' => $filePath,
            'date' => $request->v_date,
            'v_type' => 'RSIN',
            'preparedby' => Auth::user()->name,
            'r_id' => $v_no,
            'cid' => $user
        ]);

        foreach ($request->items as $item) {
            $exclusive = $item['qty'] * $item['rate'];
            $taxAmount = $exclusive * ($item['tax_rate'] / 100);
            $inclusive = $exclusive + $taxAmount;

            RawSaleInvoice::create([
                'v_no' => $v_no,
                'customer_id' => $request->supplier_id,
                'item_id' => $item['item_id'],
                'qty' => $item['qty'],
                'rate' => $item['rate'],
                'exclusive' => $exclusive,
                'tax_rate' => $item['tax_rate'],
                'tax_amount' => $taxAmount,
                'inclusive' => $inclusive,
                'discount' => $item['discount'] ?? 0,
                'discount_perc' => $item['discount_perc'] ?? 0,
                'cid' => $user
            ]);
        }

        return redirect()->route('raw_sale_invoices.index')->with('success', "Raw Sale Invoice #$v_no created successfully.");
    }

    public function edit($v_no)
    {
        $user = Auth::User()->cid;
        $saleMaster = RawSaleMaster::where('v_no', $v_no)->where('cid', $user)->firstOrFail();
        $items = ItemMaster::where('cid', $user)->get();
        $customers = Customer::where('cid', $user)->get();
        $invoiceItems = RawSaleInvoice::where('v_no', $v_no)->where('cid', $user)->get();

        return view('raw_sale_invoices.edit', compact('saleMaster', 'invoiceItems', 'items', 'customers'));
    }

    public function update(Request $request, $v_no)
    {
        $user = Auth::User()->cid;
        if (!$request->has('items') || empty($request->items)) {
            return redirect()->back()->withInput()->withErrors(['items' => 'Please add at least one item.']);
        }

        $request->validate([
            'supplier_id' => 'required',
            'items.*.item_id' => 'required|exists:item_masters,id',
            'items.*.qty' => 'required|numeric',
            'items.*.rate' => 'required|numeric',
            'items.*.tax_rate' => 'required|numeric',
        ]);

        // Price Validation
        foreach ($request->items as $index => $itemData) {
            $itemMaster = ItemMaster::find($itemData['item_id']);
            if ($itemMaster && $itemData['rate'] < $itemMaster->cost_price) {
                return redirect()->back()->withInput()->withErrors([
                    'items.' . $index . '.rate' => "Value cannot be less than cost price (" . $itemMaster->cost_price . ") for item: " . ($itemMaster->item_code ?? 'Item ' . ($index + 1))
                ]);
            }
        }

        $master = RawSaleMaster::where('v_no', $v_no)->where('cid', $user)->firstOrFail();
        $transaction = TRNDTL::where('v_type', 'RSIN')->where('r_id', $v_no)->where('cid', $user)->firstOrFail();

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
            'grand_total' => $grandTotalAfterDiscount,
            'discount' => $grandDiscount,
            'gate_pass_no' => $request->gatepass_no,
            'v_date' => $request->v_date,
            'customer_id' => $request->supplier_id
        ]);

        $transaction->update([
            'credit' => $grandTotalAfterDiscount,
            'account_id' => $request->supplier_id,
            'date' => $request->v_date,
        ]);

        RawSaleInvoice::where('v_no', $v_no)->where('cid', $user)->delete();
        foreach ($request->items as $item) {
            $exclusive = $item['qty'] * $item['rate'];
            $taxAmount = $exclusive * ($item['tax_rate'] / 100);
            $inclusive = $exclusive + $taxAmount;

            RawSaleInvoice::create([
                'v_no' => $v_no,
                'customer_id' => $request->supplier_id,
                'item_id' => $item['item_id'],
                'qty' => $item['qty'],
                'rate' => $item['rate'],
                'exclusive' => $exclusive,
                'tax_rate' => $item['tax_rate'],
                'tax_amount' => $taxAmount,
                'inclusive' => $inclusive,
                'discount' => $item['discount'] ?? 0,
                'discount_perc' => $item['discount_perc'] ?? 0,
                'cid' => $user
            ]);
        }

        return redirect()->route('raw_sale_invoices.index')->with('success', "Raw Sale Invoice #$v_no updated successfully.");
    }

    public function destroy($v_no)
    {
        $user = Auth::User()->cid;
        RawSaleMaster::where('v_no', $v_no)->where('cid', $user)->delete();
        RawSaleInvoice::where('v_no', $v_no)->where('cid', $user)->delete();
        TRNDTL::where('r_id', $v_no)->where('v_type', 'RSIN')->where('cid', $user)->delete();

        return redirect()->route('raw_sale_invoices.index')->with('success', "Raw Sale Invoice #$v_no deleted successfully.");
    }

    public function print($v_no)
    {
        $user = Auth::user()->cid;
        $master = RawSaleMaster::where('v_no', $v_no)->where('cid', $user)->firstOrFail();
        $items = RawSaleInvoice::with('item')->where('v_no', $v_no)->where('cid', $user)->get();
        $customer = Customer::where('acc_id', $master->customer_id)->where('cid', $user)->first();

        return view('raw_sale_invoices.print', compact('master', 'items', 'customer'));
    }
}
