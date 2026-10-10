<?php

namespace App\Http\Controllers;

use App\Models\ItemMaster;
use Illuminate\Http\Request;
use App\Models\SaleType;
use Illuminate\Support\Facades\Auth;

class SaleTypeController extends Controller
{
    public function index()
    {
        $cid = Auth::user()->cid;
        $saleTypes = SaleType::where('cid', $cid)->get();

        return view('saletype.index', compact('saleTypes'));
    }

    public function create()
    {
        return view('saletype.create');
    }

    public function store(Request $request)
    {
        $cid = Auth::user()->cid;

        $request->validate([
            'sale_type' => 'required'
        ]);

        SaleType::create([
            'sale_type' => $request->sale_type,
            'cid'       => $cid,
        ]);

        return redirect()->route('saletype.index')->with('success', 'Sale Type created successfully.');
    }

    public function show(string $id)
    {
        $cid = Auth::user()->cid;
        $saleType = SaleType::where('cid', $cid)->findOrFail($id);

        return view('saletype.show', compact('saleType'));
    }

    public function edit(string $id)
    {
        $cid = Auth::user()->cid;
        $saleType = SaleType::where('cid', $cid)->findOrFail($id);

        return view('saletype.edit', compact('saleType'));
    }

    public function update(Request $request, string $id)
    {
        $cid = Auth::user()->cid;

        $request->validate([
            'sale_type' => 'required|string|max:255|unique:sale_type,sale_type,' . $id . ',id,cid,' . $cid,
        ]);

        $saleType = SaleType::where('cid', $cid)->findOrFail($id);
        $saleType->update([
            'sale_type' => $request->sale_type,
        ]);

        return redirect()->route('saletype.index')->with('success', 'Sale Type updated successfully.');
    }

    public function destroy(string $id)
    {
        $cid = Auth::user()->cid;
        $saleType = SaleType::where('cid', $cid)->findOrFail($id);

        $item=ItemMaster::where('s_type',$saleType->id)->first();
        if($item){
            return redirect()->back()->with('error','This Sale type is connect with Item registration');
        }

        $saleType->delete();

        return redirect()->route('saletype.index')->with('success', 'Sale Type deleted successfully.');
    }
}
