<?php

namespace App\Http\Controllers;

use App\Models\EmployeeType;
use App\Models\Employees;
use App\Models\DepartmentSection;
use App\Models\Designation;
use Illuminate\Http\Request;

class EmployeeTypeController extends Controller
{
    public function index()
    {
        $employees = Employees::all();
        $departments = DepartmentSection::all();
        $designations = Designation::all();
        return view('employee_type.list', get_defined_vars());
    }
    
    
    public function getEmployeeDetails($id)
{
    $employee = Employees::find($id);
    return response()->json([
        'fname' => $employee->fname
    ]);
}

 public function store(Request $request)
{
    // Define base validation rules
    $rules = [
        'cnic_no' => 'required|exists:employees,id',
        'department_id' => 'required|exists:departmentsections,id',
        'designation_id' => 'required|exists:designations,id',
        'salary_type' => 'required|in:salary,waje',
        'salary_cal' => 'required|in:monthly,weekly,daily',
    ];

    // Conditional validation based on salary type and department
    if ($request->salary_type === 'salary') {
        $rules += [
            'salary_amount' => 'required|numeric',
            'shift_from' => 'required|date_format:H:i',
            'shift_to' => 'required|date_format:H:i|after:shift_from',
            'break_from' => 'required|date_format:H:i',
            'break_to' => 'required|date_format:H:i|after:break_from',
        ];
    } elseif ($request->salary_type === 'waje') {
        $departmentId = $request->department_id;
        
        if (in_array($departmentId, ['23', '25', '26', '29', '22', '28', '31'])) {
            $rules += [
                'basic_salary' => 'required|numeric',
                'fix_impression_day' => 'required|numeric',
                'lam_working_days' => 'required|numeric',
                'over_time' => 'required|numeric',
            ];
        } elseif ($departmentId === '14') {
    $rules += [
        'process_name' => 'required|array',
        'process_name.*' => 'required|string',
        'process_rate' => 'required|array',
        'process_rate.*' => 'required|string',
    ];
} elseif ($departmentId === '21') {
            $rules += [
                'per_sheet_rate' => 'required|numeric',
            ];
        }
    }

    $validatedData = $request->validate($rules);

    try {
        // Create employee with only validated data
        $employeeData = [
            'cnic_no' => $validatedData['cnic_no'],
            'department_id' => $validatedData['department_id'],
            'designation_id' => $validatedData['designation_id'],
            'salary_type' => $validatedData['salary_type'],
            'salary_cal' => $validatedData['salary_cal'],
        ];

        // Add conditional fields
        if ($validatedData['salary_type'] === 'salary') {
            $employeeData += [
                'salary_amount' => $validatedData['salary_amount'],
                'shift_from' => $validatedData['shift_from'],
                'shift_to' => $validatedData['shift_to'],
                'break_from' => $validatedData['break_from'],
                'break_to' => $validatedData['break_to'],
            ];
        } else {
            if (isset($validatedData['basic_salary'])) {
                $employeeData += [
                    'basic_salary' => $validatedData['basic_salary'],
                    'fix_impression_day' => $validatedData['fix_impression_day'],
                    'lam_working_days' => $validatedData['lam_working_days'],
                    'over_time' => $validatedData['over_time'],
                ];
            }
            
           if (isset($validatedData['process_name'])) {
    // Convert process_rate values to floats before JSON encoding
    $processRates = array_map('floatval', $validatedData['process_rate']);
    
    $employeeData += [
        'process_name' => json_encode($validatedData['process_name']),
        'process_rate' => json_encode($processRates), // Now stored as [100.50, 200.00]
    ];
}
            
            if (isset($validatedData['per_sheet_rate'])) {
                $employeeData['per_sheet_rate'] = $validatedData['per_sheet_rate'];
            }
        }

        EmployeeType::create($employeeData);

        return redirect()->route('employee_type.reports')->with('success', 'Employee registered successfully!');

    } catch (\Exception $e) {
        return back()->withInput()->with('error', 'Error registering employee: '.$e->getMessage());
    }
}

public function reports(Request $request)
{
    // Initialize the query for employee_types (for filtering)
    $query = EmployeeType::query()->with('employee');

    // Get UNFILTERED data for dropdowns
    $dropdownEmployees = EmployeeType::with('employee')->get();
     $departments = DepartmentSection::orderBy('name')->get();
    $designations = Designation::all();
    $salaryTypes = EmployeeType::select('salary_type')->distinct()->get(); // For the dropdown

    // Get filter inputs
    $employee = $request->input('employee');
    $salary_type = $request->input('salary_type'); // Changed to singular to match view
    $fname = $request->input('fname');
    $cnic_no = $request->input('cnic_no');
    $department_id = $request->input('department_id');
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    // Apply filters
    if ($startDate && $endDate) {
        $query->whereBetween('created_at', [$startDate, $endDate]);
    }
    
    if ($salary_type) {
        $query->where('salary_type', $salary_type); // Assuming salary_type is a column
    }
    
    if ($employee) {
        $query->whereHas('employee', function($q) use ($employee) {
            $q->where('id', $employee);
        });
    }
    
    
    if ($department_id) {
    $query->whereHas('employee', function($q) use ($department_id) {
        $q->where('department_id', $department_id);
    });
}
    
    if ($fname) {
        $query->whereHas('employee', function($q) use ($fname) {
            $q->where('fname', 'like', '%' . $fname . '%');
        });
    }
    
    if ($cnic_no) {
        $query->whereHas('employee', function($q) use ($cnic_no) {
            $q->where('cnic_no', 'like', '%' . $cnic_no . '%');
        });
    }

    // Get filtered results
    $employeeTypes = $query->get();

    return view('employee_type.index', compact(
        'employeeTypes',
        'department_id',
        'dropdownEmployees',
        'departments',
        'designations',
        'salaryTypes' // Added for the dropdown
    ));
}
    
