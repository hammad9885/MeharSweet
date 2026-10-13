<?php

namespace App\Http\Controllers;

use App\Models\TransportCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransportCompanyController extends Controller
{
    public function index()
    {
        $user = Auth::user()->cid;
        $transportCompanies = TransportCompany::where('cid', $user)->orderByDesc('id')->get();
        return \Inertia\Inertia::render('Sales/TransportCompanies/Index', [
            'transportCompanies' => $transportCompanies
        ]);
    }

    public function create()
    {
        return $this->index();
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        TransportCompany::create([
            'name' => $request->name,
            'cid' => Auth::user()->cid,
        ]);

        return redirect()->route('transport_companies.index')->with('success', 'Transport Company created successfully.');
    }

    public function edit($id)
    {
        $user = Auth::user()->cid;
        $transportCompanies = TransportCompany::where('cid', $user)->orderByDesc('id')->get();
        $editingCompany = TransportCompany::where('cid', $user)->findOrFail($id);
        return \Inertia\Inertia::render('Sales/TransportCompanies/Index', [
            'transportCompanies' => $transportCompanies,
            'editingCompany' => $editingCompany,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $user = Auth::user()->cid;
        $transportCompany = TransportCompany::where('cid', $user)->findOrFail($id);
        $transportCompany->update(['name' => $request->name]);

        return redirect()->route('transport_companies.index')->with('success', 'Transport Company updated successfully.');
    }

    public function destroy($id)
    {
        $user = Auth::user()->cid;
        $transportCompany = TransportCompany::where('cid', $user)->findOrFail($id);
        $transportCompany->delete();

        return redirect()->route('transport_companies.index')->with('success', 'Transport Company deleted successfully.');
    }
}
