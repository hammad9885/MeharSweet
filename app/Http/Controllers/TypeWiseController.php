<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ItemMaster;
use App\Models\ItemType;
use App\Models\SaleInvoices;
use App\Models\SaleReturnInvoice;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TypeWiseController extends Controller
{
    public function type(Request $request)
    {
        $summarize = $request->boolean('summarize'); // TRUE if summary checkbox clicked
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->format('Y-m-d') : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->format('Y-m-d') : null;
        $typeId = $request->input('type_id'); // Changed to type_id

        // Get all Item Brands for filter dropdown
        $itemsTypes = ItemType::where('cid', auth()->user()->cid)->get();

        // Base items query
        $itemsQuery = ItemMaster::where('cid', auth()->user()->cid);
        if ($typeId) {
            $itemsQuery->where('type_id', $typeId); // Filter by type
        }
        $items = $itemsQuery->get();

        // ------------------------
        // SUMMARY REPORT
        // ------------------------
       if ($summarize) {
    $report = $itemsTypes->map(function ($type) use ($startDate, $endDate) {
        // Get all items under this type
        $itemIds = ItemMaster::where('type_id', $type->id)
            ->where('cid', auth()->user()->cid)
            ->pluck('id');

        if ($itemIds->isEmpty()) {
            return [
                'type' => $type->type_title,
                'sale_qty' => 0,
                'return_qty' => 0,
                'net_qty' => 0,
                'sale_amount' => 0,
                'return_amount' => 0,
                'net_amount' => 0,
            ];
        }

        // --- SALE ---
        $saleQuery = SaleInvoices::where('cid', auth()->user()->cid)
            ->whereIn('item_id', $itemIds);

        if ($startDate && $endDate)
            $saleQuery->whereHas('master', function($q) use ($startDate, $endDate) {
                $q->where('cid', auth()->user()->cid)->whereBetween('v_date', [$startDate, $endDate]);
            });
        elseif ($startDate)
            $saleQuery->whereHas('master', function($q) use ($startDate) {
                $q->where('cid', auth()->user()->cid)->whereDate('v_date', '>=', $startDate);
            });
        elseif ($endDate)
            $saleQuery->whereHas('master', function($q) use ($endDate) {
                $q->where('cid', auth()->user()->cid)->whereDate('v_date', '<=', $endDate);
            });

        $saleqty = $saleQuery->sum('qty');
        $saleAmount = $saleQuery->get()->sum(fn($s) => ($s->rate - $s->discount) * $s->qty);

        // --- RETURN ---
        $returnQuery = SaleReturnInvoice::where('cid', auth()->user()->cid)
            ->whereIn('item_id', $itemIds);

        if ($startDate && $endDate)
            $returnQuery->whereHas('master', function($q) use ($startDate, $endDate) {
                $q->where('cid', auth()->user()->cid)->whereBetween('v_date', [$startDate, $endDate]);
            });
        elseif ($startDate)
            $returnQuery->whereHas('master', function($q) use ($startDate) {
                $q->where('cid', auth()->user()->cid)->whereDate('v_date', '>=', $startDate);
            });
        elseif ($endDate)
            $returnQuery->whereHas('master', function($q) use ($endDate) {
                $q->where('cid', auth()->user()->cid)->whereDate('v_date', '<=', $endDate);
            });

        $returnqty = $returnQuery->sum('qty');
        $returnAmount = $returnQuery->get()->sum(fn($r) => ($r->rate - $r->discount) * $r->qty);

        return [
            'type' => $type->type_title,
            'sale_qty' => $saleqty,
            'return_qty' => $returnqty,
            'net_qty' => $saleqty - $returnqty,
            'sale_amount' => $saleAmount,
            'return_amount' => $returnAmount,
            'net_amount' => $saleAmount - $returnAmount,
        ];
    });
     
    return \Inertia\Inertia::render('Sales/Reports/CategoryWiseSale', [
        'report' => $report,
        'summarize' => 1,
        'itemsTypes' => $itemsTypes,
        'typeId' => $typeId,
        'filters' => request()->all()
    ]);
}


        // ------------------------
        // DETAILED REPORT
        // ------------------------
        $result = $this->getItemDataByType($typeId, $startDate, $endDate);
        $data = $result['data'];
        $return = $result['return'];

        return \Inertia\Inertia::render('Sales/Reports/CategoryWiseSale', [
            'data' => $data,
            'return' => $return,
            'summarize' => $summarize,
            'itemsTypes' => $itemsTypes,
            'typeId' => $typeId,
            'filters' => request()->all()
        ]);
    }

    /**
     * Fetch detailed sales and returns for selected type_id and date range
     */
    private function getItemDataByType($typeId, $startDate, $endDate)
    {
        // Get all item IDs for the selected type
        $itemIds = ItemMaster::where('cid', auth()->user()->cid)
            ->when($typeId, fn($q) => $q->where('type_id', $typeId))
            ->pluck('id');

        $saleQuery = SaleInvoices::where('cid', auth()->user()->cid)
            ->whereIn('item_id', $itemIds)
            ->with(['item'=>function($q){$q->where('cid',auth()->user()->cid);}, 'customer'=>function($q){$q->where('cid',auth()->user()->cid);}, 'master'=>function($q){$q->where('cid',auth()->user()->cid);}]);

        $returnQuery = SaleReturnInvoice::where('cid', auth()->user()->cid)
            ->whereIn('item_id', $itemIds)
            ->with(['item'=>function($q){$q->where('cid',auth()->user()->cid);}, 'customer'=>function($q){$q->where('cid',auth()->user()->cid);}, 'master'=>function($q){$q->where('cid',auth()->user()->cid);}]);

        if ($startDate && $endDate) {
            $saleQuery->whereHas('master', function($q) use ($startDate, $endDate) {
                $q->where('cid', auth()->user()->cid)->whereBetween('v_date', [$startDate, $endDate]);
            });
            $returnQuery->whereHas('master', function($q) use ($startDate, $endDate) {
                $q->where('cid', auth()->user()->cid)->whereBetween('v_date', [$startDate, $endDate]);
            });
        } elseif ($startDate) {
            $saleQuery->whereHas('master', function($q) use ($startDate) {
                $q->where('cid', auth()->user()->cid)->whereDate('v_date', '>=', $startDate);
            });
            $returnQuery->whereHas('master', function($q) use ($startDate) {
                $q->where('cid', auth()->user()->cid)->whereDate('v_date', '>=', $startDate);
            });
        } elseif ($endDate) {
            $saleQuery->whereHas('master', function($q) use ($endDate) {
                $q->where('cid', auth()->user()->cid)->whereDate('v_date', '<=', $endDate);
            });
            $returnQuery->whereHas('master', function($q) use ($endDate) {
                $q->where('cid', auth()->user()->cid)->whereDate('v_date', '<=', $endDate);
            });
        }

              $data = $saleQuery->orderBy('v_no', 'DESC')->get();
        $return = $returnQuery->orderBy('v_no', 'DESC')->get();

        return compact('data', 'return');
    }
}
