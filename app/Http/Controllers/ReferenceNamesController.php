<?php

namespace App\Http\Controllers;

use App\Models\ReferenceName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferenceNamesController extends Controller
{
    public function index()
    {
        $user = Auth::user()->cid;
        $referenceNames = ReferenceName::where('cid', $user)->orderByDesc('id')->get();
        return view('ReferenceNames.index', compact('referenceNames'));
    }

    public function create()
    {
        return view('ReferenceNames.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_urdu' => 'nullable|string|max:255',
        ]);

        ReferenceName::create([
            'name' => $request->name,
            'name_urdu' => $request->name_urdu,
            'cid' => Auth::user()->cid,
        ]);

        return redirect()->route('ReferenceNames.index')->with('success', 'Reference Name created successfully.');
    }

    public function edit($id)
    {
        $user = Auth::user()->cid;
        $referenceName = ReferenceName::where('cid', $user)->findOrFail($id);
        return view('ReferenceNames.edit', compact('referenceName'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_urdu' => 'nullable|string|max:255',
        ]);

        $user = Auth::user()->cid;
        $referenceName = ReferenceName::where('cid', $user)->findOrFail($id);
        $referenceName->update([
            'name' => $request->name,
            'name_urdu' => $request->name_urdu,
        ]);

        return redirect()->route('ReferenceNames.index')->with('success', 'Reference Name updated successfully.');
    }

    public function destroy($id)
    {
        $user = Auth::user()->cid;
        $referenceName = ReferenceName::where('cid', $user)->findOrFail($id);
        $referenceName->delete();

        return redirect()->route('ReferenceNames.index')->with('success', 'Reference Name deleted successfully.');
    }
}
