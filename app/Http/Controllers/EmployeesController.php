<?php

namespace App\Http\Controllers;

use App\Models\Employees;
use App\Models\DepartmentSection;
use App\Models\Designation;
use App\Models\ExtraTime;
use App\Models\ErpParam;
use App\Models\AccountMaster;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EmployeesController extends Controller
{
    public function index()
    {
        $departments = DepartmentSection::all();
        $designations = Designation::all();
        $extratimes = ExtraTime::all();
        return view('employees.list', get_defined_vars());
    }
    
    public function getRate($id)
{
    $extraTime = ExtraTime::find($id);
    
    if (!$extraTime) {
        return response()->json([
            'success' => false,
            'message' => 'Extra time not found'
        ], 404);
    }
    
    return response()->json([
        'success' => true,
        'rate' => $extraTime->rate,
        'name' => $extraTime->name // Optional: return name for verification
    ]);
}
    
public function store(Request $request)
{
    $validatedData = $request->validate([
        'fname' => 'required|string|max:255',
        'lname' => 'required|string|max:255',
        'phone_no' => 'required|string|max:20',
        'blood_group' => 'nullable|string|max:10',
        'address' => 'required|string|max:500',
        'employee' => 'required|in:offcial,unoffcial',
        'cnic_no' => 'required|numeric|digits:13',
        'fileFront' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        'fileBack' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        'joining_date' => 'required|date',
        'bonus_id' => 'required|exists:extra_times,id',
            'rate' => 'required|numeric',
    ]);

    try {
        $timestamp = time();
    $extraTime = ExtraTime::findOrFail($request->bonus_id);
        $cnicFrontPath = null;
        if ($request->hasFile('fileFront')) {
            $file = $request->file('fileFront');
            $fileName = $timestamp.'_front.'.$file->getClientOriginalExtension();
            $file->move(public_path('storage/uploads'), $fileName);
            $cnicFrontPath = 'uploads/'.$fileName;
        }

        $cnicBackPath = null;
        if ($request->hasFile('fileBack')) {
            $file = $request->file('fileBack');
            $fileName = $timestamp.'_back.'.$file->getClientOriginalExtension();
            $file->move(public_path('storage/uploads'), $fileName);
            $cnicBackPath = 'uploads/'.$fileName;
        }

        $erpParam = ErpParam::first(); 
        $employeeLevelId = $erpParam?->employee_level;
        $employeeAdvanceId = $erpParam?->employee_advance;

        $joiningDate = Carbon::parse($validatedData['joining_date']);
        $oneYearAgo = Carbon::now()->subYear();


        // Always create regular account (Employees Level)
        $regularAccount = AccountMaster::create([
            'title' => $validatedData['fname'] . ' - ' . substr($validatedData['cnic_no'], -4),
            'opening_date' => Carbon::now()->format('Y-m-d'),
            'level2_id' => $employeeLevelId,
            'account_code' => '0000',
        ]);

        $advanceAccount = null;
        if ($joiningDate->lessThanOrEqualTo($oneYearAgo)) {
            // Also create advance account
            $advanceAccount = AccountMaster::create([
                'title' => $validatedData['fname'] . '_Advance - ' . substr($validatedData['cnic_no'], -4),
                'opening_date' => Carbon::now()->format('Y-m-d'),
                'level2_id' => $employeeAdvanceId,
                'account_code' => '0000',
            ]);
        }

        $employee = Employees::create([
            'fname' => $validatedData['fname'] . ' - ' . substr($validatedData['cnic_no'], -4),
            'lname' => $validatedData['lname'],
            'phone_no' => $validatedData['phone_no'],
            'blood_group' => $validatedData['blood_group'] ?? null,
            'address' => $validatedData['address'],
            'employee' => $validatedData['employee'],
            'cnic_no' => $validatedData['cnic_no'],
            'joining_date' => $validatedData['joining_date'],
            'bonus_id' => $validatedData['bonus_id'],
            'bonus_title' => $extraTime->name,
            'bonus_rate' => $validatedData['rate'],
            'cnic_front_path' => $cnicFrontPath,
            'cnic_back_path' => $cnicBackPath,
            'cad' => $regularAccount->id,
            'advance_cad' => $advanceAccount ? $advanceAccount->id : null,
        ]);

        $regularAccount->update([
            'account_code' => str_pad($employee->id, 4, '0', STR_PAD_LEFT),
        ]);
        if ($advanceAccount) {
            $advanceAccount->update([
                'account_code' => 'ADV' . str_pad($employee->id, 4, '0', STR_PAD_LEFT),
            ]);
        }

        return redirect()->route('employees.reports')->with('success', 'Employee registered successfully!');

    } catch (\Exception $e) {
        if (isset($cnicFrontPath)) {
            @unlink(public_path('storage/'.$cnicFrontPath));
        }
        if (isset($cnicBackPath)) {
            @unlink(public_path('storage/'.$cnicBackPath));
        }

        return back()->withInput()->with('error', 'Error registering employee: '.$e->getMessage());
    }
}


public function update(Request $request, $id)
{
    $validatedData = $request->validate([
        'fname' => 'required|string|max:255',
        'lname' => 'required|string|max:255',
        'phone_no' => 'required|string|max:20',
        'blood_group' => 'nullable|string|max:10',
        'address' => 'required|string|max:500',
        'employee' => 'required|in:offcial,unoffcial',
        'cnic_no' => 'required|numeric|digits:13',
        'fileFront' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'fileBack' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
         'joining_date' => 'required|date',
         
         'bonus_id' => 'required|exists:extra_times,id',
            'rate' => 'required|numeric',
    ]);

    try {
        $employee = Employees::findOrFail($id);
        $account = AccountMaster::findOrFail($employee->cad);
        $timestamp = time();
  $extraTime = ExtraTime::findOrFail($request->bonus_id);
  
        if ($request->hasFile('fileFront')) {
            if ($employee->cnic_front_path) {
                @unlink(public_path('storage/'.$employee->cnic_front_path));
            }
            $file = $request->file('fileFront');
            $fileName = $timestamp.'_front.'.$file->getClientOriginalExtension();
            $file->move(public_path('storage/uploads'), $fileName);
            $employee->cnic_front_path = 'uploads/'.$fileName;
        }

        if ($request->hasFile('fileBack')) {
            if ($employee->cnic_back_path) {
                @unlink(public_path('storage/'.$employee->cnic_back_path));
            }
            $file = $request->file('fileBack');
            $fileName = $timestamp.'_back.'.$file->getClientOriginalExtension();
            $file->move(public_path('storage/uploads'), $fileName);
            $employee->cnic_back_path = 'uploads/'.$fileName;
        }

        $employee->update([
            'fname' => $validatedData['fname'],
            'lname' => $validatedData['lname'],
            'phone_no' => $validatedData['phone_no'],
            'blood_group' => $validatedData['blood_group'] ?? null,
            'address' => $validatedData['address'],
            'employee' => $validatedData['employee'],
            'joining_date' => $validatedData['joining_date'],
            'cnic_no' => $validatedData['cnic_no'],
            
             'bonus_id' => $validatedData['bonus_id'], 
             'bonus_title' => $extraTime->name, 
                'bonus_rate' => $validatedData['rate'],
        ]);


        $erpParam = ErpParam::first();
        $employeeLevelId = $erpParam?->employee_level;
        $employeeAdvanceId = $erpParam?->employee_advance;

        $joiningDate = Carbon::parse($employee->joining_date); // use stored value
        $oneYearAgo = Carbon::now()->subYear();

        // Always update the regular account (employee level)
        $account->update([
            'title' => $validatedData['fname'],
            'level2_id' => $employeeLevelId,
        ]);

        // If joining date is over a year ago, ensure both accounts exist and are linked
        if ($joiningDate->lessThanOrEqualTo($oneYearAgo)) {
            // Find or create the advance account
            $advanceTitle = $employee->fname . '_Advance';
            $advanceAccount = AccountMaster::where('title', $advanceTitle)
                ->where('level2_id', $employeeAdvanceId)
                ->first();
            if (!$advanceAccount) {
                $advanceAccount = AccountMaster::create([
                    'title' => $advanceTitle,
                    'opening_date' => Carbon::now()->format('Y-m-d'),
                    'level2_id' => $employeeAdvanceId,
                    'account_code' => 'ADV' . str_pad($employee->id, 4, '0', STR_PAD_LEFT),
                ]);
            }
            // Link both account IDs to employee
            $employee->cad = $account->id;
            $employee->advance_cad = $advanceAccount->id;
            $employee->save();
        } else {
            // If not over a year, ensure advance_cad is null and only cad is set
            $employee->cad = $account->id;
            $employee->advance_cad = null;
            $employee->save();
        }

        return redirect()->route('employees.reports')->with('success', 'Employee updated successfully!');

    } catch (\Exception $e) {
        return back()->withInput()->with('error', 'Error updating employee: '.$e->getMessage());
    }
}



public function reports(Request $request)
{
    $query = Employees::query()
        ->leftJoin('department_count', 'employees.id', '=', 'department_count.id')
        ->select('employees.*', 'department_count.department_no');
    
    // Get search parameters
    $employeeId = $request->input('employee');
    $fname = $request->input('fname');
    $cnic_no = $request->input('cnic_no');
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    
    // Apply date range filter (assuming you have a 'created_at' or similar date column)
    if ($startDate && $endDate) {
        $query->whereBetween('employees.created_at', [$startDate, $endDate]);
    }

    // Apply employee ID filter
    if ($employeeId) {
        $query->where('employees.id', $employeeId);
    }
    
    // Apply name filter (case-insensitive partial match)
    if ($fname) {
        $query->where('employees.fname', 'like', '%' . $fname . '%');
    }
    
    // Apply CNIC filter (exact match)
    if ($cnic_no) {
        // Remove any non-numeric characters first
        $cleanCnic = preg_replace('/[^0-9]/', '', $cnic_no);
        $query->where('employees.cnic_no', $cleanCnic);
    }
    
    $employees = $query->get();
    
    return view('employees.index', compact('employees', 'employeeId', 'fname', 'cnic_no', 'startDate', 'endDate'));
}

    public function destroy($id)
{
    try {
        // Find the employee by ID
        $employee = Employees::findOrFail($id);

        // Get the file paths before deleting the record
        $cnicFrontPath = $employee->cnic_front_path;
        $cnicBackPath = $employee->cnic_back_path;

        // Delete associated AccountMaster
        if ($employee->cad) {
            $account = AccountMaster::find($employee->cad);
            if ($account) {
                $account->delete();
            }
        }

        // Delete the employee record
        $employee->delete();

        // Delete the associated files if they exist
        if ($cnicFrontPath && file_exists(public_path('storage/'.$cnicFrontPath))) {
            unlink(public_path('storage/'.$cnicFrontPath));
        }

        if ($cnicBackPath && file_exists(public_path('storage/'.$cnicBackPath))) {
            unlink(public_path('storage/'.$cnicBackPath));
        }

        session()->flash('success', 'Employee deleted successfully!');
return redirect()->route('employees.index');


    } catch (\Exception $e) {
        return back()->with('error', 'Error deleting employee: '.$e->getMessage());
    }
}


public function edit($id)
{
    $employee = Employees::findOrFail($id);
       $extratimes = ExtraTime::all();
    return view('employees.edit', compact('employee','extratimes'));
}


}