<?php

namespace App\Http\Controllers;

use App\Models\AccountMaster;
use App\Models\ConsumptionMaster;
use App\Models\Contractor;
use App\Models\Level2;
use App\Models\ProductMaster;
use App\Models\StockInDetail;
use App\Models\StockInMaster;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LotInReportController extends Controller
{
    public function index(Request $request)
    {
        $cid = Auth::user()->cid;

        $products = ProductMaster::where('cid', $cid)->orderBy('product_name')->get();

        $contractorLevel = Level2::where('cid', $cid)->where('title', 'Contractor')->value('id');
        $contractorsQuery = AccountMaster::where('cid', $cid);
        if ($contractorLevel) {
            $contractorsQuery->where('level2_id', $contractorLevel);
        }
        $contractors = $contractorsQuery->orderBy('title')->get();

        $reportData = $this->getReportData($request, $cid);

        return view('reports.lot_in_report', compact('products', 'contractors', 'reportData'));
    }

    public function print(Request $request)
    {
        $cid = Auth::user()->cid;

        $reportData = $this->getReportData($request, $cid);

        $startDateRaw = $request->filled('start_date') ? $request->start_date : '2026-01-01';
        $startDate = Carbon::parse($startDateRaw)->format('d/m/Y');

        $endDateRaw = $request->filled('end_date') ? $request->end_date : Carbon::now()->format('Y-m-d');
        $endDate = Carbon::parse($endDateRaw)->format('d/m/Y');

        return view('reports.lot_in_report_print', compact('reportData', 'startDate', 'endDate'));
    }

    private function getReportData(Request $request, $cid)
    {
        $query = StockInDetail::with(['master', 'product', 'contractor'])
            ->where('cid', $cid);

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('lot_no')) {
            $query->where('lot_no', 'like', '%' . $request->lot_no . '%');
        }

        if ($request->filled('contractor_id')) {
            $query->where('contractor_id', $request->contractor_id);
        }

        $startDate = $request->filled('start_date') ? $request->start_date : '2026-01-01';
        if ($startDate) {
            $query->whereHas('master', function ($q) use ($startDate) {
                $q->where('date', '>=', Carbon::parse($startDate)->format('Y-m-d'));
            });
        }

        if ($request->filled('end_date')) {
            $query->whereHas('master', function ($q) use ($request) {
                $q->where('date', '<=', Carbon::parse($request->end_date)->format('Y-m-d'));
            });
        }

        $details = $query->get()->sortByDesc(function ($detail) {
            return $detail->master ? $detail->master->date . '_' . sprintf('%08d', $detail->inv_no) : '0';
        });

        // Cache consumption lookup per product & lot to optimize query loops
        $consumptions = ConsumptionMaster::where('cid', $cid)
            ->whereNotNull('item_id')
            ->with('contractor')
            ->orderByDesc('id')
            ->get()
            ->groupBy('item_id');

        $rows = [];

        foreach ($details as $detail) {
            $productId = $detail->product_id;
            $lotNo = trim((string) $detail->lot_no);

            $productConsumptions = $consumptions->get($productId, collect());

            $matchedConsumption = null;
            if ($lotNo !== '') {
                $matchedConsumption = $productConsumptions->first(function ($c) use ($lotNo) {
                    return trim((string) $c->lot_no) === $lotNo;
                });
            }

            if (!$matchedConsumption) {
                $matchedConsumption = $productConsumptions->first();
            }

            $cartonsInLot = $matchedConsumption ? (float) ($matchedConsumption->cartons_in_lot ?? 0) : 0;
            $qtyPerCarton = $matchedConsumption ? (float) ($matchedConsumption->qty_per_carton ?? 0) : 0;

            $stockQty = (float) $detail->qty;

            // Calculate Pieces = Qty per Ctn * Qty (from Stock In)
            $pieces = $qtyPerCarton * $stockQty;

            // Determine Contractor
            $contractorName = '-';
            if ($detail->contractor) {
                $contractorName = $detail->contractor->title ?? $detail->contractor->name ?? '-';
            } elseif ($matchedConsumption && $matchedConsumption->contractor) {
                $contractorName = $matchedConsumption->contractor->title ?? $matchedConsumption->contractor->name ?? '-';
            }

            $vDate = $detail->master && $detail->master->date 
                ? Carbon::parse($detail->master->date)->format('d-m-y') 
                : '-';

            $rows[] = [
                'v_no'           => 'STI-' . $detail->inv_no,
                'inv_no'         => $detail->inv_no,
                'date'           => $vDate,
                'contractor'     => $contractorName,
                'item_title'     => $detail->product ? $detail->product->product_name : '-',
                'lot_no'         => $lotNo ?: '-',
                'stock_qty'      => (float) $detail->qty,
                'cartons_in_lot' => $cartonsInLot,
                'qty_per_carton' => $qtyPerCarton,
                'pieces'         => $pieces,
            ];
        }

        return $rows;
    }
}
