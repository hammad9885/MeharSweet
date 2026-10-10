<?php

namespace App\Http\Controllers\account;

use App\Models\Party;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PartyController extends Controller
{
    public function index()
    {
        $parties = Party::all();
        return view('accounts.party.list',get_defined_vars());
    }
    public function create()
    {
        return view('accounts.party.create');
    }
    public function store(Request $request)
    {
        // return $request;
        // $validatedData = $request->validate([
        //     'name' => 'required|string|max:255',
        //     'department' => 'nullable|string|max:255',
        //     'number' => 'required|string|max:15',
        //     'address' => 'nullable|string|max:255',
        //     'blood_group' => 'nullable|string|max:10',
        //     'salary' => 'nullable|numeric',
        //     'shift_time' => 'nullable|string|max:255',
        //     'registered' => 'required|in:official,unofficial',
        // ]);

       Party::create([
        'name' => $request->name,
        'balance' => $request->balance,
        'registered' => $request->registered,
       ]);


        return redirect()->back()->with('success', 'Employee created successfully.');
    }
}
