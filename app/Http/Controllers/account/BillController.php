<?php

namespace App\Http\Controllers\account;

use App\Models\Bill;
use App\Models\Party;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BillController extends Controller
{
    public function index()
    {
        $bills = Bill::all();
        return view('accounts.bill.list',get_defined_vars());
    }
    public function create()
    {
        $parties = Party::all();
        return view('accounts.bill.create',get_defined_vars());
    }
    public function store(Request $request)
    {
        Bill::create([
            'party_name' => $request->party_name,
            'gatepass_outno' => $request->gatepass_outno,
            'product_name' => $request->product_name,
            'rate' => $request->rate,
            'quantity' => $request->quantity,
            'amount' => $request->amount,
            'description' => $request->description,
           ]);


            return redirect()->back()->with('success', 'Employee created successfully.');
    }
}
