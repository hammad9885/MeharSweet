<?php

namespace App\Http\Controllers;

use App\Models\ItemMaster;
use App\Models\ItemType;
use App\Models\SaleInvoices;
use App\Models\SaleReturnInvoice;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ItemWiseController extends Controller
{
    public function show(Request $request)
    {

        $summarize = $request->boolean('summarize'); // TRUE if checkbox checked
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->format('Y-m-d') : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->format('Y-m-d') : null;
        $itemId = $request->input('item_id');

        // Base item query
        $items = ItemMaster::where('cid', auth()->user()->cid)->get();
        //  dd($items);
        // ------------------------
        // 📊 IF SUMMARY REPORT
        // ------------------------
        if ($summarize) {
            $report = $items->map(function ($item) use ($startDate, $endDate) {
                // SALE
                $saleQuery = $item->sales()->where('cid', auth()->user()->cid)
                    ->whereHas('master', function ($q) use ($startDate, $endDate) {
                        $q->where('cid', auth()->user()->cid);
                        if ($startDate && $endDate)
                            $q->whereBetween('v_date', [$startDate, $endDate]);
                        elseif ($startDate)
                            $q->whereDate('v_date', '>=', $startDate);
                        elseif ($endDate)
                            $q->whereDate('v_date', '<=', $endDate);
                    });

                $saleqty = $saleQuery->sum('qty');
                $saleAmount = $saleQuery->get()->sum(function ($s) {
                    $subtotal = $s->rate * $s->qty;        // total price before tax
                    $taxAmount = $subtotal * ($s->tax / 100); // tax applied first
                    $totalWithTax = $subtotal + $taxAmount;   // total after tax
                    $totalAfterDiscount = $totalWithTax - $s->discount; // discount after tax
                    return $totalAfterDiscount;
                });

                // RETURN
                $returnQuery = $item->salereturnreturn()->where('cid', auth()->user()->cid)
                    ->whereHas('master', function ($q) use ($startDate, $endDate) {
                        $q->where('cid', auth()->user()->cid);
                        if ($startDate && $endDate)
                            $q->whereBetween('v_date', [$startDate, $endDate]);
                        elseif ($startDate)
                            $q->whereDate('v_date', '>=', $startDate);
                        elseif ($endDate)
                            $q->whereDate('v_date', '<=', $endDate);
                    });

                $returnqty = $returnQuery->sum('qty');
                $returnAmount = $returnQuery->get()->sum(fn($r) => ($r->rate - $r->discount) * $r->qty);

                return [
                    'item' => $item->item_code,
                    'sale_qty' => $saleqty,
                    'return_qty' => $returnqty,
                    'net_qty' => $saleqty - $returnqty,
                    'sale_amount' => $saleAmount,
                    'return_amount' => $returnAmount,
                    'net_amount' => $saleAmount - $returnAmount,
                ];
            });

            return view('ItemWiseSaleReport.SaleWise', compact('report', 'summarize', 'items'));
        }

        // ------------------------
        // 🧾 IF DETAILED REPORT
        // ------------------------
        $saleQuery = SaleInvoices::where('cid', auth()->user()->cid)->with([
            'item'
            => function ($q) {
                $q->where('cid', auth()->user()->cid);
            },
            'customer'
            => function ($q) {
                $q->where('cid', auth()->user()->cid);
            },
            'master'
            => function ($q) {
                $q->where('cid', auth()->user()->cid);
            }
        ]);
        $returnQuery = SaleReturnInvoice::where('cid', auth()->user()->cid)->with(['item'
        => function ($q) {
            $q->where('cid', auth()->user()->cid);
        }, 'customer'
        => function ($q) {
            $q->where('cid', auth()->user()->cid);
        }, 'master'
        => function ($q) {
            $q->where('cid', auth()->user()->cid);
        }]);

        if ($itemId) {
            $saleQuery->where('item_id', $itemId);
            $returnQuery->where('item_id', $itemId);
        }

        if ($startDate && $endDate) {
            $saleQuery->whereHas('master', function ($q) use ($startDate, $endDate) {
                $q->where('cid', auth()->user()->cid)->whereBetween('v_date', [$startDate, $endDate]);
            });
            $returnQuery->whereHas('master', function ($q) use ($startDate, $endDate) {
                $q->where('cid', auth()->user()->cid)->whereBetween('v_date', [$startDate, $endDate]);
            });
        } elseif ($startDate) {
            $saleQuery->whereHas('master', function ($q) use ($startDate) {
                $q->where('cid', auth()->user()->cid)->whereDate('v_date', '>=', $startDate);
            });
            $returnQuery->whereHas('master', function ($q) use ($startDate) {
                $q->where('cid', auth()->user()->cid)->whereDate('v_date', '>=', $startDate);
            });
        } elseif ($endDate) {
            $saleQuery->whereHas('master', function ($q) use ($endDate) {
                $q->where('cid', auth()->user()->cid)->whereDate('v_date', '<=', $endDate);
            });
            $returnQuery->whereHas('master', function ($q) use ($endDate) {
                $q->where('cid', auth()->user()->cid)->whereDate('v_date', '<=', $endDate);
            });
        }

        $data = $saleQuery->orderBy('v_no', 'DESC')->get();
        $return = $returnQuery->orderBy('v_no', 'DESC')->get();

        if (request()->wantsJson() || request()->header('X-Inertia')) {
            return \Inertia\Inertia::render('Sales/Reports/ItemWiseSale', [
                'items' => $items,
                'data' => $data,
                'return' => $return,
                'summarize' => $summarize,
                'filters' => request()->all()
            ]);
        }

        return view('ItemWiseSaleReport.SaleWise', compact('items', 'data', 'return', 'summarize'));
    }
}
