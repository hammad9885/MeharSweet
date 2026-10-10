<?php

namespace App\Http\Controllers\account;

use App\Models\Level2;
use App\Models\Level3;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Level3Controller extends Controller
{
    public function index()
    {
        $level3s = Level3::with('level2s')->get();
        return view('accounts.level3.list',get_defined_vars());
    }
    public function create()
    {
        $level2s = Level2::all();
        return view('accounts.level3.create',get_defined_vars());
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

       Level3::create([
        'title' => $request->title,
        'level2_id' => $request->level2_id,
       ]);

        return redirect()->route('level3.list')->with('success', 'Level3 created successfully.');
    }
}
