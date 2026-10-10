<?php

namespace App\Http\Controllers;

use App\Models\Employees;
use App\Models\DepartmentSection;
use App\Models\Designation;
use App\Models\ExtraTime;
use App\Models\AttendenceForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class AttendenceFormController extends Controller
{
    public function index()
    {
        $employees = DB::table('employees')
    ->join('employee_types', 'employees.id', '=', 'employee_types.NTN_no')
    ->where('employee_types.salary_type', 'salary')
    ->select('employees.id', 'employees.fname', 'employees.NTN_no')
    ->get();

        return view('attendence_form.list', get_defined_vars());
    }
    
   public function reports()
{
   $attendances = AttendenceForm::with(['employeeType' => function($query) {
        $query->where('salary_type', 'salary');
    }])
    ->get();
    return view('attendence_form.index', compact('attendances'));
}

public function checkAttendanceStatus(Request $request)
{
    $employeeId = $request->input('employee_id');
    $date = $request->input('date');
    
    $attendance = AttendenceForm::where('employee_id', $employeeId)
                              ->whereDate('employee_date', $date)
                              ->first();
    
    if ($attendance) {
        return response()->json([
            'hasTimeIn' => true,
            'hasBoth' => $attendance->employee_time_out !== null,
            'time_in' => $attendance->employee_time,
            'time_out' => $attendance->employee_time_out
        ]);
    }
    
    return response()->json([
        'hasTimeIn' => false,
        'hasBoth' => false
    ]);
}

   public function store(Request $request)
{
    $request->validate([
  
        'employee_date' => 'required|date',
    ]);

    // Get the employee details
    $employee = Employees::findOrFail($request->NTN_no);
    $today = date('Y-m-d');

    // Check if the action is Time In or Time Out
    $action = $request->input('action');

    if ($action === 'time_in') {
        $request->validate([
            'employee_time' => 'required',
        ]);

        // Check if attendance already exists for today
        $existingAttendance = AttendenceForm::where('employee_id', $employee->id)
                                         ->whereDate('employee_date', $request->employee_date)
                                         ->first();

        if ($existingAttendance) {
            return redirect()->back()
                           ->with('error', 'Attendance already recorded for today. Please use Time Out instead.');
        }

        // Create new attendance record
        AttendenceForm::create([
            'employee_id' => $employee->id,
            'fname' => $employee->fname,
            'NTN' => $employee->NTN_no,
            'employee_date' => $request->employee_date,
            'employee_time' => $request->employee_time,
            // employee_time_out will be null initially
        ]);

        return redirect()->route('attendence_form.reports')
                       ->with('success', 'Time In recorded successfully for ' . $employee->fname);

    } elseif ($action === 'time_out') {
        $request->validate([
            'employee_time_out' => 'required',
        ]);

        // Find existing attendance record
        $attendance = AttendenceForm::where('employee_id', $employee->id)
                                  ->whereDate('employee_date', $request->employee_date)
                                  ->first();

        if (!$attendance) {
            return redirect()->back()
                           ->with('error', 'No Time In record found for today. Please Time In first.');
        }

        // Update only the time out
        $attendance->update([
            'employee_time_out' => $request->employee_time_out
        ]);

        return redirect()->route('attendence_form.reports')
                       ->with('success', 'Time Out recorded successfully for ' . $employee->fname);
    }

    return redirect()->back()
                   ->with('error', 'Invalid action');
}

public function edit($id)
{
    $attendance = AttendenceForm::findOrFail($id);
    $employees = Employees::all(); // in case you want to allow changing employee
    return view('attendence_form.edit', compact('attendance', 'employees'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'employee_date' => 'required|date',
        'employee_time' => 'nullable',
        'employee_time_out' => 'nullable',
    ]);

    $attendance = AttendenceForm::findOrFail($id);

    $attendance->update([
        'employee_date' => $request->employee_date,
        'employee_time' => $request->employee_time,
        'employee_time_out' => $request->employee_time_out,
    ]);

    return redirect()->route('attendence_form.reports')
                   ->with('success', 'Attendance record updated successfully.');
}

public function destroy($id)
{
    $attendance = AttendenceForm::findOrFail($id);
    $attendance->delete();

    return redirect()->route('attendence_form.reports')
                   ->with('success', 'Attendance record deleted successfully.');
}




}