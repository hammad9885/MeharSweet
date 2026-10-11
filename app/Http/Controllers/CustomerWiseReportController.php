<?php

namespace App\Http\Controllers;

use App\Models\SaleMasters;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\SaleInvoices;
use App\Models\SaleReturnInvoice;
use App\Models\SaleReturnMaster;
use Carbon\Carbon;

class CustomerWiseReportController extends Controller
{
    public function index(Request $request)
    {
        // ✅ Validate input
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'customer_id'=> 'nullable|integer|exists:customers,acc_id',
        ]);

        $cid = auth()->user()->cid;
  $data=collect();
        $return=collect();
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->format('Y-m-d') : null;
        $endDate   = $request->end_date ? Carbon::parse($request->end_date)->format('Y-m-d') : null;
        $customerId = $request->customer_id;
        $sales=null;
        $returns=null;
         if ($request->customer_id) {
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

            if ($customerId) {
                $saleQuery->where('customer_id', $customerId);
                $returnQuery->where('customer_id', $customerId);
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
            $customers = \App\Models\Customers::with('accountMaster')->where('cid', auth()->user()->cid)->get();
            return \Inertia\Inertia::render('Sales/Reports/CustomerWiseSale', [
                'data' => $data,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'return' => $return,
                'customers' => $customers,
                'filters' => request()->all()
            ]);
        }
        
        return view('CustomerWiseReport.index', compact('data', 'startDate', 'endDate','return'));
    }
    public function saleWise(Request $request)
    {
        // ✅ Validate input
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
        ]);

        $cid = auth()->user()->cid;
        $data=collect();
        $return=collect();
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->format('Y-m-d') : null;
        $endDate   = $request->end_date ? Carbon::parse($request->end_date)->format('Y-m-d') : null;

        $supplierId = $request->customer_id;
        $sales = null;
        $returns = null;
        // dd($cid);
        if ($startDate||$endDate) {
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

            if ($supplierId) {
                $saleQuery->where('customer_id', $supplierId);
                $returnQuery->where('customer_id', $supplierId);
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
            return \Inertia\Inertia::render('Sales/Reports/SaleReport', [
                'data' => $data,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'return' => $return,
                'filters' => request()->all()
            ]);
        }
        
        return view('SaleReport.salewiseReport', compact('data', 'startDate', 'endDate','return'));
    }
}
