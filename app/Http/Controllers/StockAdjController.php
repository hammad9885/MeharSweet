<?php

namespace App\Http\Controllers;

use App\Models\StockAdjMaster;
use App\Models\StockAdjDetail;
use App\Models\ItemMaster;
use App\Models\Module;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockAdjController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $user = auth()->user();
     
        $masters = StockAdjMaster::with('details')->latest()->get();
        return view('stock-adj.index', compact('masters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         $user = auth()->user();
    
        // compute next voucher number based on max existing v_no
        $lastVNo = StockAdjMaster::max('v_no');
        $nextVNo = $lastVNo ? ((int) $lastVNo + 1) : 1;

        // prepared_by should be the cid of the logged in user
        $preparedByCid = auth()->user()->name ?? null;

        // load items for the select (filter by company cid when available)
        $cid = auth()->user()->cid ?? null;
        $items = ItemMaster::when($cid, function ($q, $cid) {
            $q->where('cid', $cid);
        })->get();

        return view('stock-adj.create', compact('nextVNo', 'preparedByCid', 'items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'v_no' => 'required|unique:stock_adj_masters,v_no',
            'v_date' => 'required|date',
            // prepared_by will be set server-side to the logged-in user's cid
            'prepared_by' => 'nullable',
            'details.*.item_id' => 'required|numeric',
            'details.*.qty' => 'required|numeric',
            'details.*.rate' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $master = StockAdjMaster::create([
                'v_no' => $request->v_no,
                'v_date' => $request->v_date,
                // ensure prepared_by stores the cid of the logged-in user
                'prepared_by' => auth()->user()->cid ?? $request->prepared_by,
                'cid'=>auth()->user()->cid ?? null,
            ]);

           foreach ($request->details as $detail) {
    $master->details()->create(array_merge($detail, [
        'cid' => auth()->user()->cid,'v_date'=>$request->v_date
    ]));
}
        });

        return redirect()->route('stock-adj.index')->with('success', 'Stock Adjustment saved successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
{
    $stock_adj = StockAdjMaster::with(['details.item', 'preparedByUser'])->findOrFail($id);
    return view('stock-adj.show', compact('stock_adj'));
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockAdjMaster $stock_adj)
    {
         $user = auth()->user();
   
        $stock_adj->load('details');
        $items = ItemMaster::when(auth()->user()->cid, function ($q) {
            $q->where('cid', auth()->user()->cid);
        })->get();
        return view('stock-adj.edit', compact('stock_adj', 'items'));
    }

    /**
     * Update the specified resource in storage.
     */
  public function update(Request $request, StockAdjMaster $stock_adj)
{
    $request->validate([
        'v_date' => 'required|date',
        'prepared_by' => 'required|string|max:255',
        'details.*.item_id' => 'required|numeric|exists:item_masters,id',
        'details.*.qty' => 'required|numeric', // ✅ allows negative qty
        'details.*.rate' => 'nullable|numeric|min:0',
    ]);

    DB::transaction(function () use ($request, $stock_adj) {
        // ✅ Update master record
        $stock_adj->update([
            'v_date' => $request->v_date,
            'prepared_by' => $request->prepared_by,
            'cid' => auth()->user()->cid,
        ]);

        // ✅ Delete old detail records
        $stock_adj->details()->delete();

        // ✅ Recreate details
        foreach ($request->details as $detail) {
            $stock_adj->details()->create([
                'item_id' => $detail['item_id'],
                'qty' => $detail['qty'],
                 'v_date' => $request->v_date,
                'rate' => $detail['rate'],
                'cid' => auth()->user()->cid,
            ]);
        }
    });

    return redirect()->route('stock-adj.index')->with('success', 'Stock Adjustment updated successfully!');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockAdjMaster $stock_adj)
    {
         $user = auth()->user();
    
        StockAdjDetail::where('cid',auth()->user()->cid)->where('v_no',$stock_adj->v_no)->delete();
        $stock_adj->delete();
        return redirect()->route('stock-adj.index')->with('success', 'Stock Adjustment deleted successfully!');
    }
}
