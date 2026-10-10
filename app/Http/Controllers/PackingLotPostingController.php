<?php

namespace App\Http\Controllers;

use App\Models\AccountMaster;
use App\Models\ConsumptionDetail;
use App\Models\ConsumptionMaster;
use App\Models\PackingLotPostingDetail;
use App\Models\PackingLotPostingMaster;
use App\Models\ItemMaster;
use App\Models\Level2;
use App\Models\Mold;
use App\Models\Module;
use App\Models\ProductMaster;
use App\Models\StockInDetail;
use App\Models\UserPermission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class PackingLotPostingController extends Controller
{
    protected function ensureTables()
    {
        if (!Schema::hasTable('packing_lot_posting_masters')) {
            Schema::create('packing_lot_posting_masters', function ($table) {
                $table->id();
                $table->unsignedBigInteger('cid');
                $table->unsignedBigInteger('voucher_no');
                $table->date('date');
                $table->unsignedBigInteger('product_id')->nullable();
                $table->string('prepared_by')->nullable();
                $table->text('remarks')->nullable();
                $table->string('lot_no')->nullable();
                $table->string('prev_lot_no')->nullable();
                $table->decimal('prev_lot_amount', 15, 2)->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('packing_lot_posting_masters') && !Schema::hasColumn('packing_lot_posting_masters', 'deleted_at')) {
            Schema::table('packing_lot_posting_masters', function ($table) {
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('packing_lot_posting_details')) {
            Schema::create('packing_lot_posting_details', function ($table) {
                $table->id();
                $table->unsignedBigInteger('cid');
                $table->unsignedBigInteger('voucher_no');
                $table->unsignedBigInteger('contractor_id')->nullable();
                $table->unsignedBigInteger('item_id');
                $table->string('qty')->default(0);
                $table->string('unit')->nullable();
                $table->unsignedBigInteger('mold_id')->nullable();
                $table->string('mold_qty')->default(0);
                $table->decimal('wages', 15, 2)->nullable();
                $table->decimal('wages_amount', 15, 2)->nullable();
                $table->timestamps();
            });
        }
    }

    public static function autoGenerateCompletedLots($cid)
    {
        if (!$cid) {
            return;
        }

        (new self())->ensureTables();

        $stockInDetails = StockInDetail::with(['master'])
            ->where('cid', $cid)
            ->whereNotNull('product_id')
            ->whereNotNull('lot_no')
            ->where('lot_no', '!=', '')
            ->get();

        if ($stockInDetails->isEmpty()) {
            return;
        }

        $groups = $stockInDetails->groupBy(function ($d) {
            return $d->product_id . '_' . ($d->contractor_id ?? 0) . '_' . trim((string) $d->lot_no);
        });

        $completedLots = [];

        foreach ($groups as $key => $rows) {
            $first = $rows->first();
            $productId = $first->product_id;
            $contractorId = $first->contractor_id;
            $lotNo = trim((string) $first->lot_no);

            if ($lotNo === '') {
                continue;
            }

            $alreadyExists = PackingLotPostingMaster::withTrashed()
                ->where('cid', $cid)
                ->where('product_id', $productId)
                ->where('lot_no', $lotNo)
                ->exists();

            if ($alreadyExists) {
                continue;
            }

            $consumptionQuery = ConsumptionMaster::where('cid', $cid)
                ->where('item_id', $productId)
                ->whereNotNull('cartons_in_lot')
                ->where('cartons_in_lot', '>', 0);

            $matchedConsumption = null;
            if ($contractorId) {
                $matchedConsumption = (clone $consumptionQuery)
                    ->where('contractor_account_id', $contractorId)
                    ->where('lot_no', $lotNo)
                    ->first();

                if (!$matchedConsumption) {
                    $matchedConsumption = (clone $consumptionQuery)
                        ->where('contractor_account_id', $contractorId)
                        ->latest('id')
                        ->first();
                }
            }

            if (!$matchedConsumption) {
                $matchedConsumption = (clone $consumptionQuery)
                    ->where('lot_no', $lotNo)
                    ->first();
            }

            if (!$matchedConsumption) {
                $matchedConsumption = (clone $consumptionQuery)
                    ->latest('id')
                    ->first();
            }

            $cartonsInLot = $matchedConsumption ? (float) $matchedConsumption->cartons_in_lot : 0;
            $totalQty = (float) $rows->sum('qty');

            if ($cartonsInLot > 0 && $totalQty >= $cartonsInLot) {
                $latestStockIn = $rows->sortByDesc(function ($r) {
                    return $r->master ? ($r->master->date . '_' . sprintf('%08d', $r->inv_no)) : '0';
                })->first();

                $date = ($latestStockIn && $latestStockIn->master && $latestStockIn->master->date)
                    ? $latestStockIn->master->date
                    : Carbon::today()->toDateString();

                $completedLots[] = [
                    'product_id'          => $productId,
                    'contractor_id'       => $contractorId,
                    'lot_no'              => $lotNo,
                    'date'                => $date,
                    'cartons_in_lot'      => $cartonsInLot,
                    'total_qty'           => $totalQty,
                    'matched_consumption' => $matchedConsumption,
                ];
            }
        }

        if (empty($completedLots)) {
            return;
        }

        usort($completedLots, function ($a, $b) {
            $cmp = strcmp($a['date'], $b['date']);
            if ($cmp !== 0) return $cmp;
            return strcmp((string)$a['lot_no'], (string)$b['lot_no']);
        });

        foreach ($completedLots as $lotData) {
            $productId = $lotData['product_id'];
            $contractorId = $lotData['contractor_id'];
            $lotNo = $lotData['lot_no'];
            $date = $lotData['date'];
            $matchedConsumption = $lotData['matched_consumption'];

            $alreadyExists = PackingLotPostingMaster::withTrashed()
                ->where('cid', $cid)
                ->where('product_id', $productId)
                ->where('lot_no', $lotNo)
                ->exists();

            if ($alreadyExists) {
                continue;
            }

            $lastVoucher = PackingLotPostingMaster::where('cid', $cid)->max('voucher_no');
            $nextVoucherNo = $lastVoucher ? ((int) $lastVoucher + 1) : 1;

            $prevMaster = PackingLotPostingMaster::where('cid', $cid)
                ->where('product_id', $productId)
                ->orderByDesc('voucher_no')
                ->first();

            $prevLotNo = $prevMaster ? $prevMaster->lot_no : null;
            $prevLotAmount = $prevMaster
                ? (float) PackingLotPostingDetail::where('cid', $cid)->where('voucher_no', $prevMaster->voucher_no)->sum('wages_amount')
                : null;

            PackingLotPostingMaster::create([
                'cid'             => $cid,
                'voucher_no'      => $nextVoucherNo,
                'date'            => $date,
                'product_id'      => $productId,
                'prepared_by'     => 'Auto',
                'remarks'         => 'Auto generated on lot completion',
                'lot_no'          => $lotNo,
                'prev_lot_no'     => $prevLotNo,
                'prev_lot_amount' => $prevLotAmount,
            ]);

            $consMaster = $matchedConsumption ?? ConsumptionMaster::where('cid', $cid)
                ->where('item_id', $productId)
                ->latest('id')
                ->first();

            $detailsCreated = false;

            if ($consMaster) {
                $details = ConsumptionDetail::where('cid', $cid)
                    ->where('v_no', $consMaster->v_no)
                    ->get();

                $itemIds = $details->pluck('item_id')->filter()->unique();
                $itemMasters = ItemMaster::with('itemunits', 'itemtypes')
                    ->where('cid', $cid)
                    ->whereIn('id', $itemIds)
                    ->get()
                    ->keyBy('id');

                $moldIds = $details->pluck('mold_id')->filter()->unique();
                $molds = Mold::whereIn('id', $moldIds)
                    ->where(function ($q) use ($cid) {
                        $q->where('cid', $cid)->orWhereNull('cid');
                    })
                    ->get()
                    ->keyBy('id');

                foreach ($details as $detail) {
                    $item = $itemMasters->get($detail->item_id);
                    if (!$item) continue;

                    $itemTypeTitle = $item->itemtypes ? trim(strtolower($item->itemtypes->type_title)) : '';
                    $itemCode = trim(strtolower($item->item_code ?? ''));
                    $mold = $detail->mold_id ? ($molds->get($detail->mold_id) ?? null) : null;
                    $moldName = $mold ? trim(strtolower($mold->mold_name ?? '')) : '';

                    $isPacking = str_contains($itemTypeTitle, 'pack')
                        || str_contains($itemTypeTitle, 'پیک')
                        || str_contains($itemCode, 'pack')
                        || str_contains($itemCode, 'پیک')
                        || str_contains($moldName, 'pack')
                        || str_contains($moldName, 'پیک');

                    if (!$isPacking) {
                        continue;
                    }

                    $wages = 0;
                    if (!empty($detail->rate) && (float)$detail->rate > 0) {
                        $wages = (float) $detail->rate;
                    } elseif ($mold) {
                        $wages = (float) ($mold->total > 0 ? $mold->total : (($mold->wages ?? 0) + ($mold->expense ?? 0)));
                    }

                    $moldQty = $detail->qty !== null && $detail->qty !== '' ? (float) $detail->qty : 0;
                    $calcQty = $detail->calc_qty !== null && $detail->calc_qty !== '' ? (float) $detail->calc_qty : 0;
                    $wagesAmount = round($moldQty * $wages, 2);

                    PackingLotPostingDetail::create([
                        'cid'           => $cid,
                        'voucher_no'    => $nextVoucherNo,
                        'contractor_id' => $contractorId ?: ($detail->contractor_account_id ?? $consMaster->contractor_account_id),
                        'item_id'       => $detail->item_id,
                        'qty'           => $calcQty,
                        'unit'          => $item->itemunits->name ?? '',
                        'mold_id'       => $detail->mold_id,
                        'mold_qty'      => $moldQty,
                        'wages'         => $wages,
                        'wages_amount'  => $wagesAmount,
                    ]);
                    $detailsCreated = true;
                }
            }

            if (!$detailsCreated) {
                $packingItem = ItemMaster::where('cid', $cid)
                    ->where(function ($q) {
                        $q->whereHas('itemtypes', function ($sub) {
                            $sub->where('type_title', 'like', '%pack%');
                        })->orWhere('item_code', 'like', '%pack%');
                    })->first();

                if ($packingItem) {
                    $wages = (float) ($consMaster->wages_rate ?? $consMaster->packing_rate ?? 0);
                    $moldQty = (float) ($consMaster->lot_qty ?? $consMaster->qty ?? $lotData['cartons_in_lot']);
                    PackingLotPostingDetail::create([
                        'cid'           => $cid,
                        'voucher_no'    => $nextVoucherNo,
                        'contractor_id' => $contractorId ?: ($consMaster->contractor_account_id ?? null),
                        'item_id'       => $packingItem->id,
                        'qty'           => $moldQty,
                        'unit'          => $packingItem->itemunits->name ?? '',
                        'mold_id'       => $consMaster->mold_id ?? null,
                        'mold_qty'      => $moldQty,
                        'wages'         => $wages,
                        'wages_amount'  => round($moldQty * $wages, 2),
                    ]);
                }
            }
        }
    }

    public function index(Request $request)
    {
        $this->ensureTables();

        $module = Module::where('name', 'Packing Lot Posting')->first();
        if ($module) {
            $user = auth()->user();
            $hasPermission = UserPermission::where('module_id', $module->id)
                ->where('cid', $user->cid)
                ->where('userid', $user->id)
                ->where('read', 1)
                ->exists();

            // Allow admins to access even without explicit permission row
            if (!$hasPermission && !$user->is_admin) {
                return redirect()->back()->with('error', 'You have not Permission. Please Contact with your admin');
            }
        }

        $cid = Auth::user()->cid;

        // Auto-generate packing lot postings for any completed lots (cartons in lot = qty)
        static::autoGenerateCompletedLots($cid);

        $query = PackingLotPostingMaster::with(['details.contractor', 'details.item', 'product'])
            ->where('cid', $cid);

        if (request('voucher_no')) {
            $query->where('voucher_no', request('voucher_no'));
        }

        if (request('product_id')) {
            $query->where('product_id', request('product_id'));
        }

        if (request('contractor_id')) {
            $query->whereHas('details', function ($q) {
                $q->where('contractor_id', request('contractor_id'));
            });
        }

        if (request('startDate')) {
            $query->where('date', '>=', Carbon::parse(request('startDate'))->format('Y-m-d'));
        }

        if (request('endDate')) {
            $query->where('date', '<=', Carbon::parse(request('endDate'))->format('Y-m-d'));
        }

        $masters = $query->orderByDesc('date')
            ->orderByDesc('voucher_no')
            ->get();

        $products = ProductMaster::where('cid', $cid)->get();

        $contractorLevel = Level2::where('cid', $cid)->where('title', 'Contractor')->value('id');
        $contractors = AccountMaster::where('cid', $cid);
        if ($contractorLevel) {
            $contractors = $contractors->where('level2_id', $contractorLevel);
        }
        $contractors = $contractors->get();

        return view('packing-lot-posting.index', compact('masters', 'products', 'contractors'));
    }

    public function create()
    {
        $this->ensureTables();

        $cid = Auth::user()->cid;
        $products = ProductMaster::where('cid', $cid)->get();

        $contractorLevel = Level2::where('cid', $cid)->where('title', 'Contractor')->value('id');
        $contractors = AccountMaster::where('cid', $cid);
        if ($contractorLevel) {
            $contractors = $contractors->where('level2_id', $contractorLevel);
        }
        $contractors = $contractors->get();

        $lastVoucher = PackingLotPostingMaster::where('cid', $cid)->max('voucher_no');
        $nextVoucherNo = $lastVoucher ? ((int) $lastVoucher + 1) : 1;

        $previousLotByProduct = $this->previousLotByProduct($cid, null);

        $contractorByProduct = $this->contractorByPackingMold($cid);

        $molds = Mold::where(function ($q) use ($cid) {
            $q->where('cid', $cid)->orWhereNull('cid');
        })->get();

        $packingItems = ItemMaster::with('itemunits', 'itemtypes')
            ->where('cid', $cid)
            ->where(function ($q) {
                $q->whereHas('itemtypes', function ($sub) {
                    $sub->where('type_title', 'like', '%packing%');
                })->orWhere('item_code', 'like', '%packing%');
            })
            ->get();

        return view('packing-lot-posting.create', compact(
            'products',
            'contractors',
            'nextVoucherNo',
            'previousLotByProduct',
            'contractorByProduct',
            'molds',
            'packingItems'
        ));
    }

    public function store(Request $request)
    {
        $this->ensureTables();

        if (!$request->has('details') || empty(array_filter($request->details, function ($d) {
            return !empty($d['item_id']);
        }))) {
            return redirect()->back()->withInput()->withErrors(['details' => 'Please add at least one item.']);
        }

        $filteredDetails = array_values(array_filter($request->details, function ($d) {
            return !empty($d['item_id']);
        }));
        $request->merge(['details' => $filteredDetails]);

        $request->validate([
            'voucher_no'             => 'required|integer|unique:packing_lot_posting_masters,voucher_no',
            'date'                   => 'required|date',
            'product_id'             => 'nullable|exists:product_masters,id',
            'prepared_by'            => 'nullable|string|max:255',
            'remarks'                => 'nullable|string',
            'lot_no'                 => 'nullable|string|max:255',
            'prev_lot_no'            => 'nullable|string|max:255',
            'prev_lot_amount'        => 'nullable|numeric',
            'details'                => 'required|array|min:1',
            'details.*.item_id'      => 'required|exists:item_masters,id',
            'details.*.qty'          => 'required|numeric|min:0',
        ]);

        $cid = Auth::user()->cid;

        $master = PackingLotPostingMaster::create([
            'cid'             => $cid,
            'voucher_no'      => $request->voucher_no,
            'date'            => $request->date,
            'product_id'      => $request->product_id,
            'prepared_by'     => $request->prepared_by,
            'remarks'         => $request->remarks,
            'lot_no'          => $request->lot_no,
            'prev_lot_no'     => $request->prev_lot_no,
            'prev_lot_amount' => $request->prev_lot_amount,
        ]);

        foreach ($request->details as $detail) {
            if (empty($detail['item_id'])) {
                continue;
            }
            PackingLotPostingDetail::create([
                'cid'           => $cid,
                'voucher_no'    => $request->voucher_no,
                'contractor_id' => $detail['contractor_id'] ?? null,
                'item_id'       => $detail['item_id'],
                'qty'           => $detail['qty'] ?? 0,
                'unit'          => $detail['unit'] ?? null,
                'mold_id'       => $detail['mold_id'] ?? null,
                'mold_qty'      => $detail['mold_qty'] ?? 0,
                'wages'         => $detail['wages'] ?? 0,
                'wages_amount'  => $detail['wages_amount'] ?? 0,
            ]);
        }

        return redirect()->route('packing-lot-posting.index')->with('success', "Packing Lot Posting #$request->voucher_no created successfully!");
    }

    public function edit($id)
    {
        $this->ensureTables();

        $cid = Auth::user()->cid;
        $posting = PackingLotPostingMaster::with(['details'])->where('cid', $cid)->findOrFail($id);

        $products = ProductMaster::where('cid', $cid)->get();

        $contractorLevel = Level2::where('cid', $cid)->where('title', 'Contractor')->value('id');
        $contractors = AccountMaster::where('cid', $cid);
        if ($contractorLevel) {
            $contractors = $contractors->where('level2_id', $contractorLevel);
        }
        $contractors = $contractors->get();

        $previousLotByProduct = $this->previousLotByProduct($cid, $posting->voucher_no);

        $contractorByProduct = $this->contractorByPackingMold($cid);

        $molds = Mold::where(function ($q) use ($cid) {
            $q->where('cid', $cid)->orWhereNull('cid');
        })->get();

        $packingItems = ItemMaster::with('itemunits', 'itemtypes')
            ->where('cid', $cid)
            ->where(function ($q) {
                $q->whereHas('itemtypes', function ($sub) {
                    $sub->where('type_title', 'like', '%packing%');
                })->orWhere('item_code', 'like', '%packing%');
            })
            ->get();

        return view('packing-lot-posting.edit', compact(
            'posting',
            'products',
            'contractors',
            'previousLotByProduct',
            'contractorByProduct',
            'molds',
            'packingItems'
        ));
    }

    public function update(Request $request, $id)
    {
        $this->ensureTables();

        if (!$request->has('details') || empty(array_filter($request->details, function ($d) {
            return !empty($d['item_id']);
        }))) {
            return redirect()->back()->withInput()->withErrors(['details' => 'Please add at least one item.']);
        }

        $filteredDetails = array_values(array_filter($request->details, function ($d) {
            return !empty($d['item_id']);
        }));
        $request->merge(['details' => $filteredDetails]);

        $cid = Auth::user()->cid;
        $posting = PackingLotPostingMaster::where('cid', $cid)->findOrFail($id);

        $request->validate([
            'date'                   => 'required|date',
            'product_id'             => 'nullable|exists:product_masters,id',
            'prepared_by'            => 'nullable|string|max:255',
            'remarks'                => 'nullable|string',
            'lot_no'                 => 'nullable|string|max:255',
            'prev_lot_no'            => 'nullable|string|max:255',
            'prev_lot_amount'        => 'nullable|numeric',
            'details'                => 'required|array|min:1',
            'details.*.item_id'      => 'required|exists:item_masters,id',
            'details.*.qty'          => 'required|numeric|min:0',
        ]);

        $posting->update([
            'date'            => $request->date,
            'product_id'      => $request->product_id,
            'prepared_by'     => $request->prepared_by,
            'remarks'         => $request->remarks,
            'lot_no'          => $request->lot_no,
            'prev_lot_no'     => $request->prev_lot_no,
            'prev_lot_amount' => $request->prev_lot_amount,
        ]);

        PackingLotPostingDetail::where('cid', $cid)->where('voucher_no', $posting->voucher_no)->delete();

        foreach ($request->details as $detail) {
            if (empty($detail['item_id'])) {
                continue;
            }
            PackingLotPostingDetail::create([
                'cid'           => $cid,
                'voucher_no'    => $posting->voucher_no,
                'contractor_id' => $detail['contractor_id'] ?? null,
                'item_id'       => $detail['item_id'],
                'qty'           => $detail['qty'] ?? 0,
                'unit'          => $detail['unit'] ?? null,
                'mold_id'       => $detail['mold_id'] ?? null,
                'mold_qty'      => $detail['mold_qty'] ?? 0,
                'wages'         => $detail['wages'] ?? 0,
                'wages_amount'  => $detail['wages_amount'] ?? 0,
            ]);
        }

        return redirect()->route('packing-lot-posting.index')->with('success', "Packing Lot Posting #$posting->voucher_no updated successfully!");
    }

    public function destroy($id)
    {
        $cid = Auth::user()->cid;
        $posting = PackingLotPostingMaster::where('cid', $cid)->findOrFail($id);

        PackingLotPostingDetail::where('cid', $cid)->where('voucher_no', $posting->voucher_no)->delete();
        $posting->delete();

        return redirect()->route('packing-lot-posting.index')->with('success', "Packing Lot Posting #$posting->voucher_no deleted successfully!");
    }

    public function getItemsByProduct($productId)
    {
        $cid = Auth::user()->cid;

        $master = ConsumptionMaster::where('cid', $cid)
            ->where('item_id', $productId)
            ->latest()
            ->first();

        if (!$master) {
            return response()->json([]);
        }

        $details = ConsumptionDetail::where('cid', $cid)
            ->where('v_no', $master->v_no)
            ->get();

        $itemIds = $details->pluck('item_id')->filter()->unique();
        $itemMasters = ItemMaster::with('itemunits', 'itemtypes')
            ->where('cid', $cid)
            ->whereIn('id', $itemIds)
            ->get()
            ->keyBy('id');

        $moldIds = $details->pluck('mold_id')->filter()->unique();
        $molds = Mold::whereIn('id', $moldIds)
            ->where(function ($q) use ($cid) {
                $q->where('cid', $cid)->orWhereNull('cid');
            })
            ->get()
            ->keyBy('id');

        $items = $details->map(function ($detail) use ($itemMasters, $molds, $master) {
            $item = $itemMasters->get($detail->item_id);
            if (!$item) return null;

            $itemTypeTitle = $item->itemtypes ? trim(strtolower($item->itemtypes->type_title)) : '';
            $itemCode = trim(strtolower($item->item_code ?? ''));
            $mold = $detail->mold_id ? ($molds->get($detail->mold_id) ?? null) : null;
            $moldName = $mold ? trim(strtolower($mold->mold_name ?? '')) : '';

            // Pick only the row whose type/item/mold is packing
            $isPacking = str_contains($itemTypeTitle, 'pack')
                || str_contains($itemTypeTitle, 'پیک')
                || str_contains($itemCode, 'pack')
                || str_contains($itemCode, 'پیک')
                || str_contains($moldName, 'pack')
                || str_contains($moldName, 'پیک');

            if (!$isPacking) {
                return null;
            }

            $wages = 0;
            if (!empty($detail->rate) && (float)$detail->rate > 0) {
                $wages = (float) $detail->rate;
            } elseif ($mold) {
                $wages = (float) ($mold->total > 0 ? $mold->total : (($mold->wages ?? 0) + ($mold->expense ?? 0)));
            }

            return [
                'id'            => $detail->item_id,
                'item_code'     => $item->item_code ?? '',
                'unit'          => $item->itemunits->name ?? '',
                'mold_id'       => $detail->mold_id,
                'mold_name'     => $mold->mold_name ?? '',
                'mold_qty'      => $detail->qty !== null && $detail->qty !== '' ? (float) $detail->qty : 0,
                'calc_qty'      => $detail->calc_qty !== null && $detail->calc_qty !== '' ? (float) $detail->calc_qty : 0,
                'wages'         => $wages,
                'contractor_id' => $detail->contractor_account_id ?? $master->contractor_account_id,
            ];
        })->filter()->values();

        return response()->json($items);
    }

    private function previousLotByProduct($cid, $excludeVoucherNo)
    {
        $query = PackingLotPostingMaster::where('cid', $cid)
            ->whereNotNull('product_id');

        if ($excludeVoucherNo !== null) {
            $query->where('voucher_no', '<>', $excludeVoucherNo);
        }

        $result = [];
        foreach ($query->orderByDesc('voucher_no')->get() as $master) {
            if (isset($result[$master->product_id])) {
                continue;
            }
            $amount = PackingLotPostingDetail::where('cid', $cid)
                ->where('voucher_no', $master->voucher_no)
                ->sum('wages_amount');

            $result[$master->product_id] = [
                'lot_no'     => $master->lot_no,
                'lot_amount' => $amount,
            ];
        }

        return $result;
    }

    private function contractorByPackingMold($cid)
    {
        $packingMoldIds = Mold::where(function ($q) use ($cid) {
            $q->where('cid', $cid)->orWhereNull('cid');
        })
        ->where(function ($q) {
            $q->where('mold_name', 'like', '%packing%')
              ->orWhere('mold_id', 'like', '%packing%');
        })
        ->pluck('id');

        // Look for contractor from ConsumptionDetail for packing mold
        $detailContractors = ConsumptionDetail::where('cid', $cid)
            ->whereIn('mold_id', $packingMoldIds)
            ->whereNotNull('contractor_account_id')
            ->orderByDesc('id')
            ->get();

        $map = [];
        if ($detailContractors->isNotEmpty()) {
            $vNos = $detailContractors->pluck('v_no')->unique();
            $masters = ConsumptionMaster::where('cid', $cid)->whereIn('v_no', $vNos)->whereNotNull('item_id')->get()->keyBy('v_no');
            foreach ($detailContractors as $d) {
                if (isset($masters[$d->v_no])) {
                    $pid = $masters[$d->v_no]->item_id;
                    if (!isset($map[$pid])) {
                        $map[$pid] = $d->contractor_account_id;
                    }
                }
            }
        }

        $query = ConsumptionMaster::where('cid', $cid)
            ->whereNotNull('item_id')
            ->whereNotNull('contractor_account_id');

        if ($packingMoldIds->isNotEmpty()) {
            $query->whereIn('mold_id', $packingMoldIds);
        }

        $masterMap = $query->orderByDesc('id')
            ->get(['item_id', 'contractor_account_id'])
            ->groupBy('item_id')
            ->map(function ($rows) {
                return $rows->first()->contractor_account_id;
            })->toArray();

        return array_merge($masterMap, $map);
    }
}
