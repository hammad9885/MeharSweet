<?php

namespace App\Http\Controllers;

use App\Models\ItemMaster;
use Illuminate\Http\Request;
use App\Models\Units;
use Illuminate\Support\Facades\Auth;

class ItemUnits extends Controller
{
    public function index()
    {
        $cid = Auth::user()->cid;
        $units = Units::where('cid', $cid)->orderByDesc('id')->get();
        return \Inertia\Inertia::render('Inventory/Units/Index', compact('units'));
    }

    public function create()
    {
        return $this->index();
    }

    public function store(Request $request)
    {
        $cid = Auth::user()->cid;

        $request->validate([
            'name' => 'required'
        ]);

        Units::create([
            'name' => $request->name,
            'cid'  => $cid,
        ]);

        return redirect()->route('units.index')->with('success', 'Unit created successfully.');
    }

    public function edit(string $id)
    {
        $cid = Auth::user()->cid;
        $units = Units::where('cid', $cid)->orderByDesc('id')->get();
        $editingUnit = Units::where('cid', $cid)->findOrFail($id);

        return \Inertia\Inertia::render('Inventory/Units/Index', [
            'units' => $units,
            'editingUnit' => $editingUnit,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $cid = Auth::user()->cid;

        $request->validate([
            'name' => 'required|string|max:255|unique:units,name,' . $id . ',id,cid,' . $cid,
        ]);

        $unit = Units::where('cid', $cid)->findOrFail($id);
        $unit->update([
            'name' => $request->name,
        ]);

        return redirect()->route('units.index')->with('success', 'Unit updated successfully.');
    }

    public function destroy(string $id)
    {
        $cid = Auth::user()->cid;
        $unit = Units::where('cid', $cid)->findOrFail($id);

        $item=ItemMaster::where('unit_id',$unit->id)->first();

        if($item){
            return redirect()->back()->with('error','This Unit Is connected with Item TRegistartion');
        }

        $unit->delete();

        return redirect()->route('units.index')->with('success', 'Unit deleted successfully.');
    }
}
