<?php

namespace App\Http\Controllers;

use App\Models\AccountMaster;
use App\Models\ConsumptionDetail;
use App\Models\ConsumptionMaster;
use App\Models\ContractorLotPostingDetail;
use App\Models\ContractorLotPostingMaster;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class ContractorLotPostingController extends Controller
{
    protected function ensureTables()
    {
        if (!Schema::hasTable('contractor_lot_posting_masters')) {
            Schema::create('contractor_lot_posting_masters', function ($table) {
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
                $table->softDeletes();
            });
        } elseif (!Schema::hasColumn('contractor_lot_posting_masters', 'deleted_at')) {
            Schema::table('contractor_lot_posting_masters', function ($table) {
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('contractor_lot_posting_details')) {
            Schema::create('contractor_lot_posting_details', function ($table) {
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

    public function index(Request $request)
    {
        $this->ensureTables();

        $module = Module::where('name', 'Contractor Lot Posting')->first();
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
        self::autoGenerateLots($cid);

        $query = ContractorLotPostingMaster::with(['details.contractor', 'details.item', 'product'])
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

        return Inertia::render('Purchase/ContractorLotPosting/Index', [
            'postings' => $masters,
            'masters' => $masters,
            'products' => $products,
            'contractors' => $contractors,
            'filters' => $request->all(),
        ]);
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

        $lastVoucher = ContractorLotPostingMaster::where('cid', $cid)->max('voucher_no');
        $nextVoucherNo = $lastVoucher ? ((int) $lastVoucher + 1) : 1;

        $previousLotByProduct = $this->previousLotByProduct($cid, null);

        $contractorByProduct = $this->contractorByPackingMold($cid);

        $contractorByMold = ConsumptionDetail::where('cid', $cid)
            ->whereNotNull('mold_id')
            ->whereNotNull('contractor_account_id')
            ->orderByDesc('id')
            ->get(['mold_id', 'contractor_account_id'])
            ->groupBy('mold_id')
            ->map(function ($rows) {
                return $rows->first()->contractor_account_id;
            })
            ->toArray();

        $molds = Mold::where(function ($q) use ($cid) {
            $q->where('cid', $cid)->orWhereNull('cid');
        })->get();

        $rawItems = ItemMaster::where('cid', $cid)->get();

        return Inertia::render('Purchase/ContractorLotPosting/CreateEdit', compact(
            'products',
            'contractors',
            'nextVoucherNo',
            'previousLotByProduct',
            'contractorByProduct',
            'contractorByMold',
            'molds',
            'rawItems'
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
            'voucher_no'             => 'required|integer|unique:contractor_lot_posting_masters,voucher_no',
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

        $master = ContractorLotPostingMaster::create([
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
            ContractorLotPostingDetail::create([
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

        return redirect()->route('contractor-lot-posting.index')->with('success', "Contractor Lot Posting #$request->voucher_no created successfully!");
    }

    public function edit($id)
    {
        $this->ensureTables();

        $cid = Auth::user()->cid;
        $posting = ContractorLotPostingMaster::with(['details'])->where('cid', $cid)->findOrFail($id);

        $products = ProductMaster::where('cid', $cid)->get();

        $contractorLevel = Level2::where('cid', $cid)->where('title', 'Contractor')->value('id');
        $contractors = AccountMaster::where('cid', $cid);
        if ($contractorLevel) {
            $contractors = $contractors->where('level2_id', $contractorLevel);
        }
        $contractors = $contractors->get();

        $previousLotByProduct = $this->previousLotByProduct($cid, $posting->voucher_no);

        $contractorByProduct = $this->contractorByPackingMold($cid);

        $contractorByMold = ConsumptionDetail::where('cid', $cid)
            ->whereNotNull('mold_id')
            ->whereNotNull('contractor_account_id')
            ->orderByDesc('id')
            ->get(['mold_id', 'contractor_account_id'])
            ->groupBy('mold_id')
            ->map(function ($rows) {
                return $rows->first()->contractor_account_id;
            })
            ->toArray();

        $molds = Mold::where(function ($q) use ($cid) {
            $q->where('cid', $cid)->orWhereNull('cid');
        })->get();

        $rawItems = ItemMaster::where('cid', $cid)->get();

        return Inertia::render('Purchase/ContractorLotPosting/CreateEdit', array_merge(
            compact(
                'posting',
                'products',
                'contractors',
                'previousLotByProduct',
                'contractorByProduct',
                'contractorByMold',
                'molds',
                'rawItems'
            ),
            ['isEdit' => true]
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
        $posting = ContractorLotPostingMaster::where('cid', $cid)->findOrFail($id);

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

        ContractorLotPostingDetail::where('cid', $cid)->where('voucher_no', $posting->voucher_no)->delete();

        foreach ($request->details as $detail) {
            if (empty($detail['item_id'])) {
                continue;
            }
            ContractorLotPostingDetail::create([
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

        return redirect()->route('contractor-lot-posting.index')->with('success', "Contractor Lot Posting #$posting->voucher_no updated successfully!");
    }

    public function destroy($id)
    {
        $this->ensureTables();
        $cid = Auth::user()->cid;
        $posting = ContractorLotPostingMaster::where('cid', $cid)->findOrFail($id);

        ContractorLotPostingDetail::where('cid', $cid)->where('voucher_no', $posting->voucher_no)->delete();
        $posting->delete();

        return redirect()->route('contractor-lot-posting.index')->with('success', "Contractor Lot Posting #$posting->voucher_no deleted successfully!");
    }

    public function getItemsByProduct($productId, $overrideCid = null)
    {
        $cid = $overrideCid ?? (Auth::check() ? Auth::user()->cid : null);

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

        // Fallback contractor lookup by mold from other consumptions if not set in this detail
        $contractorByMold = ConsumptionDetail::where('cid', $cid)
            ->whereNotNull('mold_id')
            ->whereNotNull('contractor_account_id')
            ->orderByDesc('id')
            ->get(['mold_id', 'contractor_account_id'])
            ->groupBy('mold_id')
            ->map(function ($rows) {
                return $rows->first()->contractor_account_id;
            })
            ->toArray();

        $items = $details->map(function ($detail) use ($itemMasters, $molds, $master, $contractorByMold) {
            $item = $itemMasters->get($detail->item_id);
            if (!$item) return null;

            $itemTypeTitle = $item->itemtypes ? trim(mb_strtolower($item->itemtypes->type_title ?? '')) : '';
            $codeLower = trim(mb_strtolower($item->item_code ?? ''));
            $isDana = str_contains($itemTypeTitle, 'dana') || str_contains($itemTypeTitle, 'دانہ') || str_contains($codeLower, 'dana') || str_contains($codeLower, 'دانہ');
            if (!$isDana) {
                return null;
            }

            $mold = $detail->mold_id ? ($molds->get($detail->mold_id) ?? null) : null;
            $wages = 0;
            if (!empty($detail->rate) && (float)$detail->rate > 0) {
                $wages = (float) $detail->rate;
            } elseif ($mold) {
                $wages = (float) ($mold->total > 0 ? $mold->total : (($mold->wages ?? 0) + ($mold->expense ?? 0)));
            }

            $contractorId = $detail->contractor_account_id;
            if (!$contractorId && $detail->mold_id && isset($contractorByMold[$detail->mold_id])) {
                $contractorId = $contractorByMold[$detail->mold_id];
            }
            if (!$contractorId) {
                $contractorId = $master->contractor_account_id;
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
                'contractor_id' => $contractorId,
            ];
        })->filter()->values();

        return response()->json($items);
    }

    private function previousLotByProduct($cid, $excludeVoucherNo)
    {
        $query = ContractorLotPostingMaster::where('cid', $cid)
            ->whereNotNull('product_id');

        if ($excludeVoucherNo !== null) {
            $query->where('voucher_no', '<>', $excludeVoucherNo);
        }

        $result = [];
        foreach ($query->orderByDesc('voucher_no')->get() as $master) {
            if (isset($result[$master->product_id])) {
                continue;
            }
            $amount = ContractorLotPostingDetail::where('cid', $cid)
                ->where('voucher_no', $master->voucher_no)
                ->sum('wages_amount');

            $result[$master->product_id] = [
                'lot_no'   => $master->lot_no,
                'lot_amount' => $amount,
            ];
        }

        $stockInDetails = StockInDetail::where('cid', $cid)
            ->whereNotNull('product_id')
            ->whereNotNull('lot_no')
            ->where('lot_no', '!=', '')
            ->orderByDesc('id')
            ->get();

        foreach ($stockInDetails as $siDetail) {
            if (!isset($result[$siDetail->product_id])) {
                $result[$siDetail->product_id] = [
                    'lot_no'      => $siDetail->lot_no,
                    'lot_amount'  => 0,
                    'is_stock_in' => true,
                ];
            } else {
                $result[$siDetail->product_id]['stock_in_lot_no'] = $siDetail->lot_no;
            }
        }

        return $result;
    }

    private function contractorByPackingMold($cid)
    {
        $stockInContractors = StockInDetail::where('cid', $cid)
            ->whereNotNull('product_id')
            ->whereNotNull('contractor_id')
            ->orderByDesc('id')
            ->get(['product_id', 'contractor_id'])
            ->groupBy('product_id')
            ->map(function ($rows) {
                return $rows->first()->contractor_id;
            })
            ->toArray();

        $packingMoldIds = Mold::where(function ($q) use ($cid) {
            $q->where('cid', $cid)->orWhereNull('cid');
        })
        ->where(function ($q) {
            $q->where('mold_name', 'like', '%packing%')
              ->orWhere('mold_id', 'like', '%packing%');
        })
        ->pluck('id');

        $query = ConsumptionMaster::where('cid', $cid)
            ->whereNotNull('item_id')
            ->whereNotNull('contractor_account_id');

        if ($packingMoldIds->isNotEmpty()) {
            $query->whereIn('mold_id', $packingMoldIds);
        }

        $consumptionContractors = $query->orderByDesc('id')
            ->get(['item_id', 'contractor_account_id'])
            ->groupBy('item_id')
            ->map(function ($rows) {
                return $rows->first()->contractor_account_id;
            })
            ->toArray();

        return $stockInContractors + $consumptionContractors;
    }

    public static function autoGenerateLots($cid)
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

        foreach ($groups as $key => $rows) {
            $first = $rows->first();
            $productId = $first->product_id;
            $contractorId = $first->contractor_id;
            $lotNo = trim((string) $first->lot_no);

            if ($lotNo === '') {
                continue;
            }

            $alreadyExists = ContractorLotPostingMaster::withTrashed()->where('cid', $cid)
                ->where('product_id', $productId)
                ->where('lot_no', $lotNo)
                ->exists();

            if ($alreadyExists) {
                continue;
            }

            $latestStockIn = $rows->sortByDesc(function ($r) {
                return $r->master ? ($r->master->date . '_' . sprintf('%08d', $r->inv_no)) : '0';
            })->first();

            $date = ($latestStockIn && $latestStockIn->master && $latestStockIn->master->date)
                ? $latestStockIn->master->date
                : Carbon::today()->toDateString();

            $lastVoucher = ContractorLotPostingMaster::where('cid', $cid)->max('voucher_no');
            $nextVoucherNo = $lastVoucher ? ((int) $lastVoucher + 1) : 1;

            $master = ContractorLotPostingMaster::create([
                'cid'        => $cid,
                'voucher_no' => $nextVoucherNo,
                'date'       => $date,
                'product_id' => $productId,
                'lot_no'     => $lotNo,
            ]);

            $itemsResponse = (new self())->getItemsByProduct($productId, $cid);
            $items = json_decode($itemsResponse->getContent(), true);

            if (!empty($items)) {
                // Collect all contractor_ids already in items (from consumption)
                $itemContractorIds = array_filter(array_column($items, 'contractor_id'));

                foreach ($items as $item) {
                    $moldQty = isset($item['mold_qty']) && $item['mold_qty'] !== '' ? (float)$item['mold_qty'] : 0;
                    $wages = isset($item['wages']) && $item['wages'] !== '' ? (float)$item['wages'] : 0;
                    $wagesAmount = round($moldQty * $wages, 2);

                    ContractorLotPostingDetail::create([
                        'cid'           => $cid,
                        'voucher_no'    => $nextVoucherNo,
                        'contractor_id' => $item['contractor_id'] ?? $contractorId,
                        'item_id'       => $item['id'],
                        'qty'           => $item['calc_qty'] ?? 0,
                        'unit'          => $item['unit'] ?? null,
                        'mold_id'       => $item['mold_id'] ?? null,
                        'mold_qty'      => $moldQty,
                        'wages'         => $wages,
                        'wages_amount'  => $wagesAmount,
                    ]);
                }

                // If Stock In contractor is different from all consumption contractors,
                // add a separate row so it appears in ledger
                if ($contractorId && !in_array($contractorId, $itemContractorIds)) {
                    $firstItem = $items[0];
                    ContractorLotPostingDetail::create([
                        'cid'           => $cid,
                        'voucher_no'    => $nextVoucherNo,
                        'contractor_id' => $contractorId,
                        'item_id'       => $firstItem['id'],
                        'qty'           => 0,
                        'unit'          => $firstItem['unit'] ?? null,
                        'mold_id'       => $firstItem['mold_id'] ?? null,
                        'mold_qty'      => 0,
                        'wages'         => 0,
                        'wages_amount'  => 0,
                    ]);
                }
            }
        }
    }
}
