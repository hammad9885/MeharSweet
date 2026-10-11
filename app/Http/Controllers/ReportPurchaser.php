<?php

namespace App\Http\Controllers;


use App\Models\ItemMaster;
use App\Models\ItemType;
use App\Models\PurchaseInvoice as SaleInvoices;
use App\Models\PurchaseMaster;
use App\Models\PurchaseReturnInvoice as SaleReturnInvoice;
use App\Models\PurchaseReturnMaster;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportPurchaser extends Controller
{
    public function ItemWise(Request $request)
    {
        // dd($request->item_id);
        $summarize = $request->boolean('summarize'); // TRUE if checkbox checked
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->format('Y-m-d') : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->format('Y-m-d') : null;
        $itemId = $request->input('item_id');
        // dd($startDate);
        // Base item query
        $items = ItemMaster::where('cid', auth()->user()->cid)->get();
        // dd($items);
        // ------------------------
        // 📊 IF SUMMARY REPORT
        // ------------------------
        if ($summarize) {
            $report = $items->map(function ($item) use ($startDate, $endDate) {
                // SALE
                $saleQuery = $item->purchases()->where('cid', auth()->user()->cid)
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
                $returnQuery = $item->purchasereturn()->where('cid', auth()->user()->cid)
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
                    'Purchase_qty' => $saleqty,
                    'return_qty' => $returnqty,
                    'net_qty' => $saleqty - $returnqty,
                    'Purchase_amount' => $saleAmount,
                    'return_amount' => $returnAmount,
                    'net_amount' => $saleAmount - $returnAmount,
                ];
            });

            if (request()->wantsJson() || request()->header('X-Inertia')) {
                return \Inertia\Inertia::render('Purchase/Reports/ItemWisePurchase', [
                    'report' => $report,
                    'summarize' => 1,
                    'items' => $items,
                    'filters' => $request->all(),
                ]);
            }

            return view('PurchaseReports.ItemWise', compact('report', 'summarize', 'items'));
        }

        // ------------------------
        // 🧾 IF DETAILED REPORT
        // ------------------------
        $saleQuery = SaleInvoices::where('cid', auth()->user()->cid)->with([
            'item'
            => function ($q) {
                $q->where('cid', auth()->user()->cid);
            },
            'supplier'
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
        }, 'supplier'
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
            return \Inertia\Inertia::render('Purchase/Reports/ItemWisePurchase', [
                'items' => $items,
                'data' => $data,
                'return' => $return,
                'summarize' => 0,
                'filters' => $request->all(),
            ]);
        }

        return view('PurchaseReports.ItemWise', compact('items', 'data', 'return', 'summarize'));
    }

    public function supplierwise(Request $request)
    {

        // ✅ Validate input
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'supplier_id' => 'nullable|integer',
        ]);

        $cid = auth()->user()->cid;
        $data = collect();
        $return = collect();
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->format('Y-m-d') : null;
        $endDate   = $request->end_date ? Carbon::parse($request->end_date)->format('Y-m-d') : null;

        $supplierId = $request->supplier_id;
        $sales = null;
        $returns = null;
        // dd($cid);
        if ($request->supplier_id) {
            // ------------------------
            // 🧾 IF DETAILED REPORT
            // ------------------------
            $saleQuery = SaleInvoices::where('cid', auth()->user()->cid)->with([
                'item'
                => function ($q) {
                    $q->where('cid', auth()->user()->cid);
                },
                'supplier'
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
            }, 'supplier'
            => function ($q) {
                $q->where('cid', auth()->user()->cid);
            }, 'master'
            => function ($q) {
                $q->where('cid', auth()->user()->cid);
            }]);

            if ($supplierId) {
                $saleQuery->where('supplier_id', $supplierId);
                $returnQuery->where('supplier_id', $supplierId);
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
        }
        // dd($sales);
        // dd($returns);

        if (request()->wantsJson() || request()->header('X-Inertia')) {
            $suppliers = \App\Models\Supplier::where('cid', auth()->user()->cid)->get();
            return \Inertia\Inertia::render('Purchase/Reports/SupplierWisePurchase', [
                'data' => $data,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'return' => $return,
                'suppliers' => $suppliers,
                'filters' => $request->all(),
            ]);
        }

        return view('PurchaseReports.SupplierWise', compact('data', 'startDate', 'endDate', 'return'));
    }
    public function saleWise(Request $request)
    {
        // ✅ Validate input
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
        ]);

        $cid = auth()->user()->cid;

        $startDate = $request->start_date ? Carbon::parse($request->start_date)->format('Y-m-d') : null;
        $endDate   = $request->end_date ? Carbon::parse($request->end_date)->format('Y-m-d') : null;

        $saleQuery = SaleInvoices::where('cid', auth()->user()->cid)->with([
            'item'
            => function ($q) {
                $q->where('cid', auth()->user()->cid);
            },
            'supplier'
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
        }, 'supplier'
        => function ($q) {
            $q->where('cid', auth()->user()->cid);
        }, 'master'
        => function ($q) {
            $q->where('cid', auth()->user()->cid);
        }]);



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
            return \Inertia\Inertia::render('Purchase/Reports/PurchaseReport', [
                'data' => $data,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'return' => $return,
                'filters' => $request->all(),
            ]);
        }

        return view('PurchaseReports.PurchaseWise', compact('data', 'startDate', 'endDate', 'return'));
    }
    public function type(Request $request)
    {
        // dd($request->all());
        $summarize = $request->boolean('summarize'); // TRUE if summary checkbox clicked
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->format('Y-m-d') : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->format('Y-m-d') : null;
        $typeId = $request->input('type_id'); // Changed to brand_id

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
                    $saleQuery->whereHas('master', function ($q) use ($startDate, $endDate) {
                        $q->where('cid', auth()->user()->cid)->whereBetween('v_date', [$startDate, $endDate]);
                    });
                elseif ($startDate)
                    $saleQuery->whereHas('master', function ($q) use ($startDate) {
                        $q->where('cid', auth()->user()->cid)->whereDate('v_date', '>=', $startDate);
                    });
                elseif ($endDate)
                    $saleQuery->whereHas('master', function ($q) use ($endDate) {
                        $q->where('cid', auth()->user()->cid)->whereDate('v_date', '<=', $endDate);
                    });

                $saleqty = $saleQuery->sum('qty');
                $saleAmount = $saleQuery->get()->sum(fn($s) => ($s->rate - $s->discount) * $s->qty);

                // --- RETURN ---
                $returnQuery = SaleReturnInvoice::where('cid', auth()->user()->cid)
                    ->whereIn('item_id', $itemIds);

                if ($startDate && $endDate)
                    $returnQuery->whereHas('master', function ($q) use ($startDate, $endDate) {
                        $q->where('cid', auth()->user()->cid)->whereBetween('v_date', [$startDate, $endDate]);
                    });
                elseif ($startDate)
                    $returnQuery->whereHas('master', function ($q) use ($startDate) {
                        $q->where('cid', auth()->user()->cid)->whereDate('v_date', '>=', $startDate);
                    });
                elseif ($endDate)
                    $returnQuery->whereHas('master', function ($q) use ($endDate) {
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

            if (request()->wantsJson() || request()->header('X-Inertia')) {
                return \Inertia\Inertia::render('Purchase/Reports/CategoryWisePurchase', [
                    'report' => $report,
                    'summarize' => 1,
                    'itemsTypes' => $itemsTypes,
                    'typeId' => $typeId,
                    'filters' => $request->all(),
                ]);
            }

            return view('PurchaseReports.TypeWise', compact('report', 'summarize', 'itemsTypes', 'typeId'));
        }


        // ------------------------
        // DETAILED REPORT
        // ------------------------
        $result = $this->getItemDataByType($typeId, $startDate, $endDate);
        $data = $result['data'];
        $return = $result['return'];

        if (request()->wantsJson() || request()->header('X-Inertia')) {
            return \Inertia\Inertia::render('Purchase/Reports/CategoryWisePurchase', [
                'data' => $data,
                'return' => $return,
                'summarize' => 0,
                'itemsTypes' => $itemsTypes,
                'typeId' => $typeId,
                'filters' => $request->all(),
            ]);
        }

        return view('PurchaseReports.TypeWise', compact('data', 'return', 'summarize', 'itemsTypes', 'typeId'));
    }

    /**
     * Fetch detailed sales and returns for selected brand_id and date range
     */
    private function getItemDataByType($typeId, $startDate, $endDate)
    {
        // Get all item IDs for the selected type
        $itemIds = ItemMaster::where('cid', auth()->user()->cid)
            ->when($typeId, fn($q) => $q->where('type_id', $typeId))
            ->pluck('id');

        $saleQuery = SaleInvoices::where('cid', auth()->user()->cid)
            ->whereIn('item_id', $itemIds)
            ->with(['item' => function ($q) {
                $q->where('cid', auth()->user()->cid);
            }, 'supplier' => function ($q) {
                $q->where('cid', auth()->user()->cid);
            }, 'master' => function ($q) {
                $q->where('cid', auth()->user()->cid);
            }]);

        $returnQuery = SaleReturnInvoice::where('cid', auth()->user()->cid)
            ->whereIn('item_id', $itemIds)
            ->with(['item' => function ($q) {
                $q->where('cid', auth()->user()->cid);
            }, 'supplier' => function ($q) {
                $q->where('cid', auth()->user()->cid);
            }, 'master' => function ($q) {
                $q->where('cid', auth()->user()->cid);
            }]);

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

        return compact('data', 'return');
    }
}
