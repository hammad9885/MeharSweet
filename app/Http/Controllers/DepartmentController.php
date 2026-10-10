<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::all();
        return view('department.list',get_defined_vars());
    }
    public function create()
    {
        return view('department.create');
    }
   public function store(Request $request)
{
    // Validate the 'name' field
    $request->validate([
        'name' => 'required|string|max:255',
    ]);

    try {
        // Create a new department
        Department::create([
            'name' => $request->name,
        ]);

        // Redirect with a success message
        return redirect()->route('department.list')->with('success', 'Department created successfully.');
    } catch (\Exception $e) {
        // Dump the error message and stop execution if there's an exception
        dd($e->getMessage());
    }
}

    public function edit($id)
    {
        $department = Department::findOrFail($id);
        return view('department.edit', compact('department'));
    }
   public function update(Request $request, $id)
{
    // Validate the 'name' field
    $request->validate([
        'name' => 'required|string|max:255',
    ]);

    try {
        // Find the department or fail if not found
        $department = Department::findOrFail($id);
        $department->name = $request->input('name');
        $department->save();
    } catch (\Exception $e) {
        // Dump the error message and stop execution if there's an exception
        dd($e->getMessage());
    }

    // Redirect with a success message
    return redirect()->route('department.list')->with('success', 'Department updated successfully');
}


    public function destroy($id)
    {
        $department = Department::findOrFail($id);
        $department->delete();

        return redirect()->route('department.list')->with('success', 'Department deleted successfully');
    }

}
