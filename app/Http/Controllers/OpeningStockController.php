<?php

namespace App\Http\Controllers;

use App\Models\OpenStock;
use App\Models\ItemMaster;
use Illuminate\Http\Request;

class OpeningStockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $item_id = $request->input('item_id');
        $item_type = $request->input('item_type');

        $opening = OpenStock::with('item')
                   ->where('cid', auth()->user()->cid)
                   ->when($item_id, function($q) use ($item_id) {
                    $q->where('item_id', $item_id);
                    })
                    ->when($item_type, function($q) use ($item_type) {
                    $q->whereHas('item', function($query) use ($item_type) {
                    $query->where('item_type', $item_type);
        });
    })->get();

    $items = ItemMaster::where('cid', auth()->user()->cid)->get();
    return view('openStock.index', compact('opening', 'items'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $items=ItemMaster::where('cid',auth()->user()->cid)->get();
         return view('openStock.create',compact('items'));
    }

    /**
     * Store a newly created resource in storage.
     */
     public function store(Request $request)
{
    $validated = $request->validate([
        'items.*.item_id' => 'required|exists:item_masters,id',
        'items.*.rate' => 'required|numeric|min:0',
        'items.*.qty' => 'required|numeric|min:1',
    ]);

    foreach ($request->items as $item) {
        $v_no= OpenStock::where('cid',auth()->user()->cid)->max('v_no')??0;
        $v_no+=1;
        OpenStock::create([
            'item_id' => $item['item_id'],
            'cid' => auth()->user()->cid,
            'qty' => $item['qty'],
            'rate' => $item['rate'],
            'v_date' => $item['date'],
            'v_no'=>$v_no
        ]);
    }

    return redirect()->route('openingStock.index')->with('success', 'Open stock inserted successfully!');
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        OpenStock::where('id',$id)->where('cid',auth()->user()->cid)->delete();
        return redirect()->route('openingStock.index')->with('success',"Item deleted Successfully");
    }
}
