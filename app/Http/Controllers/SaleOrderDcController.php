<?php

namespace App\Http\Controllers;

use App\Models\ConsumptionMaster;
use App\Models\Customers;
use App\Models\DeliveryChallanDetail;
use App\Models\DeliveryChallanMaster;
use App\Models\Module;
use App\Models\ProductMaster;
use App\Models\ReferenceName;
use App\Models\SaleMasters;
use App\Models\SaleOrderDetail;
use App\Models\SaleOrderMaster;
use App\Models\TransportCompany;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleOrderDcController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user()->cid;
        $module = Module::where('name', 'Delivery Challan (SOR)')->first();
        if ($module) {
            $perm = UserPermission::where('module_id', $module->id)->where('cid', $user)->where('userid', Auth::user()->id)->where('read', 1);
            if (!$perm->exists()) {
                return redirect()->back()->with('error', 'You have not Permission. Please Contact with your admin');
            }
        }
        $query = DeliveryChallanMaster::where('cid', $user)->with(['details.item', 'customer', 'referenceName', 'transportCompany']);

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

        $dcs = $query->orderByDesc('updated_at')->get();

        // Collect all SO v_no referenced by these DCs
        $sorVnos = $dcs->pluck('details')->flatten()->pluck('sale_order_vno')->unique()->filter();
        $sorExists = \App\Models\SaleOrderMaster::whereIn('v_no', $sorVnos)->pluck('v_no')->toArray();

        // Check paid status: which DCs have a sale invoice
        $dcVnos = $dcs->pluck('v_no');
        $paidInvoices = SaleMasters::whereIn('dc_vno', $dcVnos)->where('cid', $user)->pluck('v_no', 'dc_vno')->toArray();
        $paidDcs = array_keys($paidInvoices);

        // Paid/Unpaid filter
        if ($request->paid_status === 'paid') {
            $dcs = $dcs->filter(function ($dc) use ($paidDcs) {
                return in_array($dc->v_no, $paidDcs);
            });
        } elseif ($request->paid_status === 'unpaid') {
            $dcs = $dcs->filter(function ($dc) use ($paidDcs) {
                return !in_array($dc->v_no, $paidDcs);
            });
        }

        $customers = Customers::where('cid', $user)->get();
        return view('sale_order_dc.index', compact('dcs', 'customers', 'sorExists', 'paidDcs', 'paidInvoices', 'startDate', 'endDate'));
    }

    public function create(Request $request)
    {
        $user = Auth::user()->cid;
        $module = Module::where('name', 'Delivery Challan (SOR)')->first();
        if ($module) {
            $perm = UserPermission::where('module_id', $module->id)->where('cid', $user)->where('userid', Auth::user()->id)->where('write', 1);
            if (!$perm->exists()) {
                return redirect()->back()->with('error', 'You have not Permission. Please Contact with your admin');
            }
        }
        $transportCompanies = TransportCompany::where('cid', $user)->get();
        $customers = Customers::where('cid', $user)->get();
        $referenceNames = ReferenceName::where('cid', $user)->orderByDesc('id')->get();
        $preselectCustomerId = $request->query('customer_id');
        $preselectSorVno = $request->query('sor_vno');
        $preselectedRefNameId = null;
        $preselectedPartyRef = null;
        $preselectedRefNumber = null;
        if ($preselectCustomerId) {
            $cust = Customers::where('acc_id', $preselectCustomerId)->where('cid', $user)->first();
            $preselectedRefNumber = $cust?->ntn;
        }
        if ($preselectSorVno) {
            $sor = SaleOrderMaster::where('v_no', $preselectSorVno)
                ->where('cid', $user)
                ->first();
            $preselectedRefNameId = $sor?->ref_name_id;
            $preselectedPartyRef = $sor?->party_ref;
            if (!$preselectedRefNumber && $sor?->customer_id) {
                $cust = Customers::where('acc_id', $sor->customer_id)->where('cid', $user)->first();
                $preselectedRefNumber = $cust?->ntn;
            }
        }
        return view('sale_order_dc.create', compact('transportCompanies', 'customers', 'referenceNames', 'preselectCustomerId', 'preselectSorVno', 'preselectedRefNameId', 'preselectedPartyRef', 'preselectedRefNumber'));
    }

    public function store(Request $request)
    {
        $user = Auth::user()->cid;

        $request->validate([
            'customer_id' => 'required',
            'v_date' => 'required|date',
            'sor_vno' => 'required',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:product_masters,id',
            'items.*.available_qty' => 'required|numeric',
            'items.*.load_qty' => 'required|numeric|min:0',
        ]);

        $qtyPerCarton = $request->qty_per_carton;
        $maxNum = DeliveryChallanMaster::where('cid', $user)
            ->select(DB::raw("MAX(CAST(SUBSTRING(v_no, 5) AS UNSIGNED)) as max_num"))
            ->value('max_num');
        $v_no = 'DCN-' . (($maxNum ?? 0) + 1);

        $master = DeliveryChallanMaster::create([
            'v_no' => $v_no,
            'v_date' => $request->v_date,
            'customer_id' => $request->customer_id,
            'prepared_by' => Auth::user()->name,
            'ref_no' => $request->ref_no,
            'ref_name_id' => $request->ref_name_id,
            'bilty_no' => $request->bilty_no,
            'transport_company_id' => $request->transport_company_id,
            'qty_per_carton' => $qtyPerCarton,
            'freight' => $request->freight ?? 0,
            'cid' => $user,
        ]);

        foreach ($request->items as $sortOrder => $item) {
            DeliveryChallanDetail::create([
                'v_no' => $v_no,
                'sale_order_vno' => $request->sor_vno,
                'item_id' => $item['item_id'],
                'available_qty' => $item['available_qty'],
                'load_qty' => $item['load_qty'],
                'lot_no' => $item['lot_no'] ?? null,
                'sort_order' => $sortOrder,
                'cid' => $user,
            ]);

            $sod = SaleOrderDetail::where('v_no', $request->sor_vno)
                ->where('item_id', $item['item_id'])
                ->where('cid', $user)
                ->first();

            if ($sod) {
                $newQty = $sod->qty - $item['load_qty'];
                $sod->update(['qty' => max(0, $newQty)]);
            }
        }

        $this->autoDeleteCompletedSo($request->sor_vno, $user);

        return redirect()->route('sale_order_dc.index')->with('success', "Delivery Challan #$v_no created successfully.");
    }

    public function edit($id)
    {
        $user = Auth::user()->cid;
        $dc = DeliveryChallanMaster::where('cid', $user)->findOrFail($id);

        // Check if this DC is linked to a paid Sale Invoice
        $hasInvoice = \App\Models\SaleMasters::where('dc_vno', $dc->v_no)->where('cid', $user)->exists();
        if ($hasInvoice) {
            return redirect()->route('sale_order_dc.index')
                ->withErrors(['error' => 'You must delete the Sale Invoice first before editing this DC.']);
        }

        $module = Module::where('name', 'Delivery Challan (SOR)')->first();
        if ($module) {
            $perm = UserPermission::where('module_id', $module->id)->where('cid', $user)->where('userid', Auth::user()->id)->where('update', 1);
            if (!$perm->exists()) {
                return redirect()->back()->with('error', 'You have not Permission. Please Contact with your admin');
            }
        }

        // Load details explicitly (ensures they're always available)
        $dc->load('details');
        $transportCompanies = TransportCompany::where('cid', $user)->get();
        $customers = Customers::where('cid', $user)->get();
        $referenceNames = ReferenceName::where('cid', $user)->orderByDesc('id')->get();
        $sorVno = $dc->details->first() ? $dc->details->first()->sale_order_vno : null;

        $sorDetails = collect();
        $sorId = null;
        if ($sorVno) {
            $sorDetails = SaleOrderDetail::with('item')
                ->where('v_no', $sorVno)
                ->where('cid', $user)
                ->get()
                ->keyBy('item_id');
            $sorOrder = SaleOrderMaster::where('v_no', $sorVno)->where('cid', $user)->first();
            $sorId = $sorOrder ? $sorOrder->id : null;
        }

        return view('sale_order_dc.edit', compact('transportCompanies', 'dc', 'customers', 'referenceNames', 'sorVno', 'sorDetails', 'sorId'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user()->cid;

        $request->validate([
            'customer_id' => 'required',
            'v_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:product_masters,id',
            'items.*.load_qty' => 'required|numeric|min:0',
        ]);

        $qtyPerCarton = $request->qty_per_carton;
        $master = DeliveryChallanMaster::where('cid', $user)->findOrFail($id);
        $oldDetails = DeliveryChallanDetail::where('v_no', $master->v_no)->get();
        $sorVno = $request->sor_vno ?? $oldDetails->first()->sale_order_vno;

        foreach ($oldDetails as $old) {
            $sod = SaleOrderDetail::where('v_no', $old->sale_order_vno)
                ->where('item_id', $old->item_id)
                ->where('cid', $user)
                ->first();

            if ($sod) {
                $sod->update(['qty' => $sod->qty + $old->load_qty]);
            }
        }

        DeliveryChallanDetail::where('v_no', $master->v_no)->delete();

        $master->update([
            'v_date' => $request->v_date,
            'customer_id' => $request->customer_id,
            'ref_no' => $request->ref_no,
            'ref_name_id' => $request->ref_name_id,
            'bilty_no' => $request->bilty_no,
            'transport_company_id' => $request->transport_company_id,
            'qty_per_carton' => $qtyPerCarton,
            'freight' => $request->has('freight') ? (float)($request->freight ?? 0) : (float)($master->freight ?? 0),
        ]);

        foreach ($request->items as $sortOrder => $item) {
            DeliveryChallanDetail::create([
                'v_no' => $master->v_no,
                'sale_order_vno' => $sorVno,
                'item_id' => $item['item_id'],
                'available_qty' => $item['available_qty'] ?? 0,
                'load_qty' => $item['load_qty'],
                'lot_no' => $item['lot_no'] ?? null,
                'sort_order' => $sortOrder,
                'cid' => $user,
            ]);

            $sod = SaleOrderDetail::where('v_no', $sorVno)
                ->where('item_id', $item['item_id'])
                ->where('cid', $user)
                ->first();

            if ($sod) {
                $newQty = $sod->qty - $item['load_qty'];
                $sod->update(['qty' => max(0, $newQty)]);
            }
        }

        $this->autoDeleteCompletedSo($sorVno, $user);

        return redirect()->route('sale_order_dc.index')->with('success', "Delivery Challan #$master->v_no updated successfully.");
    }

    public function print($id)
    {
        $user = Auth::user()->cid;
        $dc = DeliveryChallanMaster::where('cid', $user)
            ->with(['details.item', 'customer', 'transportCompany', 'referenceName'])
            ->findOrFail($id);

        $sorVno = $dc->details->first() ? $dc->details->first()->sale_order_vno : null;
        $sorExists = $sorVno ? SaleOrderMaster::where('cid', $user)->where('v_no', $sorVno)->pluck('v_no')->toArray() : [];

        return view('sale_order_dc.print', compact('dc', 'sorVno', 'sorExists'));
    }

    public function destroy($id)
    {
        $user = Auth::user()->cid;
        $module = Module::where('name', 'Delivery Challan (SOR)')->first();
        if ($module) {
            $perm = UserPermission::where('module_id', $module->id)->where('cid', $user)->where('userid', Auth::user()->id)->where('delete', 1);
            if (!$perm->exists()) {
                return redirect()->back()->with('error', 'You have not Permission. Please Contact with your admin');
            }
        }
        $master = DeliveryChallanMaster::where('cid', $user)->findOrFail($id);

        foreach (DeliveryChallanDetail::where('v_no', $master->v_no)->get() as $d) {
            $sod = SaleOrderDetail::where('v_no', $d->sale_order_vno)
                ->where('item_id', $d->item_id)
                ->where('cid', $user)
                ->first();

            if ($sod) {
                $sod->update(['qty' => $sod->qty + $d->load_qty]);
            }
        }

        DeliveryChallanDetail::where('v_no', $master->v_no)->delete();
        $master->delete();
        return redirect()->route('sale_order_dc.index')->with('success', 'Delivery Challan deleted successfully.');
    }

    public function getSorsByCustomer($customerId)
    {
        $user = Auth::user()->cid;
        $sors = SaleOrderMaster::where('cid', $user)
            ->where('customer_id', $customerId)
            ->whereHas('details', function ($q) {
                $q->where('qty', '>', 0);
            })
            ->get(['v_no']);

        return response()->json($sors);
    }

    public function getSorItems($v_no)
    {
        $user = Auth::user()->cid;
        $items = SaleOrderDetail::with('item')
            ->where('v_no', $v_no)
            ->where('cid', $user)
            ->where('qty', '>', 0)
            ->orderBy('id')
            ->get();

        return response()->json($items);
    }

    public function getSorMaster($v_no)
    {
        $user = Auth::user()->cid;
        $sor = SaleOrderMaster::where('v_no', $v_no)->where('cid', $user)->first(['ref_name_id', 'party_ref']);
        return response()->json([
            'ref_name_id' => $sor ? $sor->ref_name_id : null,
            'party_ref' => $sor ? $sor->party_ref : null,
        ]);
    }

    public function getConsumptionQpc($productId)
    {
        $user = Auth::user()->cid;
        $latest = ConsumptionMaster::where('cid', $user)
            ->where('item_id', $productId)
            ->whereNotNull('qty_per_carton')
            ->latest()
            ->first();

        return response()->json([
            'qty_per_carton' => $latest ? (int) $latest->qty_per_carton : null,
            'cartons_in_lot' => $latest ? (int) $latest->cartons_in_lot : null,
        ]);
    }

    public function getNextLotNo($itemId)
    {
        $user = Auth::user()->cid;

        // Get cartons_in_lot and qty_per_carton from consumption_masters
        $consumption = ConsumptionMaster::where('cid', $user)
            ->where('item_id', $itemId)
            ->whereNotNull('cartons_in_lot')
            ->latest()
            ->first();

        $cartonsInLot = $consumption ? (int) $consumption->cartons_in_lot : 50;
        $qpc = $consumption ? (int) $consumption->qty_per_carton : null;
        $hasConsumption = $consumption !== null;

        // Find max lot_no for this item
        $maxLot = DeliveryChallanDetail::where('cid', $user)
            ->where('item_id', $itemId)
            ->whereNotNull('lot_no')
            ->max('lot_no');

        if ($maxLot) {
            // Get all detail rows for this last lot
            $lastLotDetails = DeliveryChallanDetail::where('cid', $user)
                ->where('item_id', $itemId)
                ->where('lot_no', $maxLot)
                ->get();

            $usedCartons = 0;
            foreach ($lastLotDetails as $detail) {
                $usedCartons += $detail->load_qty;
            }

            $remainingCapacity = max(0, $cartonsInLot - $usedCartons);

            return response()->json([
                'last_lot_no'        => (int) $maxLot,
                'used_cartons'       => (int) $usedCartons,
                'remaining_capacity' => (int) $remainingCapacity,
                'cartons_in_lot'     => $cartonsInLot,
                'next_lot_no'        => (int) $maxLot + 1,
                'has_consumption'    => $hasConsumption,
            ]);
        }

        // No existing lots — use product_masters.lot_no_sale as fallback start
        $product = ProductMaster::where('cid', $user)->find($itemId);
        $startLot = ($product && $product->lot_no_sale) ? (int) $product->lot_no_sale : 1;

        return response()->json([
            'last_lot_no'        => null,
            'used_cartons'       => 0,
            'remaining_capacity' => 0,
            'cartons_in_lot'     => $cartonsInLot,
            'next_lot_no'        => $startLot,
            'has_consumption'    => $hasConsumption,
        ]);
    }

    private function autoDeleteCompletedSo($sorVno, $user)
    {
        $so = SaleOrderMaster::where('v_no', $sorVno)->where('cid', $user)->first();
        if (!$so) return;

        $allZero = SaleOrderDetail::where('v_no', $sorVno)->where('cid', $user)
            ->where('qty', '>', 0)
            ->doesntExist();

        if ($allZero) {
            SaleOrderDetail::where('v_no', $sorVno)->where('cid', $user)->delete();
            $so->delete();
        }
    }
}
