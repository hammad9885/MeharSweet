<?php

namespace App\Http\Controllers\account;

use App\Models\Group;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::all();
        return view('accounts.groups.list',get_defined_vars());
    }
    public function create()
    {
        return view('accounts.groups.create');
    }
    public function store(Request $request)
{
    // Validate the request data
    $request->validate([
        'title' => 'required|string|max:255',  // Title must be required, a string, and should not exceed 255 characters
    ]);

    // Create the group if validation passes
    Group::create([
        'title' => $request->title,
    ]);

    return redirect()->route('group.list')->with('success', 'Group created successfully.');
}

}
