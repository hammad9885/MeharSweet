<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\{
    DepartmentSection,
    Designation,
    Employee,
    Attendance
};

class SalaryCalculatorController extends Controller
{
    /**
     * Display the salary calculation page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $loggedInUser = Auth::user();
        
        // Get necessary data for salary calculation
        $departmentSections = DepartmentSection::where('id', '!=', 13)->get();
        $designations = Designation::all();
        $employees = Employee::all();
        
        return view('salary_calc.list', compact(
            'loggedInUser',
            'departmentSections',
            'designations',
            'employees'
        ));
    }
    
    // SalaryCalculatorController.php

public function getSalaryData(Request $request)
{
    $request->validate([
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'department_id' => 'required|exists:departmentsections,id',
        'designation_id' => 'required|exists:designations,id',
        'employee_id' => 'required|exists:employees,id',
    ]);

    $data = DB::table('salary_calc')
        ->where('department_id', $request->department_id)
        ->where('designation_id', $request->designation_id)
        ->where('employee_id', $request->employee_id)
        ->whereBetween('employee_date', [$request->start_date, $request->end_date])
        ->select(
            'employee_date as date',
            'department_name as department',
            'designation_name as designation',
            'employee_name as employee',
            'salary_amount',
            'salary_cal',
            'shift_from',
            'shift_to',
            'break_from',
            'break_to',
            'bonus_title',
            'bonus_rate',
            'employee_time as time_in',
            'employee_time_out as time_out'
        )
        ->get();

    return response()->json($data);
}
    
}