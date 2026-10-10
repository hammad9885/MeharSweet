<?php

namespace App\Http\Controllers\account;

use App\Models\Loan;
use App\Models\Party;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoanController extends Controller
{
    public function index()
    {
        $loans = Loan::all();
        return view('accounts.loan.list',get_defined_vars());
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

       Loan::create([
        'name' => $request->name,
        'description' => $request->description,
       ]);


        return redirect()->back()->with('success', 'Employee created successfully.');
    }
    public function create()
    {
        $employees = Employee::all();
        return view('accounts.loan.create',get_defined_vars());
    }
}