   public function destroy($id)
{
    try {
        // Find the employee type record
        $employeeType = EmployeeType::findOrFail($id);
        
        
        // Delete the record
        $employeeType->delete();
        
        return redirect()->route('employee_type.reports')->with('success', 'Employee type deleted successfully!');
        
    } catch (\Exception $e) {
        return back()->with('error', 'Error deleting employee type: '.$e->getMessage());
    }}

public function edit($id)
{
    $employeeType = EmployeeType::findOrFail($id); // The record being edited
    $employees = Employees::all(); // For the CNIC dropdown
    $departments = DepartmentSection::all();
    $designations = Designation::all();
    
    return view('employee_type.edit', compact('employeeType', 'employees', 'departments', 'designations'));

}

public function update(Request $request, $id)
{
    // Find the employee record to update
    $employeeType = EmployeeType::findOrFail($id);

    // Define base validation rules
    $rules = [
        'cnic_no' => 'required|exists:employees,id',
        'department_id' => 'required|exists:departmentsections,id',
        'designation_id' => 'required|exists:designations,id',
        'salary_type' => 'required|in:salary,waje',
         'salary_cal' => 'required|in:monthly,weekly,daily',
    ];

    // Conditional validation based on salary type and department
    if ($request->salary_type === 'salary') {
        $rules += [
            'salary_amount' => 'required|numeric',
            'shift_from' => 'required|date_format:H:i',
            'shift_to' => 'required|date_format:H:i|after:shift_from',
            'break_from' => 'required|date_format:H:i',
            'break_to' => 'required|date_format:H:i|after:break_from',
        ];
    } elseif ($request->salary_type === 'waje') {
        $departmentId = $request->department_id;
        
        if (in_array($departmentId, ['23', '25', '26', '29', '22', '28', '31'])) {
            $rules += [
                'basic_salary' => 'required|numeric',
                'fix_impression_day' => 'required|numeric',
                'lam_working_days' => 'required|numeric',
                'over_time' => 'required|numeric',
            ];
        } elseif ($departmentId === '14') {
            $rules += [
                'process_name' => 'required|array|min:1',
                'process_name.*' => 'required|string',
                'process_rate' => 'required|array|min:1',
                'process_rate.*' => 'required|numeric',
            ];
        } elseif ($departmentId === '21') {
            $rules += [
                'per_sheet_rate' => 'required|numeric',
            ];
        }
    }

    $validatedData = $request->validate($rules);

    try {
        // Update employee with only validated data
        $employeeData = [
            'cnic_no' => $validatedData['cnic_no'],
            'department_id' => $validatedData['department_id'],
            'designation_id' => $validatedData['designation_id'],
            'salary_type' => $validatedData['salary_type'],
            'salary_cal' => $validatedData['salary_cal'],
        ];

        // Clear all conditional fields first
        $fieldsToClear = [
            'salary_amount', 'shift_from', 'shift_to', 'break_from', 'break_to',
            'basic_salary', 'fix_impression_day', 'lam_working_days', 'over_time',
            'process_name', 'process_rate', 'per_sheet_rate'
        ];
        
        foreach ($fieldsToClear as $field) {
            $employeeType->$field = null;
        }

        // Add conditional fields based on salary type
        if ($validatedData['salary_type'] === 'salary') {
            $employeeData += [
                'salary_amount' => $validatedData['salary_amount'],
                'shift_from' => $validatedData['shift_from'],
                'shift_to' => $validatedData['shift_to'],
                'break_from' => $validatedData['break_from'],
                'break_to' => $validatedData['break_to'],
            ];
        } else {
            if (isset($validatedData['basic_salary'])) {
                $employeeData += [
                    'basic_salary' => $validatedData['basic_salary'],
                    'fix_impression_day' => $validatedData['fix_impression_day'],
                    'lam_working_days' => $validatedData['lam_working_days'],
                    'over_time' => $validatedData['over_time'],
                ];
            }
            
            if (isset($validatedData['process_name'])) {
                // Ensure both arrays have the same count
                $processCount = min(
                    count($validatedData['process_name']),
                    count($validatedData['process_rate'])
                );
                
                $processNames = [];
                $processRates = [];
                
                for ($i = 0; $i < $processCount; $i++) {
                    if (!empty($validatedData['process_name'][$i])) {
                        $processNames[] = $validatedData['process_name'][$i];
                        $processRates[] = $validatedData['process_rate'][$i];
                    }
                }
                
                $employeeData += [
                    'process_name' => json_encode($processNames),
                    'process_rate' => json_encode($processRates),
                ];
            }
            
            if (isset($validatedData['per_sheet_rate'])) {
                $employeeData['per_sheet_rate'] = $validatedData['per_sheet_rate'];
            }
        }

        // Update the employee record
        $employeeType->update($employeeData);

        return redirect()->route('employee_type.reports')->with('success', 'Employee updated successfully!');

    } catch (\Exception $e) {
        return back()->withInput()->with('error', 'Error updating employee: '.$e->getMessage());
    }
}

}




