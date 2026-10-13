<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\Module;
use App\Models\ProductMaster;
use App\Models\ReferenceName;
use App\Models\SaleOrderDetail;
use App\Models\SaleOrderMaster;
use App\Models\TransportCompany;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleOrderController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user()->cid;
        $module = Module::where('name', 'Sale Order')->first();
        if ($module) {
            $perm = UserPermission::where('module_id', $module->id)->where('cid', $user)->where('userid', Auth::user()->id)->where('read', 1);
            if (!$perm->exists()) {
                return redirect()->back()->with('error', 'You have not Permission. Please Contact with your admin');
            }
        }
        $query = SaleOrderMaster::where('cid', $user)->with(['details.item', 'customer', 'referenceName']);

        if ($request->voucher_no) {
            $query->where('v_no', 'like', '%' . $request->voucher_no . '%');
        }
        if ($request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }
        $startDate = $request->startDate ?? now()->subMonths(2)->format('Y-m-d');
        $endDate = $request->endDate ?? now()->format('Y-m-d');
        $query->whereDate('v_date', '>=', $startDate);
        $query->whereDate('v_date', '<=', $endDate);
        if ($request->item_id) {
            $query->whereHas('details', function ($q) use ($request) {
                $q->where('item_id', $request->item_id);
            });
        }

        $orders = $query->orderByDesc('v_date')->get();
        $totalOrders = $orders->count();
        $customers = Customers::where('cid', $user)->get();
        $products = ProductMaster::where('cid', $user)->where('finished', 1)->get();

        $totalItemQty = 0;
        $selectedItemName = null;
        if ($request->item_id) {
            $totalItemQty = \App\Models\SaleOrderDetail::whereIn('v_no', $orders->pluck('v_no'))
                ->where('item_id', $request->item_id)
                ->sum('qty');
            $selectedItem = $products->firstWhere('id', $request->item_id);
            $selectedItemName = $selectedItem ? $selectedItem->product_name : null;
        }

        // Fetch real DC totals per SO detail
        $dcTotals = \App\Models\DeliveryChallanDetail::whereIn('sale_order_vno', $orders->pluck('v_no'))
            ->select('sale_order_vno', 'item_id', DB::raw('COALESCE(SUM(load_qty), 0) as total_dc'))
            ->groupBy('sale_order_vno', 'item_id')
            ->get()
            ->groupBy('sale_order_vno');

        // Fetch DC voucher numbers per SO
        $dcRefs = \App\Models\DeliveryChallanDetail::whereIn('sale_order_vno', $orders->pluck('v_no'))
            ->select('sale_order_vno', 'v_no')
            ->distinct()
            ->get()
            ->groupBy('sale_order_vno')
            ->map(function ($items) {
                return $items->pluck('v_no')->unique()->values();
            });

        return \Inertia\Inertia::render('Sales/SaleOrders/Index', [
            'orders' => $orders,
            'totalOrders' => $totalOrders,
            'customers' => $customers,
            'products' => $products,
            'dcTotals' => $dcTotals,
            'dcRefs' => $dcRefs,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalItemQty' => $totalItemQty,
            'selectedItemName' => $selectedItemName,
            'filters' => request()->all()
        ]);
    }

    public function create()
    {
        $user = Auth::user()->cid;
        $module = Module::where('name', 'Sale Order')->first();
        if ($module) {
            $perm = UserPermission::where('module_id', $module->id)->where('cid', $user)->where('userid', Auth::user()->id)->where('write', 1);
            if (!$perm->exists()) {
                return redirect()->back()->with('error', 'You have not Permission. Please Contact with your admin');
            }
        }
        $customers = Customers::where('cid', $user)->get();
        $products = ProductMaster::where('cid', $user)->where('finished', 1)->get();
        $transportCompanies = TransportCompany::where('cid', $user)->get();
        $referenceNames = ReferenceName::where('cid', $user)->orderByDesc('id')->get();

        return \Inertia\Inertia::render('Sales/SaleOrders/CreateEdit', compact('customers', 'products', 'transportCompanies', 'referenceNames'));
    }

    public function store(Request $request)
    {
        $user = Auth::user()->cid;

        $request->validate([
            'customer_id' => 'required',
            'v_date' => 'required|date',
            'items' => 'required|array',
            'items.*.item_id' => 'nullable|exists:product_masters,id',
            'items.*.qty' => 'nullable|numeric|min:0.01',
        ]);

        $customerName = Customers::where('acc_id', $request->customer_id)->value('name');
        $refName = ReferenceName::where('id', $request->ref_name_id)->value('name');

        if ($customerName && $refName && trim(strtolower($customerName)) === trim(strtolower($refName))) {
            return redirect()->back()->withInput()->withErrors(['ref_name_id' => 'Customer name and Reference Party name cannot be the same.']);
        }

        // Block duplicate: same customer + same ref_name_id (including blank/null)
        $existing = SaleOrderMaster::where('cid', $user)
            ->where('customer_id', $request->customer_id);

        if ($request->filled('ref_name_id')) {
            $existing->where('ref_name_id', $request->ref_name_id);
        } else {
            $existing->whereNull('ref_name_id');
        }

        if ($existing->exists()) {
            $refNameStr = $request->filled('ref_name_id') 
                ? ReferenceName::where('id', $request->ref_name_id)->value('name')
                : 'blank reference';
            $msg = "A sale order already exists for this customer with Reference Party \"{$refNameStr}\". Change the reference or delete the old one.";
            return redirect()->back()->withInput()->withErrors(['ref_name_id' => $msg]);
        }



        $items = array_filter($request->items ?? [], function ($item) {
            return !empty($item['item_id']) && !empty($item['qty']);
        });

        $maxNum = SaleOrderMaster::where('cid', $user)
            ->select(DB::raw("MAX(CAST(SUBSTRING(v_no, 5) AS UNSIGNED)) as max_num"))
            ->value('max_num');
        $v_no = 'SOR-' . (($maxNum ?? 0) + 1);

        $master = SaleOrderMaster::create([
            'v_no' => $v_no,
            'v_date' => $request->v_date,
            'prepared_by' => Auth::user()->name,
            'party_ref' => $request->party_ref,
            'ref_name_id' => $request->ref_name_id,
            'transport_company_id' => $request->transport_company_id,
            'bilty_no' => $request->bilty_no,
            'customer_id' => $request->customer_id,
            'cid' => $user,
        ]);

        foreach ($items as $item) {
            SaleOrderDetail::create([
                'v_no' => $v_no,
                'item_id' => $item['item_id'],
                'qty' => $item['qty'],
                'original_qty' => $item['qty'],
                'rate' => 0,
                'cid' => $user,
            ]);
        }

        // Shift remaining qty from old orders for the same customer to this new order
        $oldOrders = SaleOrderMaster::where('cid', $user)
            ->where('customer_id', $request->customer_id)
            ->where('v_no', '!=', $v_no)
            ->whereHas('details', function ($q) {
                $q->where('qty', '>', 0);
            })
            ->with(['details' => function ($q) {
                $q->where('qty', '>', 0);
            }])
            ->get();

        foreach ($oldOrders as $oldOrder) {
            foreach ($oldOrder->details as $oldDetail) {
                $newDetail = SaleOrderDetail::where('v_no', $v_no)
                    ->where('item_id', $oldDetail->item_id)
                    ->first();

                if ($newDetail) {
                    $newDetail->increment('qty', $oldDetail->qty);
                    $oldDetail->update(['qty' => 0]);
                }
            }
        }

        return redirect()->route('sale_order.index')->with('success', "Sale Order #$v_no created successfully.");
    }

    public function edit($id)
    {
        $user = Auth::user()->cid;
        $module = Module::where('name', 'Sale Order')->first();
        if ($module) {
            $perm = UserPermission::where('module_id', $module->id)->where('cid', $user)->where('userid', Auth::user()->id)->where('update', 1);
            if (!$perm->exists()) {
                return redirect()->back()->with('error', 'You have not Permission. Please Contact with your admin');
            }
        }
        $order = SaleOrderMaster::where('cid', $user)->with('details')->findOrFail($id);
        $customers = Customers::where('cid', $user)->get();
        $products = ProductMaster::where('cid', $user)->where('finished', 1)->get();
        $transportCompanies = TransportCompany::where('cid', $user)->get();
        $referenceNames = ReferenceName::where('cid', $user)->orderByDesc('id')->get();

        return \Inertia\Inertia::render('Sales/SaleOrders/CreateEdit', [
            'isEdit' => true,
            'order' => $order,
            'customers' => $customers,
            'products' => $products,
            'transportCompanies' => $transportCompanies,
            'referenceNames' => $referenceNames
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user()->cid;

        $request->validate([
            'customer_id' => 'required',
            'v_date' => 'required|date',
            'items' => 'required|array',
            'items.*.item_id' => 'nullable|exists:product_masters,id',
            'items.*.qty' => 'nullable|numeric|min:0.01',
        ]);

        $customerName = Customers::where('acc_id', $request->customer_id)->value('name');
        $refName = ReferenceName::where('id', $request->ref_name_id)->value('name');

        if ($customerName && $refName && trim(strtolower($customerName)) === trim(strtolower($refName))) {
            return redirect()->back()->withInput()->withErrors(['ref_name_id' => 'Customer name and Reference Party name cannot be the same.']);
        }

        // Block duplicate: same customer + same ref_name_id (excluding current order)
        $existing = SaleOrderMaster::where('cid', $user)
            ->where('customer_id', $request->customer_id)
            ->where('id', '!=', $id);

        if ($request->filled('ref_name_id')) {
            $existing->where('ref_name_id', $request->ref_name_id);
        } else {
            $existing->whereNull('ref_name_id');
        }

        if ($existing->exists()) {
            $refNameStr = $request->filled('ref_name_id') 
                ? ReferenceName::where('id', $request->ref_name_id)->value('name')
                : 'blank reference';
            $msg = "A sale order already exists for this customer with Reference Party \"{$refNameStr}\". Change the reference or delete the old one.";
            return redirect()->back()->withInput()->withErrors(['ref_name_id' => $msg]);
        }

        $items = array_filter($request->items ?? [], function ($item) {
            return !empty($item['item_id']) && !empty($item['qty']);
        });

        $master = SaleOrderMaster::where('cid', $user)->findOrFail($id);
        $master->update([
            'v_date' => now()->format('Y-m-d'),
            'customer_id' => $request->customer_id,
            'party_ref' => $request->party_ref,
            'ref_name_id' => $request->ref_name_id,
            'transport_company_id' => $request->transport_company_id,
            'bilty_no' => $request->bilty_no,
        ]);

        $existingDetails = SaleOrderDetail::where('v_no', $master->v_no)->get()->keyBy('item_id');

        foreach ($items as $item) {
            if ($existingDetails->has($item['item_id'])) {
                $existingDetails->get($item['item_id'])->update([
                    'qty' => $item['qty'],
                ]);
            } else {
                SaleOrderDetail::create([
                    'v_no' => $master->v_no,
                    'item_id' => $item['item_id'],
                    'qty' => $item['qty'],
                    'original_qty' => $item['qty'],
                    'rate' => 0,
                    'cid' => $user,
                ]);
            }
        }

        // Remove details not in the updated items list
        foreach ($existingDetails as $itemId => $detail) {
            $stillPresent = collect($items)->contains('item_id', $itemId);
            if (!$stillPresent) {
                $detail->delete();
            }
        }

        return redirect()->route('sale_order.index')->with('success', "Sale Order #$master->v_no updated successfully.");
    }

    public function destroy($id)
    {
        $user = Auth::user()->cid;
        $module = Module::where('name', 'Sale Order')->first();
        if ($module) {
            $perm = UserPermission::where('module_id', $module->id)->where('cid', $user)->where('userid', Auth::user()->id)->where('delete', 1);
            if (!$perm->exists()) {
                return redirect()->back()->with('error', 'You have not Permission. Please Contact with your admin');
            }
        }
        $master = SaleOrderMaster::where('cid', $user)->findOrFail($id);

        SaleOrderDetail::where('v_no', $master->v_no)->delete();
        $master->delete();
        return redirect()->route('sale_order.index')->with('success', 'Sale Order deleted successfully.');
    }

    public function print($id)
    {
        $user = Auth::user()->cid;
        $order = SaleOrderMaster::where('cid', $user)->with(['details.item', 'customer', 'transportCompany', 'referenceName'])->findOrFail($id);
        return view('sale_order.print', compact('order'));
    }

    public function getCustomerOrders($customerId)
    {
        $user = Auth::user()->cid;
        $orders = SaleOrderMaster::where('cid', $user)
            ->where('customer_id', $customerId)
            ->with(['details.item', 'referenceName'])
            ->orderByDesc('id')
            ->get()
            ->map(function ($order) {
                $itemsStr = $order->details->take(5)->map(function ($d) {
                    return ($d->item->product_name ?? 'Unknown') . ' (' . $d->qty . ')';
                })->implode(', ');
                if ($order->details->count() > 5) {
                    $itemsStr .= '...';
                }
                $partyRefStr = '-';
                if ($order->referenceName) {
                    $partyRefStr = $order->referenceName->name . ($order->referenceName->name_urdu ? ' - ' . $order->referenceName->name_urdu : '');
                } elseif ($order->party_ref) {
                    $partyRefStr = $order->party_ref;
                }
                return [
                    'id' => $order->id,
                    'v_no' => $order->v_no,
                    'v_date' => $order->v_date,
                    'party_ref' => $partyRefStr,
                    'items' => $itemsStr,
                    'item_count' => $order->details->count(),
                ];
            });

        return response()->json($orders);
    }
}
