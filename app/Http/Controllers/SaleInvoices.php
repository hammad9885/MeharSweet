<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Customers;
use App\Models\DeliveryChallanDetail;
use App\Models\DeliveryChallanMaster;
use App\Models\ErpParam;
use App\Models\ItemMaster;
use App\Models\Module;
use App\Models\ProductMaster;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseMaster;
use App\Models\ReferenceName;
use App\Models\SaleInvoices as ModelsSaleInvoices;
use App\Models\SaleMasters;
use App\Models\SaleOrderMaster;
use App\Models\ConsumptionMaster;
use App\Models\TransportCompany;
use App\Models\TRNDTL;
use App\Models\UserPermission;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class SaleInvoices extends Controller
{
    private function getProductConsumptionQpcMap($user)
    {
        return ConsumptionMaster::where('cid', $user)
            ->whereNotNull('qty_per_carton')
            ->where('qty_per_carton', '>', 0)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get()
            ->unique('item_id')
            ->pluck('qty_per_carton', 'item_id');
    }

    private function resolveCustomerTransportCompanyId($customerId, $user)
    {
        if (!$customerId) return null;
        $customer = Customers::where('acc_id', $customerId)->where('cid', $user)->first();
        if (!$customer) return null;

        $title = trim($customer->company_title ?? '');
        if ($title) {
            $tcId = TransportCompany::where('cid', $user)->where('name', $title)->value('id');
            if ($tcId) return $tcId;
        }

        $trans = trim($customer->transport_company ?? '');
        if ($trans) {
            $tcId = TransportCompany::where('cid', $user)->where('name', $trans)->value('id');
            if ($tcId) return $tcId;
        }

        $dcTcId = DeliveryChallanMaster::where('customer_id', $customerId)
            ->where('cid', $user)
            ->whereNotNull('transport_company_id')
            ->latest('id')
            ->value('transport_company_id');
        if ($dcTcId) return $dcTcId;

        return SaleOrderMaster::where('customer_id', $customerId)
            ->where('cid', $user)
            ->whereNotNull('transport_company_id')
            ->latest('id')
            ->value('transport_company_id');
    }

    private function resolveCustomerBiltyNo($customerId, $user, $currentDcVno = null)
    {
        if (!$customerId) return null;

        if ($currentDcVno) {
            $dcBilty = DeliveryChallanMaster::where('v_no', $currentDcVno)
                ->where('cid', $user)
                ->whereNotNull('bilty_no')
                ->where('bilty_no', '!=', '')
                ->value('bilty_no');
            if ($dcBilty) return $dcBilty;
        }

        $latestDcBilty = DeliveryChallanMaster::where('customer_id', $customerId)
            ->where('cid', $user)
            ->whereNotNull('bilty_no')
            ->where('bilty_no', '!=', '')
            ->latest('id')
            ->value('bilty_no');
        if ($latestDcBilty) return $latestDcBilty;

        $latestSmBilty = SaleMasters::where('customer_id', $customerId)
            ->where('cid', $user)
            ->whereNotNull('bilty_no')
            ->where('bilty_no', '!=', '')
            ->latest('v_no')
            ->value('bilty_no');
        if ($latestSmBilty) return $latestSmBilty;

        return SaleOrderMaster::where('customer_id', $customerId)
            ->where('cid', $user)
            ->whereNotNull('bilty_no')
            ->where('bilty_no', '!=', '')
            ->latest('id')
            ->value('bilty_no');
    }

    private function ensureSchemaExists()
    {
        try {
            if (!Schema::hasColumn('sale_master', 'freight')) {
                Schema::table('sale_master', function (Blueprint $table) {
                    $table->decimal('freight', 15, 2)->default(0)->nullable()->after('discount');
                });
            }
            if (!Schema::hasColumn('sale_master', 'bilty_no')) {
                Schema::table('sale_master', function (Blueprint $table) {
                    $table->string('bilty_no')->nullable()->after('dc_vno');
                });
            }
            if (!Schema::hasColumn('sale_master', 'transport_company_id')) {
                Schema::table('sale_master', function (Blueprint $table) {
                    $table->unsignedBigInteger('transport_company_id')->nullable()->after('bilty_no');
                });
            }
            if (!Schema::hasColumn('delivery_challan_master', 'freight')) {
                Schema::table('delivery_challan_master', function (Blueprint $table) {
                    $table->decimal('freight', 15, 2)->default(0)->nullable()->after('qty_per_carton');
                });
            }
        } catch (\Throwable $e) {
            // Ignore if schema already updated or permission issues
        }
    }

    public function index(Request $request)
    {
        $this->ensureSchemaExists();
        $user = Auth::user()->cid;
        $module = Module::where('name', 'Sale Invoice')->first();
        if ($module) {
            $perm = UserPermission::where('module_id', $module->id)->where('cid', $user)->where('userid', Auth::user()->id)->where('read', 1);
            if (!$perm->exists()) {
                return redirect()->back()->with('error', 'You have not Permission. Please Contact with your admin');
            }
        }
        $startDate = request('startDate');
        $endDate = request('endDate');
        $query = SaleMasters::where('cid', $user)->with(['items' => function ($query) {
            $query->where('cid', auth()->user()->cid);
        }, 'referenceName']);

        if (request('voucher_no')) {
            $query->where('v_no', request('voucher_no'));
        }

        if (request('customer_id')) {
            $query->whereHas('items', function ($q) {
                $q->where('customer_id', request('customer_id'));
            });
        }

        if (request('item_id')) {
            $query->whereHas('items', function ($q) {
                $q->where('item_id', request('item_id'));
            });
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

        $user = Auth::user()->cid;
        $customers = Customers::where('cid', $user)->get();
        $items = ItemMaster::where('cid', $user)->get();
        return \Inertia\Inertia::render('Sales/SaleInvoices/Index', [
            'invoices' => $invoices,
            'customers' => $customers,
            'items' => $items,
            'filters' => request()->all(),
        ]);
    }

    public function create(Request $request)
    {
        $this->ensureSchemaExists();
        $user = Auth::user()->cid;
        $module = Module::where('name', 'Sale Invoice')->first();
        if ($module) {
            $perm = UserPermission::where('module_id', $module->id)->where('cid', $user)->where('userid', Auth::user()->id)->where('write', 1);
            if (!$perm->exists()) {
                return redirect()->back()->with('error', 'You have not Permission. Please Contact with your admin');
            }
        }
        $suppliers = Customers::where('cid', $user)->get();
        $items = ProductMaster::where('cid', $user)->get();
        $qpcMap = $this->getProductConsumptionQpcMap($user);
        foreach ($items as $item) {
            $item->consumption_qpc = $qpcMap[$item->id] ?? null;
        }
        $transportCompanies = TransportCompany::where('cid', $user)->get();
        $preselectedSupplier = $request->query('supplier_id');
        $preselectedDc = $request->query('dc_vno');
        $preselectedDcDate = null;
        $preselectedDcRefNameId = null;
        $preselectedDcFreight = 0;
        $preselectedDcBiltyNo = null;
        $preselectedDcTransportCompanyId = null;
        if ($preselectedDc) {
            $dc = DeliveryChallanMaster::where('v_no', $preselectedDc)
                ->where('cid', $user)
                ->first();
            if ($dc) {
                $preselectedDcDate = $dc->v_date;
                $preselectedDcRefNameId = $dc->ref_name_id;
                $preselectedDcFreight = (float)($dc->freight ?? 0);
                $preselectedDcBiltyNo = $dc->bilty_no;
                $preselectedDcTransportCompanyId = $dc->transport_company_id;
                if (!$preselectedSupplier) {
                    $preselectedSupplier = $dc->customer_id;
                }
            }
        }

        if ($preselectedSupplier) {
            if (empty($preselectedDcTransportCompanyId)) {
                $preselectedDcTransportCompanyId = $this->resolveCustomerTransportCompanyId($preselectedSupplier, $user);
            }
            if (empty($preselectedDcBiltyNo)) {
                $preselectedDcBiltyNo = $this->resolveCustomerBiltyNo($preselectedSupplier, $user, $preselectedDc);
            }
        }

        $custTransportMap = [];
        $custBiltyMap = [];
        $allTcsByName = $transportCompanies->keyBy(function ($tc) {
            return trim($tc->name);
        });
        foreach ($suppliers as $sup) {
            $tcId = null;
            if (!empty($sup->company_title) && isset($allTcsByName[trim($sup->company_title)])) {
                $tcId = $allTcsByName[trim($sup->company_title)]->id;
            } elseif (!empty($sup->transport_company) && isset($allTcsByName[trim($sup->transport_company)])) {
                $tcId = $allTcsByName[trim($sup->transport_company)]->id;
            }
            if ($tcId) {
                $custTransportMap[$sup->acc_id] = $tcId;
            }
        }

        $referenceNames = ReferenceName::where('cid', $user)->orderByDesc('id')->get();

        return \Inertia\Inertia::render('Sales/SaleInvoices/CreateEdit', compact('suppliers', 'items', 'transportCompanies', 'preselectedSupplier', 'preselectedDc', 'preselectedDcDate', 'preselectedDcRefNameId', 'preselectedDcFreight', 'preselectedDcBiltyNo', 'preselectedDcTransportCompanyId', 'referenceNames', 'custTransportMap', 'custBiltyMap'));
    }

    public function store(Request $request)
    {
        $this->ensureSchemaExists();
        if (!$request->has('items') || empty($request->items)) {
            return redirect()->back()->withInput()->withErrors(['items' => 'Please add at least one item.']);
        }
        $user = Auth::user()->cid;

        $request->validate([
            'supplier_id' => 'required|exists:customers,acc_id',
            'items.*.item_id' => 'required|exists:product_masters,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.rate' => 'required|numeric',
            'items.*.tax_rate' => 'required|numeric',
            'items.*.discount' => 'nullable|numeric',
            'grand_discount' => 'nullable|numeric',
            'freight' => 'nullable|numeric',
        ]);

        $filePath = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->move(public_path('storage/uploads'), $fileName);
            $filePath = 'uploads/' . $fileName;
        }

        $v_no = SaleMasters::where('cid', $user)->max('v_no');
        if ($v_no) {
            $v_no = $v_no + 1;
        } else {
            $v_no = 1;
        }

        $erpParams = ErpParam::where('cid', $user)->first();

        if (! $erpParams || ! $erpParams->saleCreditAccount()) {
            return back()->withErrors(['error' => 'Sales Chart of Account configuration is missing.']);
        }

        $grandTotal = 0;

        foreach ($request->items as $item) {
            $exclusive = (float)$item['qty'] * (float)$item['rate'];
            $taxAmount = $exclusive * ((float)($item['tax_rate'] ?? 0) / 100);
            $inclusive = $exclusive + $taxAmount;
            $finalAfterDiscount = $inclusive - (float)($item['discount'] ?? 0);
            $grandTotal += $finalAfterDiscount;
        }

        $grandDiscountAmt = (float)($request->grand_discount ?? 0);
        $freight = (float)($request->freight ?? 0);
        $preBal = (float)($request->pre_bal ?? 0);
        $grandTotalAfterDiscount = ($grandTotal - $grandDiscountAmt) + $freight + $preBal;
        $supplier = Customers::where('acc_id', $request->supplier_id)->first();
        $master = SaleMasters::create([
            'v_no' => $v_no,
            'gate_pass_no' => $request->gatepass_no,
            'grand_total' => $grandTotalAfterDiscount,
            'discount' => $grandDiscountAmt,
            'freight' => $freight,
            'cid' => $user,
            'v_date' => $request->v_date,
            'customer_id' => $supplier->acc_id,
            'dc_vno' => $request->dc_vno,
            'ref_name_id' => $request->ref_name_id,
            'bilty_no' => $request->bilty_no,
            'transport_company_id' => $request->transport_company_id,
        ]);

        if ($request->dc_vno) {
            DeliveryChallanMaster::where('v_no', $request->dc_vno)->where('cid', $user)->update([
                'bilty_no' => $request->bilty_no,
                'transport_company_id' => $request->transport_company_id,
            ]);
        }

        $maxVoucherNo = TRNDTL::where('v_type', 'SIN')->where('cid', $user)->max('v_no');
        if ($maxVoucherNo) {
            $maxVoucherNo = $maxVoucherNo + 1;
        } else {
            $maxVoucherNo = 1;
        }
        TRNDTL::create([
            'v_no' => $maxVoucherNo,
            'account_id' => $supplier->acc_id,
            'cash_id' => $erpParams->saleCreditAccount,
            'credit' => ($grandTotal - $grandDiscountAmt) + $freight,
            'debit' => 0,
            'pre_bal' => $preBal,
            'date' => $request->v_date,
            'v_type' => 'SIN',
            'preparedby' => Auth::User()->name,
            'r_id' => $v_no,
            'file_id' => $filePath,
            'cid' => $user,
        ]);

        foreach ($request->items as $item) {
            $exclusive = (float)$item['qty'] * (float)$item['rate'];
            $taxAmount = $exclusive * ((float)($item['tax_rate'] ?? 0) / 100);
            $discount = (float)($item['discount'] ?? 0);
            $inclusive = $exclusive + $taxAmount;
            $discount_perc = (float)($item['discount_perc'] ?? 0);

            ModelsSaleInvoices::create([
                'v_no' => $v_no,
                'customer_id' => $request->supplier_id,
                'item_id' => $item['item_id'],
                'qty' => $item['qty'],
                'rate' => $item['rate'],
                'exclusive' => $exclusive,
                'tax_rate' => $item['tax_rate'] ?? 0,
                'tax_amount' => $taxAmount,
                'inclusive' => $inclusive,
                'discount_perc' => $discount_perc,
                'discount' => $discount,
                'cid' => $user,
            ]);
        }

        return redirect()->route('sale_invoices.index')->with('success', "Sale Invoice #$v_no created successfully.");
    }

    public function edit($v_no)
    {
        $this->ensureSchemaExists();
        $user = Auth::user()->cid;
        $module = Module::where('name', 'Sale Invoice')->first();
        if ($module) {
            $perm = UserPermission::where('module_id', $module->id)->where('cid', $user)->where('userid', Auth::user()->id)->where('update', 1);
            if (!$perm->exists()) {
                return redirect()->back()->with('error', 'You have not Permission. Please Contact with your admin');
            }
        }

        $purchaseMaster = SaleMasters::where('v_no', $v_no)
            ->where('cid', $user)
            ->firstOrFail();

        $items = ProductMaster::where('cid', $user)->get();
        $qpcMap = $this->getProductConsumptionQpcMap($user);
        foreach ($items as $item) {
            $item->consumption_qpc = $qpcMap[$item->id] ?? null;
        }

        $suppliers = Customers::where('cid', $user)->get();

        $invoiceItems = ModelsSaleInvoices::where('v_no', $v_no)
            ->where('cid', $user)
            ->get();

        $dcCartonData = collect();
        $dcQpc = 0;
        $dcMaster = null;
        if ($purchaseMaster->dc_vno) {
            $dcMaster = DeliveryChallanMaster::where('v_no', $purchaseMaster->dc_vno)->where('cid', $user)->first();
            if ($dcMaster) {
                $dcQpc = (float)($dcMaster->qty_per_carton ?? 0);
                $dcItems = DeliveryChallanDetail::where('v_no', $purchaseMaster->dc_vno)->where('cid', $user)->orderBy('id')->get();
                $dcCartonData = $dcItems->groupBy('item_id')->map(function ($rows) {
                    return $rows->sum('load_qty');
                });
            }
        }

        $productQpc = collect();
        $allItemIds = $invoiceItems->pluck('item_id')->merge($dcCartonData->keys())->unique();
        foreach ($allItemIds as $itemId) {
            $q = $qpcMap[$itemId] ?? null;
            if (!$q && $dcQpc > 0) {
                $q = $dcQpc;
            }
            if (!$q) {
                $invItem = $invoiceItems->firstWhere('item_id', $itemId);
                $cartons = $dcCartonData[$itemId] ?? null;
                if ($invItem && $cartons && $cartons > 0 && $invItem->qty > 0) {
                    $q = round($invItem->qty / $cartons);
                }
            }
            $productQpc[$itemId] = ($q && $q > 0) ? (int)$q : 1;
        }

        if (empty($purchaseMaster->transport_company_id)) {
            $purchaseMaster->transport_company_id = $dcMaster?->transport_company_id
                ?: $this->resolveCustomerTransportCompanyId($purchaseMaster->customer_id, $user);
        }

        if (empty($purchaseMaster->bilty_no)) {
            $purchaseMaster->bilty_no = (!empty($dcMaster?->bilty_no) ? $dcMaster->bilty_no : null)
                ?: $this->resolveCustomerBiltyNo($purchaseMaster->customer_id, $user, $purchaseMaster->dc_vno);
        }

        $maxVoucherNo = TRNDTL::where('v_type', 'SIN')->where('r_id', $v_no)->where('cid', $user)->firstOrFail();

        if ($purchaseMaster->freight === null) {
            if ($dcMaster && $dcMaster->freight !== null && (float)$dcMaster->freight > 0) {
                $purchaseMaster->freight = (float)$dcMaster->freight;
            } else {
                $itemsSubtotal = $invoiceItems->sum(function ($item) {
                    return ($item->inclusive ?? ((float)$item->qty * (float)$item->rate)) - (float)($item->discount ?? 0);
                });
                $calcFreight = round((float)$purchaseMaster->grand_total - ($itemsSubtotal - (float)$purchaseMaster->discount) - (float)($maxVoucherNo->pre_bal ?? 0), 2);
                $purchaseMaster->freight = $calcFreight > 0 ? $calcFreight : 0;
            }
        }

        $rawDate = $purchaseMaster->v_date;
        $invoiceDate = null;
        foreach (['d/m/Y', 'd-m-Y', 'Y-m-d', 'Y/m/d'] as $fmt) {
            try {
                $invoiceDate = \Carbon\Carbon::createFromFormat($fmt, $rawDate)->format('Y-m-d');
                break;
            } catch (\Exception $e) {}
        }
        if (!$invoiceDate) {
            $invoiceDate = now()->format('Y-m-d');
        }

        $preBal = $maxVoucherNo->pre_bal;

        if ($preBal === null) {
            $preBal = DB::table('trn_dtl_views')
                ->where('aid', $purchaseMaster->customer_id)
                ->where('cid', $user)
                ->where('v_date', '<=', $invoiceDate)
                ->where(function ($q) use ($maxVoucherNo) {
                    $q->where('v_type', '!=', 'SIN')
                        ->orWhere('v_no', '!=', $maxVoucherNo->v_no);
                })
                ->sum(DB::raw('CAST(credit AS DECIMAL(15,2)) - CAST(debit AS DECIMAL(15,2))'));
        }

        $closingBalance = $preBal;

        $referenceNames = ReferenceName::where('cid', $user)->orderByDesc('id')->get();
        $transportCompanies = TransportCompany::where('cid', $user)->get();

        $custTransportMap = [];
        $custBiltyMap = [];
        $allTcsByName = $transportCompanies->keyBy(function ($tc) {
            return trim($tc->name);
        });
        foreach ($suppliers as $sup) {
            $tcId = null;
            if (!empty($sup->company_title) && isset($allTcsByName[trim($sup->company_title)])) {
                $tcId = $allTcsByName[trim($sup->company_title)]->id;
            } elseif (!empty($sup->transport_company) && isset($allTcsByName[trim($sup->transport_company)])) {
                $tcId = $allTcsByName[trim($sup->transport_company)]->id;
            }
            if ($tcId) {
                $custTransportMap[$sup->acc_id] = $tcId;
            }
        }

        return \Inertia\Inertia::render('Sales/SaleInvoices/CreateEdit', array_merge(
            compact('purchaseMaster', 'invoiceItems', 'items', 'suppliers', 'maxVoucherNo', 'dcCartonData', 'dcQpc', 'productQpc', 'preBal', 'closingBalance', 'referenceNames', 'transportCompanies', 'custTransportMap', 'custBiltyMap'),
            ['isEdit' => true]
        ));
    }

    public function update(Request $request, $v_no)
    {
        $this->ensureSchemaExists();
        $user = Auth::user()->cid;
        if (!$request->has('items') || empty($request->items)) {
            return redirect()->back()->withInput()->withErrors(['items' => 'Please add at least one item.']);
        }

        $request->validate([
            'supplier_id' => 'required',
            'items.*.item_id' => 'required|exists:product_masters,id',
            'items.*.qty' => 'required|numeric',
            'items.*.rate' => 'required|numeric',
            'items.*.tax_rate' => 'required|numeric',
            'items.*.discount' => 'nullable|numeric',
            'grand_discount' => 'nullable|numeric',
            'freight' => 'nullable|numeric',
        ]);

        $filePath = $request->old_file ?? null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->move(public_path('storage/uploads'), $fileName);
            $filePath = 'uploads/' . $fileName;
        }
        $master = SaleMasters::where('v_no', $v_no)->where('cid', $user)->firstOrFail();
        $maxVoucherNo = TRNDTL::where('v_type', 'SIN')->where('r_id', $v_no)->where('cid', $user)->firstOrFail();

        $grandTotal = 0;

        foreach ($request->items as $item) {
            $exclusive = (float)$item['qty'] * (float)$item['rate'];
            $taxAmount = $exclusive * ((float)($item['tax_rate'] ?? 0) / 100);
            $inclusive = $exclusive + $taxAmount;
            $finalAfterDiscount = $inclusive - (float)($item['discount'] ?? 0);
            $grandTotal += $finalAfterDiscount;
        }

        $grandDiscountAmt = (float)($request->grand_discount ?? 0);
        $freight = (float)($request->freight ?? 0);
        $preBal = (float)($request->pre_bal ?? 0);
        $netAmount = ($grandTotal - $grandDiscountAmt) + $freight + $preBal;

        $master->update([
            'grand_total' => $netAmount,
            'discount' => $grandDiscountAmt,
            'freight' => $freight,
            'gate_pass_no' => $request->gatepass_no,
            'v_date' => $request->v_date,
            'ref_name_id' => $request->ref_name_id,
            'bilty_no' => $request->bilty_no,
            'transport_company_id' => $request->transport_company_id,
        ]);

        if ($master->dc_vno) {
            DeliveryChallanMaster::where('v_no', $master->dc_vno)->where('cid', $user)->update([
                'bilty_no' => $request->bilty_no,
                'transport_company_id' => $request->transport_company_id,
            ]);
        }
        $maxVoucherNo->update([
            'credit' => ($grandTotal - $grandDiscountAmt) + $freight,
            'pre_bal' => $preBal,
            'file_id' => $filePath,
            'date' => $request->v_date,
        ]);

        ModelsSaleInvoices::where('v_no', $v_no)->delete();

        foreach ($request->items as $item) {
            $exclusive = $item['qty'] * $item['rate'];
            $taxAmount = $exclusive * ($item['tax_rate'] / 100);
            $discount  = $item['discount'];
            $inclusive = $exclusive + $taxAmount;
            $discount_perc = $item['discount_perc'];

            ModelsSaleInvoices::create([
                'v_no' => $v_no,
                'customer_id' => $request->supplier_id,
                'item_id' => $item['item_id'],
                'qty' => $item['qty'],
                'rate' => $item['rate'],
                'exclusive' => $exclusive,
                'tax_rate' => $item['tax_rate'],
                'tax_amount' => $taxAmount,
                'inclusive' => $inclusive,
                'cid' => $user,
                'discount_perc' => $discount_perc,
                'discount' => $discount ?? 0,
            ]);
        }

        return redirect()->route('sale_invoices.index')->with('success', "Sale Invoice #$v_no updated successfully.");
    }

    public function destroy($v_no)
    {
        $user = Auth::user()->cid;

        SaleMasters::where('v_no', $v_no)->where('cid', $user)->delete();
        ModelsSaleInvoices::where('v_no', $v_no)->where('cid', $user)->delete();
        TRNDTL::where('r_id', $v_no)->where('v_type', 'SIN')->where('cid', $user)->delete();

        return redirect()->route('sale_invoices.index')->with('success', "Sale Invoice #$v_no deleted successfully.");
    }

    public function print($v_no)
    {
        $this->ensureSchemaExists();
        $user = Auth::user()->cid;

        $master = SaleMasters::where('v_no', $v_no)
            ->where('cid', $user)
            ->firstOrFail();

        $items = ModelsSaleInvoices::with('item')
            ->where('v_no', $v_no)
            ->where('cid', $user)
            ->get();

        $customer = Customers::where('acc_id', $items->first()->customer_id ?? null)->first();

        $dcCartonData = collect();
        $dcQpc = 0;
        $dcBiltyNo = null;
        $dcTransportCompany = null;
        if ($master->dc_vno) {
            $dcMaster = DeliveryChallanMaster::where('v_no', $master->dc_vno)->where('cid', $user)->first();
            if ($dcMaster) {
                $dcQpc = (float)($dcMaster->qty_per_carton ?? 0);
                $dcItems = DeliveryChallanDetail::where('v_no', $master->dc_vno)->where('cid', $user)->orderBy('id')->get();
                $dcCartonData = $dcItems->groupBy('item_id')->map(function ($rows) {
                    return $rows->sum('load_qty');
                });
                $dcBiltyNo = $master->bilty_no ?: ($dcMaster->bilty_no ?? null);
                $transportCompanyId = $master->transport_company_id ?: ($dcMaster->transport_company_id ?? null);
                $dcTransportCompany = $transportCompanyId
                    ? \App\Models\TransportCompany::find($transportCompanyId)?->name
                    : null;
            }
        }

        $qpcMap = $this->getProductConsumptionQpcMap($user);
        $productQpc = collect();
        foreach ($items as $invItem) {
            $q = $qpcMap[$invItem->item_id] ?? null;
            if (!$q && $dcQpc > 0) {
                $q = $dcQpc;
            }
            if (!$q) {
                $cartons = $dcCartonData[$invItem->item_id] ?? null;
                if ($cartons && $cartons > 0 && $invItem->qty > 0) {
                    $q = round($invItem->qty / $cartons);
                }
            }
            $productQpc[$invItem->item_id] = ($q && $q > 0) ? (int)$q : 1;
        }

        if (empty($dcBiltyNo)) {
            $dcBiltyNo = $master->bilty_no
                ?: $this->resolveCustomerBiltyNo($customer?->acc_id ?? $master->customer_id, $user, $master->dc_vno);
        }
        if (empty($dcTransportCompany)) {
            $tcId = $master->transport_company_id
                ?: $this->resolveCustomerTransportCompanyId($customer?->acc_id ?? $master->customer_id, $user);
            $dcTransportCompany = $tcId
                ? \App\Models\TransportCompany::find($tcId)?->name
                : null;
        }

        $preBal = TRNDTL::where('v_type', 'SIN')->where('r_id', $v_no)->where('cid', $user)->value('pre_bal') ?? 0;

        // Fetch the Reference Name text linked to this invoice's ref_name_id
        $referenceNameObj = $master->ref_name_id
            ? ReferenceName::where('id', $master->ref_name_id)->where('cid', $user)->first()
            : null;
        $referenceName = $referenceNameObj
            ? ($referenceNameObj->name . ($referenceNameObj->name_urdu ? ' - ' . $referenceNameObj->name_urdu : ''))
            : null;

        return view('sale_invoices.print', compact('master', 'items', 'customer', 'dcCartonData', 'dcQpc', 'productQpc', 'dcBiltyNo', 'dcTransportCompany', 'preBal', 'referenceName'));
    }

    public function getDcsByCustomer($customerId)
    {
        $user = Auth::user()->cid;
        $dcs = DeliveryChallanMaster::where('cid', $user)
            ->where('customer_id', $customerId)
            ->orderByDesc('id')
            ->get(['v_no', 'v_date', 'qty_per_carton', 'bilty_no', 'transport_company_id']);

        $defaultTransportCompanyId = $this->resolveCustomerTransportCompanyId($customerId, $user);
        $defaultBiltyNo = $this->resolveCustomerBiltyNo($customerId, $user);

        return response()->json([
            'dcs' => $dcs,
            'default_transport_company_id' => $defaultTransportCompanyId,
            'default_bilty_no' => $defaultBiltyNo,
        ]);
    }

    public function getDcItems($v_no)
    {
        $user = Auth::user()->cid;
        $master = DeliveryChallanMaster::where('v_no', $v_no)
            ->where('cid', $user)
            ->first();

        if ($master) {
            if (empty($master->transport_company_id)) {
                $master->transport_company_id = $this->resolveCustomerTransportCompanyId($master->customer_id, $user);
            }
            if (empty($master->bilty_no)) {
                $master->bilty_no = $this->resolveCustomerBiltyNo($master->customer_id, $user, $master->v_no);
            }
        }

        $qpcMap = $this->getProductConsumptionQpcMap($user);
        $items = DeliveryChallanDetail::with('item')
            ->where('v_no', $v_no)
            ->where('cid', $user)
            ->orderBy('id')
            ->get()
            ->map(function ($detail) use ($user, $qpcMap) {
                $consumptionQpc = null;
                if (!empty($detail->lot_no)) {
                    $lotConsumption = ConsumptionMaster::where('item_id', $detail->item_id)
                        ->where('lot_no', $detail->lot_no)
                        ->where('cid', $user)
                        ->whereNotNull('qty_per_carton')
                        ->where('qty_per_carton', '>', 0)
                        ->orderByDesc('date')
                        ->orderByDesc('id')
                        ->first();
                    if ($lotConsumption) {
                        $consumptionQpc = (int) $lotConsumption->qty_per_carton;
                    }
                }
                if (!$consumptionQpc) {
                    $consumptionQpc = $qpcMap[$detail->item_id] ?? null;
                }
                $detail->consumption_qpc = $consumptionQpc ? (int) $consumptionQpc : 0;
                return $detail;
            });

        return response()->json([
            'master' => $master,
            'items' => $items,
        ]);
    }
}