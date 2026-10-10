<?php

namespace App\Http\Controllers;

use App\Models\ConsumptionDetail;
use App\Models\ConsumptionMaster;
use App\Models\Mold;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemMoldController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $companyId = Auth::user()->cid;
        $query = Mold::where('cid', $companyId);

        if ($request->mold_id) {
            $query->where('id', $request->mold_id);
        }

        $itemtypes = $query->get();
        $molds = Mold::where('cid', $companyId)->get();

        return view('mold.index', get_defined_vars());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Auto-generate Mold ID
        $last = Mold::where('cid', auth()->user()->cid)->latest()->first();
        $moldId = 'MOLD-' . str_pad(($last->id ?? 0) + 1, 4, '0', STR_PAD_LEFT);

        return view('mold.create', compact('moldId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'mold_name' => 'required|string|max:255',
        ]);

        // Auto-generate Mold ID again (safe)
        $last = Mold::where('cid', auth()->user()->cid)->latest()->first();
        $moldId = 'MOLD-' . str_pad(($last->id ?? 0) + 1, 4, '0', STR_PAD_LEFT);

        $total = ($request->wages ?? 0) + ($request->expense ?? 0);

        Mold::create([
            'mold_id'   => $moldId,
            'mold_name' => $request->mold_name,
            'item_type' => $request->item_type,
            'weight'    => $request->Gram,
            'wages'     => $request->wages,
            'expense'   => $request->expense,
            'total'     => $total,
            'remarks'   => $request->remarks,
            'cid'       => auth()->user()->cid
        ]);

        return redirect()->route('mold.index')->with('success', 'Mold Added Successfully');
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
        $mold = Mold::where('cid', auth()->user()->cid)->findOrFail($id);
        return view('mold.edit', compact('mold'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'mold_name' => 'required|string|max:255',
        ]);

        $mold = Mold::where('cid', auth()->user()->cid)->findOrFail($id);
        $oldWages = $mold->wages;
        $mold->update($request->all());
        $newWages = $mold->wages;

        // Mass update: if wages changed, recalculate all consumption records using this mold
        if ($oldWages != $newWages) {
            // Update consumption_details
            $details = ConsumptionDetail::where('mold_id', $mold->id)->get();
            foreach ($details as $detail) {
                $detail->rate = $newWages;
                $detail->amount = ($detail->qty ?? 0) * $newWages;
                $detail->save();
            }

            // Update consumption_masters
            $masters = ConsumptionMaster::where('mold_id', $mold->id)->get();
            foreach ($masters as $master) {
                $master->wages_rate = $newWages;
                $master->wages_amount = ($master->mold_qty ?? 0) * $newWages;
                $master->save();
            }
        }

        return redirect()->route('mold.index')->with('success', 'Mold Updated Successfully. All related consumption records have been recalculated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}