<?php

namespace App\Http\Controllers;
use App\Models\AccountMaster;
use App\Models\ErpParam;
use App\Models\Level2;
use App\Models\PurchaseInvoice;
use App\Models\ItemMaster;
use App\Models\PurchaseMaster;
use App\Models\Contractor;
use App\Models\Supplier;
use App\Models\TRNDTL;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user()->cid;
        $query = PurchaseMaster::where('cid', $user)->with(['items' => function ($query) {
            $query->where('cid', auth()->user()->cid);
        }, 'supplier' => function ($query) {
            $query->where('cid', auth()->user()->cid);
        }]);

        if ($request->filled('voucher_no')) {
            $query->where('v_no', $request->voucher_no);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('item_id')) {
            $query->whereHas('items', function ($q) use ($request) {
                $q->where('item_id', $request->item_id);
            });
        }

        if ($request->filled('startDate')) {
            $query->whereDate('v_date', '>=', $request->startDate);
        }

        if ($request->filled('endDate')) {
            $query->whereDate('v_date', '<=', $request->endDate);
        }

        $invoices = $query->orderByDesc('v_no')->get();

        if (request()->wantsJson() || request()->header('X-Inertia')) {
            $suppliers = Supplier::where('cid', $user)->get();
            $items = ItemMaster::where('cid', $user)->get();
            return \Inertia\Inertia::render('Purchase/PurchaseInvoices/Index', [
                'invoices' => $invoices,
                'suppliers' => $suppliers,
                'items' => $items,
                'filters' => $request->all(),
            ]);
        }

        return view('purchase_invoices.index', compact('invoices'));
    }

    public function create()
    {
        $user = Auth::user()->cid;
        $suppliers = Supplier::where('cid', $user)->get();
        $items = ItemMaster::with('itemtypes', 'itemunits')->where('cid', $user)->get();
        $contractorLevel = Level2::where('cid', $user)->where('title', 'Contractor')->value('id');
        $contractors = AccountMaster::where('cid', $user);
        if ($contractorLevel) {
            $contractors = $contractors->where('level2_id', $contractorLevel);
        }
        $contractors = $contractors->get();

        $lastPurchaseRates = PurchaseInvoice::where('cid', $user)
            ->whereIn('item_id', $items->pluck('id'))
            ->orderByDesc('id')
            ->get()
            ->unique('item_id')
            ->pluck('rate', 'item_id');

        if (request()->wantsJson() || request()->header('X-Inertia')) {
            return \Inertia\Inertia::render('Purchase/PurchaseInvoices/CreateEdit', compact('suppliers', 'items', 'contractors', 'lastPurchaseRates'));
        }

        return view('purchase_invoices.create', compact('suppliers', 'items', 'contractors', 'lastPurchaseRates'));
    }

    public function store(Request $request)
    {
        if (!$request->has('items') || empty($request->items)) {
            return redirect()->back()->withInput()->withErrors(['items' => 'Please add at least one item.']);
        }
        $user = Auth::user()->cid;

        $items = array_filter($request->items, function ($item) {
            return !empty($item['item_id']);
        });

        if (empty($items)) {
            return redirect()->back()->withInput()->withErrors(['items' => 'Please fill at least one item.']);
        }

        $request->merge(['items' => array_values($items)]);

        $request->validate([
            'supplier_id' => 'required|exists:supplier,acc_id',
            'items.*.item_id' => 'required|exists:item_masters,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.contractor_id' => 'nullable|exists:account_masters,id',
        ]);


        $latestVNo = PurchaseMaster::where('cid', $user)->latest('v_no')->value('v_no');
        $v_no = $latestVNo ? $latestVNo + 1 : 1;

        $grandTotal = 0;
        $erpParams = ErpParam::where('cid', $user)->first();

        if (!$erpParams || !$erpParams->purchaseDebitAccount) {
            return back()->withErrors(['error' => 'Supplier Chart of Account configuration is missing.']);
        }

        foreach ($request->items as $item) {
            $grandTotal += $item['qty'] * $item['rate'];
        }

        $grandDiscount = $request->grand_discount ?? 0;
        $freight = $request->freight ?? 0;
        $grandTotalAfterDiscount = $grandTotal - $grandDiscount;
        $netAmount = $grandTotalAfterDiscount + $freight;

        $master = PurchaseMaster::create([
            'v_no' => $v_no,
            'gate_pass_no' => $request->gatepass_no,
            'ref_no' => $request->ref_no,
            'remarks' => $request->remarks,
            'v_date' => $request->v_date,
            'supplier_id' => $request->supplier_id,
            'contractor_id' => $items[0]['contractor_id'] ?? null,
            'grand_total' => $grandTotalAfterDiscount,
            'discount' => $grandDiscount ?? 0,
            'freight' => $freight,
            'net_amount' => $netAmount,
            'cid' => $user,
        ]);

        $supplier = Supplier::where('acc_id', $request->supplier_id)->first();
        $maxVoucherNo = TRNDTL::where('v_type', 'PIN')->where('cid', $user)->max('v_no');
        $maxVoucherNo = $maxVoucherNo ? $maxVoucherNo + 1 : 1;

        TRNDTL::create([
            'v_no' => $maxVoucherNo,
            'account_id' => $supplier->acc_id,
            'cash_id' => $erpParams->purchaseDebitAccount->id,
            'credit' => 0,
            'debit' => $netAmount,
            'date' => $request->v_date,
            'v_type' => 'PIN',
            'preparedby' => Auth::user()->name,
            'r_id' => $v_no,
            'cid' => $user,
        ]);

        foreach ($request->items as $item) {
            $amount = $item['qty'] * $item['rate'];

            $rawItem = ItemMaster::with('itemtypes')
                ->where('cid', $user)
                ->find($item['item_id']);
            $itemTypeTitle = $rawItem && $rawItem->itemtypes ? trim(strtolower($rawItem->itemtypes->type_title)) : '';

            if (str_contains($itemTypeTitle, 'dana')) {
                $lotNo = 0;
            } else {
                if (isset($item['lot_no']) && $item['lot_no'] !== '') {
                    $lotNo = $item['lot_no'];
                } else {
                    $maxLot = PurchaseInvoice::where('cid', $user)
                        ->where('item_id', $item['item_id'])
                        ->whereNotNull('lot_no')
                        ->max('lot_no');
                    $lotNo = $maxLot ? $maxLot + 1 : 1;
                }
            }

            PurchaseInvoice::create([
                'v_no' => $v_no,
                'supplier_id' => $request->supplier_id,
                'item_id' => $item['item_id'],
                'qty' => $item['qty'],
                'rate' => $item['rate'],
                'exclusive' => $amount,
                'tax_rate' => 0,
                'tax_amount' => 0,
                'inclusive' => $amount,
                'discount' => 0,
                'discount_perc' => 0,
                'lot_no' => $lotNo,
                'contractor_id' => $item['contractor_id'] ?? null,
                'cid' => $user,
            ]);

            $rawItem = ItemMaster::find($item['item_id']);
            if ($rawItem) {
                $rawItem->cost_price = $item['rate'];
                $rawItem->save();
            }
        }

        return redirect()->route('purchase_invoices.index')
            ->with('success', "Purchase Invoice #$v_no created successfully.");
    }

    public function edit($v_no)
    {
        $user = Auth::user()->cid;

        $purchaseMaster = PurchaseMaster::where('v_no', $v_no)
            ->where('cid', $user)
            ->firstOrFail();

        $items = ItemMaster::with('itemtypes', 'itemunits')->where('cid', $user)->get();
        $suppliers = Supplier::where('cid', $user)->get();
        $contractorLevel = Level2::where('cid', $user)->where('title', 'Contractor')->value('id');
        $contractors = AccountMaster::where('cid', $user);
        if ($contractorLevel) {
            $contractors = $contractors->where('level2_id', $contractorLevel);
        }
        $contractors = $contractors->get();

        $invoiceItems = PurchaseInvoice::where('v_no', $v_no)
            ->where('cid', $user)
            ->whereNotNull('item_id')
            ->get();

        $maxVoucherNo = TRNDTL::where('v_type', 'PIN')->where('r_id', $v_no)
            ->where('cid', $user)->firstOrFail();

        $lastPurchaseRates = PurchaseInvoice::where('cid', $user)
            ->whereIn('item_id', $items->pluck('id'))
            ->orderByDesc('id')
            ->get()
            ->unique('item_id')
            ->pluck('rate', 'item_id');

        if (request()->wantsJson() || request()->header('X-Inertia')) {
            return \Inertia\Inertia::render('Purchase/PurchaseInvoices/CreateEdit', array_merge(
                compact('purchaseMaster', 'invoiceItems', 'items', 'suppliers', 'contractors', 'maxVoucherNo', 'lastPurchaseRates'),
                ['isEdit' => true]
            ));
        }

        return view('purchase_invoices.edit', compact('purchaseMaster', 'invoiceItems', 'items', 'suppliers', 'contractors', 'maxVoucherNo', 'lastPurchaseRates'));
    }

    public function update(Request $request, $v_no)
    {
        if (!$request->has('items') || empty($request->items)) {
            return redirect()->back()->withInput()->withErrors(['items' => 'Please add at least one item.']);
        }
        $user = Auth::user()->cid;

        $items = array_filter($request->items, function ($item) {
            return !empty($item['item_id']);
        });

        if (empty($items)) {
            return redirect()->back()->withInput()->withErrors(['items' => 'Please fill at least one item.']);
        }

        $request->merge(['items' => array_values($items)]);

        $request->validate([
            'supplier_id' => 'required|exists:supplier,acc_id',
            'items.*.item_id' => 'required|exists:item_masters,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.contractor_id' => 'nullable|exists:account_masters,id',
        ]);

        $grandTotal = 0;
        foreach ($request->items as $item) {
            $grandTotal += $item['qty'] * $item['rate'];
        }

        $grandDiscount = $request->grand_discount ?? 0;
        $freight = $request->freight ?? 0;
        $grandTotalAfterDiscount = $grandTotal - $grandDiscount;
        $netAmount = $grandTotalAfterDiscount + $freight;

        $master = PurchaseMaster::where('v_no', $v_no)->where('cid', $user)->firstOrFail();
        $master->update([
            'v_date' => $request->v_date,
            'gate_pass_no' => $request->gatepass_no,
            'ref_no' => $request->ref_no,
            'remarks' => $request->remarks,
            'supplier_id' => $request->supplier_id,
            'contractor_id' => $items[0]['contractor_id'] ?? null,
            'grand_total' => $grandTotalAfterDiscount,
            'discount' => $grandDiscount,
            'freight' => $freight,
            'net_amount' => $netAmount,
        ]);

        $maxVoucherNo = TRNDTL::where('v_type', 'PIN')->where('r_id', $v_no)
            ->where('cid', $user)->firstOrFail();
        $maxVoucherNo->update([
            'debit' => $netAmount,
            'date' => $request->v_date,
        ]);

        PurchaseInvoice::where('v_no', $v_no)->delete();

        foreach ($request->items as $item) {
            $amount = $item['qty'] * $item['rate'];

            $rawItem = ItemMaster::with('itemtypes')
                ->where('cid', $user)
                ->find($item['item_id']);
            $itemTypeTitle = $rawItem && $rawItem->itemtypes ? trim(strtolower($rawItem->itemtypes->type_title)) : '';

            if (str_contains($itemTypeTitle, 'dana')) {
                $lotNo = 0;
            } else {
                if (isset($item['lot_no']) && $item['lot_no'] !== '') {
                    $lotNo = $item['lot_no'];
                } else {
                    $maxLot = PurchaseInvoice::where('cid', $user)
                        ->where('item_id', $item['item_id'])
                        ->whereNotNull('lot_no')
                        ->max('lot_no');
                    $lotNo = $maxLot ? $maxLot + 1 : 1;
                }
            }

            PurchaseInvoice::create([
                'v_no' => $v_no,
                'supplier_id' => $request->supplier_id,
                'item_id' => $item['item_id'],
                'qty' => $item['qty'],
                'rate' => $item['rate'],
                'exclusive' => $amount,
                'tax_rate' => 0,
                'tax_amount' => 0,
                'inclusive' => $amount,
                'discount' => 0,
                'discount_perc' => 0,
                'lot_no' => $lotNo,
                'contractor_id' => $item['contractor_id'] ?? null,
                'cid' => $user,
            ]);

            $rawItem = ItemMaster::find($item['item_id']);
            if ($rawItem) {
                $rawItem->cost_price = $item['rate'];
                $rawItem->save();
            }
        }

        return redirect()->route('purchase_invoices.index')
            ->with('success', "Purchase Invoice #$v_no updated successfully.");
    }

    public function destroy($v_no)
    {
        $user = Auth::user()->cid;

        PurchaseMaster::where('v_no', $v_no)->where('cid', $user)->delete();
        PurchaseInvoice::where('v_no', $v_no)->where('cid', $user)->delete();
        TRNDTL::where('r_id', $v_no)->where('v_type', 'PIN')->where('cid', $user)->delete();

        return redirect()->route('purchase_invoices.index')
            ->with('success', "Purchase Invoice #$v_no deleted successfully.");
    }

    public function getNextPurchaseLotNo($itemId)
    {
        $user = Auth::user()->cid;
        $maxLot = PurchaseInvoice::where('cid', $user)
            ->where('item_id', $itemId)
            ->whereNotNull('lot_no')
            ->max('lot_no');
        $nextLot = $maxLot ? $maxLot + 1 : 1;
        return response()->json(['lot_no' => $nextLot]);
    }

    public function print($v_no)
    {
        $user = Auth::user()->cid;

        $master = PurchaseMaster::where('v_no', $v_no)
            ->where('cid', $user)
            ->firstOrFail();

        $items = PurchaseInvoice::with('item')
            ->where('v_no', $v_no)
            ->where('cid', $user)
            ->get();

        $supplier = Supplier::where('acc_id', $items->first()->supplier_id ?? null)->first();

        return view('purchase_invoices.print', compact('master', 'items', 'supplier'));
    }

    public function report(Request $request)
    {
        $user = Auth::user()->cid;

        // Fetch suppliers and items from existing purchase_invoices data
        $suppliers = Supplier::whereIn('acc_id',
                        PurchaseMaster::where('cid', $user)->pluck('supplier_id')->unique()
                     )->get();

        $items = ItemMaster::whereIn('id',
                    PurchaseInvoice::where('cid', $user)->whereNotNull('item_id')->pluck('item_id')->unique()
                 )->get();

        $vouchers = PurchaseMaster::where('cid', $user)
                       ->orderByDesc('v_no')
                       ->pluck('v_no');

        // Detail rows from purchase_invoices (each item = one row)
        $query = PurchaseInvoice::where('cid', $user)
            ->whereNotNull('item_id')
            ->with([
                'item'       => fn($q) => $q->with('itemunits'),
                'supplier',
                'contractor',
                'master',
            ]);

        if ($request->filled('voucher_no')) {
            $query->where('v_no', $request->voucher_no);
        }
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }
        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }
        if ($request->filled('startDate')) {
            $query->whereHas('master', fn($q) => $q->whereDate('v_date', '>=', $request->startDate));
        }
        if ($request->filled('endDate')) {
            $query->whereHas('master', fn($q) => $q->whereDate('v_date', '<=', $request->endDate));
        }

        $invoices = $query->orderByDesc('v_no')->get();

        if (request()->wantsJson() || request()->header('X-Inertia')) {
            return \Inertia\Inertia::render('Purchase/Reports/ItemBasedPurchase', [
                'invoices' => $invoices,
                'suppliers' => $suppliers,
                'items' => $items,
                'vouchers' => $vouchers,
                'filters' => $request->all(),
            ]);
        }

        return view('purchase_reports.purchase_invoice_report',
            compact('invoices', 'suppliers', 'items', 'vouchers'));
    }
}
