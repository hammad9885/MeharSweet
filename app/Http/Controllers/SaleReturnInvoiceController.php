<?php

namespace App\Http\Controllers;

use App\Models\ErpParam;
use App\Models\SaleReturnInvoice;
use App\Models\ProductMaster;
use App\Models\SaleReturnMaster;
use App\Models\Customers as Customer;
use App\Models\TRNDTL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SaleReturnInvoiceController extends Controller
{
    // Display a listing of Sale Return
    public function index()
    {
        $user = Auth::user()->cid;
        $startDate = request('startDate');
        $endDate = request('endDate');
        $query = SaleReturnMaster::where('cid', $user)->with(['items' => function ($query) {
            $query->where('cid', auth()->user()->cid);
        }, 'customer' => function ($query) {
            $query->where('cid', auth()->user()->cid);
        }]);

        // Filter by Voucher No
        if (request('voucher_no')) {
            $query->where('v_no', request('voucher_no'));
        }

        // Filter by Customer
        if (request('customer_id')) {
            $query->whereHas('items', function ($q) {
                $q->where('customer_id', request('customer_id'));
            });
        }

        // Filter by Item
        if (request('item_id')) {
            $query->whereHas('items', function ($q) {
                $q->where('item_id', request('item_id'));
            });
        }

        if ($startDate || $endDate) {
            // Filter by Date
            if ($startDate && $endDate)
                $query->whereBetween('v_date', [$startDate, $endDate]);
            elseif ($startDate)
                $query->whereDate('v_date', '>=', $startDate);
            elseif ($endDate)
                $query->whereDate('v_date', '<=', $endDate);
        }

        $invoices = $query->orderByDesc('v_no')->get();

        if (request()->wantsJson() || request()->header('X-Inertia')) {
            $user = Auth::user()->cid;
            $customers = Customer::where('cid', $user)->get();
            $items = ProductMaster::where('cid', $user)->get();
            return \Inertia\Inertia::render('Sales/SaleReturns/Index', [
                'invoices' => $invoices,
                'customers' => $customers,
                'items' => $items,
                'filters' => request()->all()
            ]);
        }

        return view('sale_return_invoices.index', compact('invoices'));
    }

    // Show the form for creating a new sale return invoice
    public function create()
    {
        $user = Auth::User()->cid;
        $customers = Customer::where('cid', $user)->get();
        $items = ProductMaster::where('cid', $user)->get();

        return view('sale_return_invoices.create', compact('customers', 'items'));
    }

    // Store a newly created sale return invoice (master-detail)
    public function store(Request $request)
    {
        $user = Auth::User()->cid;
        if (!$request->has('items') || empty($request->items)) {
            return redirect()->back()->withInput()->withErrors(['items' => 'Please add at least one item.']);
        }

        $request->validate([
            'customer_id' => 'required',
            'items.*.item_id' => 'required|exists:product_masters,id',
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
        $latestVNo = SaleReturnMaster::where('cid', $user)->latest('v_no')->value('v_no');
        $v_no = $latestVNo ? $latestVNo + 1 : 1;

        $grandTotal = 0;
        $erpParams = ErpParam::where('cid', $user)->first();

        if (!$erpParams || !$erpParams->saleCreditAccount()) {
            return back()->withErrors(['error' => 'Customer Chart of Account configuration is missing.']);
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
        //    dd($request->v_date);
        // Store SaleReturnMaster
        $customer = Customer::find($request->customer_id);
        $master = SaleReturnMaster::create([
            'v_no' => $v_no,
            'gate_pass_no' => $request->gatepass_no,
            'grand_total' => $grandTotalAfterDiscount,
            'discount' => $grandDiscount ?? 0,
            'cid' => $user,
            'customer_id' => $request->customer_id,
            'v_date' => $request->v_date
        ]);

        // Ledger entry for return (Credit the customer’s account)

        $maxVoucherNo = TRNDTL::where('v_type', 'SRN')->where('cid', $user)->max('v_no');
        $maxVoucherNo = $maxVoucherNo ? $maxVoucherNo + 1 : 1;

        TRNDTL::create([
            'v_no' => $maxVoucherNo,
            'account_id' => $request->customer_id,
            'cash_id' => $erpParams->saleCreditAccount,
            'credit' => 0,
            'debit' => $grandTotalAfterDiscount,
            'file_id' => $filePath,

            'date' => $request->v_date,
            'v_type' => 'SRN',
            'preparedby' => Auth::user()->name,
            'r_id' => $v_no,
            'cid' => $user
        ]);

        // Store saleReturnInvoice items
        foreach ($request->items as $item) {
            $exclusive = $item['qty'] * $item['rate'];
            $taxAmount = $exclusive * ($item['tax_rate'] / 100);
            $inclusive = $exclusive + $taxAmount;
            $discount = $item['discount'];
            $discount_perc = $item['discount_perc'];

            SaleReturnInvoice::create([
                'v_no' => $v_no,
                'customer_id' => $request->customer_id,
                'item_id' => $item['item_id'],
                'qty' => $item['qty'],
                'rate' => $item['rate'],
                'exclusive' => $exclusive,
                'tax_rate' => $item['tax_rate'],
                'tax_amount' => $taxAmount,
                'inclusive' => $inclusive,
                'discount_perc' => $discount_perc,
                'discount' => $discount ?? 0,
                'cid' => $user
            ]);
        }

        return redirect()->route('sale_return_invoices.index')->with('success', "Sale Return Invoice #$v_no created successfully.");
    }

    // Show the form for editing the specified sale return invoice
    public function edit($v_no)
    {
        $user = Auth::User()->cid;

        $saleMaster = SaleReturnMaster::where('v_no', $v_no)
            ->where('cid', $user)
            ->firstOrFail();

        $items = ProductMaster::where('cid', $user)->get();
        $customers = Customer::where('cid', $user)->get();
        $invoiceItems = SaleReturnInvoice::where('v_no', $v_no)
            ->where('cid', $user)
            ->get();
        $maxVoucherNo = TRNDTL::where('v_type', 'SRN')->where('r_id', $v_no)->where('cid', $user)->firstOrFail();

        return view('sale_return_invoices.edit', compact('saleMaster', 'invoiceItems', 'items', 'customers', 'maxVoucherNo'));
    }

    // Update the specified sale return invoice
    public function update(Request $request, $v_no)
    {
        if (!$request->has('items') || empty($request->items)) {
            return redirect()->back()->withInput()->withErrors(['items' => 'Please add at least one item.']);
        }
        $user = Auth::User()->cid;

        $request->validate([
            'customer_id' => 'required|exists:customers,acc_id',
            'items.*.item_id' => 'required|exists:product_masters,id',
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
        $master = SaleReturnMaster::where('v_no', $v_no)->where('cid', $user)->firstOrFail();

        $transaction = TRNDTL::where('v_type', 'SRN')->where('r_id', $v_no)->where('cid', $user)->firstOrFail();
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
            'discount' => $grandDiscount ?? 0,
            'gate_pass_no' => $request->gatepass_no,

            'v_date' => $request->v_date
        ]);
        $transaction->update([
            'credit' => 0,
            'debit' => $grandTotalAfterDiscount,
            ' date' => $request->v_date,
            'file_id' => $filePath,
        ]);

        // Delete existing items and re-insert
        SaleReturnInvoice::where('v_no', $v_no)->delete();
        $customer = Customer::find($request->customer_id);

        foreach ($request->items as $item) {
            $exclusive = $item['qty'] * $item['rate'];
            $taxAmount = $exclusive * ($item['tax_rate'] / 100);
            $inclusive = $exclusive + $taxAmount;
            $discount = $item['discount'];
            $discount_perc = $item['discount_perc'];
            SaleReturnInvoice::create([
                'v_no' => $v_no,
                'customer_id' => $request->customer_id,
                'item_id' => $item['item_id'],
                'qty' => $item['qty'],
                'rate' => $item['rate'],
                'exclusive' => $exclusive,
                'tax_rate' => $item['tax_rate'],
                'tax_amount' => $taxAmount,
                'inclusive' => $inclusive,
                'discount_perc' => $discount_perc,
                'discount' => $discount ?? 0,
                'cid' => $user
            ]);
        }

        return redirect()->route('sale_return_invoices.index')->with('success', "Sale Return Invoice #$v_no updated successfully.");
    }

    // Remove the specified sale return invoice
    public function destroy($v_no)
    {
        $user = Auth::User()->cid;

        SaleReturnMaster::where('v_no', $v_no)->where('cid', $user)->delete();
        SaleReturnInvoice::where('v_no', $v_no)->where('cid', $user)->delete();

        TRNDTL::where('r_id', $v_no)->where('v_type', 'SRN')->where('cid', $user)->delete();
        return redirect()->route('sale_return_invoices.index')->with('success', "Sale Return Invoice #$v_no deleted successfully.");
    }
    public function print($v_no)
    {
        $user = Auth::user()->cid;

        $master = \App\Models\SaleReturnInvoice::where('v_no', $v_no)
            ->where('cid', $user)
            ->firstOrFail();
        $rmaster = \App\Models\SaleReturnMaster::where('v_no', $v_no)
            ->where('cid', $user)
            ->firstOrFail();

        $items = \App\Models\SaleReturnInvoice::with('item')
            ->where('v_no', $v_no)
            ->where('cid', $user)
            ->get();

        $customer = \App\Models\Customers::where('acc_id', $items->first()->customer_id ?? null)->first();

        return view('sale_return_invoices.print', compact('master', 'items', 'customer', 'rmaster'));
    }
}
