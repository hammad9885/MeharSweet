<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\User;

class CompanyRegController extends Controller
{
    /**
     * Show company registration page.
     */
    public function index()
    {
        $companies = Company::with('users')->get(); // eager load users
        $users = User::whereNull('cid')->get(); // only unassigned users

        return view('company_reg.index', compact('companies', 'users'));
    }

    /**
     * Store a new company (workspace).
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Find max cid already in use across companies
        $maxCid = Company::max('cid');
        $newCid = $maxCid ? $maxCid + 1 : 1;

        $workspace = Company::create([
            'name' => $request->name,
            'cid'  => $newCid,
        ]);

        return redirect()->route('company_reg.index')
            ->with('success', 'Company created successfully!');
        }

    /**
     * Assign users to a workspace.
     */
    public function assignUsers(Request $request, $workspaceId)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $workspace = Company::findOrFail($workspaceId);

        User::whereIn('id', $request->user_ids)->update(['cid' => $workspace->cid]);

        return redirect()->route('company_reg.index')
            ->with('success', 'Users assigned successfully!');
    }
}
