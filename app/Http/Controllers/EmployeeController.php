<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\DepartmentSection;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('departments')->get();
        return view('employee.list',get_defined_vars());
    }
    public function create()
{
    $departments = DepartmentSection::all(); // Fetch all departments
    return view('employee.create', get_defined_vars()); // Pass departments to the view
}
    public function create1()
    {
        $departments = DepartmentSection::all();
        return view('employee.create1',get_defined_vars());
    }
    public function store(Request $request)
    {
        // Apply validation rules
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'number' => 'required|numeric',
            'address' => 'required|string|max:255',
            'blood_group' => 'nullable|string|max:3', // Adjust max length for blood group if necessary
            'salary' => 'required|numeric|min:0',
            'shift_time1' => 'required',
            'shift_time2' => 'required',
            'registered' => 'required|in:official,unofficial',
        ]);

        // Store employee in the database
        Employee::create($validatedData);

        return redirect()->route('employee.list')->with('success', 'Employee created successfully.');
    }

    public function edit($id)
{
    $employee = Employee::findOrFail($id); // Fetch the employee to edit
    $departments = DepartmentSection::all(); // Get all departments for the dropdown
    $selectedDepartment = $employee->department_id; // The department the employee belongs to
    return view('employee.edit', compact('employee', 'departments', 'selectedDepartment')); // Pass data to view
}

public function update(Request $request, $id)
{
    // Validate the request input
    $request->validate([
        'name' => 'required|string|max:255',
        'department_id' => 'required|exists:departments,id', // Ensure department exists
        'number' => 'required|string|max:255',
        'address' => 'nullable|string|max:255',
        'blood_group' => 'nullable|string|max:10',
        'salary' => 'nullable|numeric',
        'shift_time1' => 'nullable|string|max:255',
        'shift_time2' => 'nullable|string|max:255',
    ]);

    try {
        // Fetch the employee by ID
        $employee = Employee::findOrFail($id);

        // Update employee details
        $employee->name = $request->input('name');
        $employee->department_id = $request->input('department_id'); // Update department
        $employee->number = $request->input('number');
        $employee->address = $request->input('address');
        $employee->blood_group = $request->input('blood_group');
        $employee->salary = $request->input('salary');
        $employee->shift_time1 = $request->input('shift_time1');
        $employee->shift_time2 = $request->input('shift_time2');
        // Save the updated employee
        $employee->save();

    } catch (\Exception $e) {
        return redirect()->back()->withErrors(['error' => $e->getMessage()]); // Return back with the error message
    }

    // Redirect with success message
    return redirect()->route('employee.list')->with('success', 'Employee updated successfully');
}



    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return redirect()->route('employee.list')->with('success', 'Employee deleted successfully');
    }
}
