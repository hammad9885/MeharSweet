<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserManageController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $cid = Auth::user()->cid;
        $users = User::where('cid', $cid)->get();

        return \Inertia\Inertia::render('Users/Index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = new User();
        $user->name     = $request->name;
        $user->email    = $request->email;
        $user->mobile   = $request->mobile;
        $user->password = Hash::make($request->password);

        $user->cid      = Auth::user()->cid; // Assign same company ID

        $user->save();

        return redirect()->route('userssingle.index')->with('success', 'User added successfully!');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $this->authorizeCompany($user);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $this->authorizeCompany($user);
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $this->authorizeCompany($user);

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
           
        ]);

        $user->name  = $request->name;
        $user->email = $request->email;
        $user->mobile=$request->mobile;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('userssingle.index')->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        $this->authorizeCompany($user);
        $user->delete();

        return redirect()->route('userssingle.index')->with('success', 'User deleted successfully!');
    }

    /**
     * Prevent access to users from another company
     */
    private function authorizeCompany(User $user)
    {
        if ($user->cid !== Auth::user()->cid) {
            abort(403, 'Unauthorized action.');
        }
    }
}
