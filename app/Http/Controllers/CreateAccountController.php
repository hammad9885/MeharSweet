<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Right;
use Illuminate\Http\Request;
use App\Models\AccountMaster;
use Illuminate\Support\Facades\Hash;


class CreateAccountController extends Controller
{
    public function index()
    {
        return view('create_account.list',get_defined_vars());
    }
    
   public function store(Request $request)
{
    // Pre-process the request to ensure permissions are correctly converted to tinyint (0 or 1)
    $permissions = $request->input('permissions', []);
    foreach ($permissions as $key => $permission) {
        // If 'add', 'edit', or 'del' is not provided, default to 0
        $permissions[$key]['add'] = isset($permission['add']) ? filter_var($permission['add'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0 : 0;
        $permissions[$key]['edit'] = isset($permission['edit']) ? filter_var($permission['edit'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0 : 0;
        $permissions[$key]['del'] = isset($permission['del']) ? filter_var($permission['del'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0 : 0;
    }
    $request->merge(['permissions' => $permissions]);

    // Validate the incoming request data
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8',
        'navigationOptions' => 'nullable|array',
        'navigationOptions.*' => 'nullable|string',
        'role' => 'required|string|in:admin,user',
        'permissions' => 'nullable|array',
        'permissions.*.level' => 'required|string',
        'permissions.*.add' => 'sometimes|boolean',
        'permissions.*.edit' => 'sometimes|boolean',
        'permissions.*.del' => 'sometimes|boolean',
    ]);

    // Create a new user instance
    $user = new User();
    $user->name = $validatedData['name'];
    $user->email = $validatedData['email'];
    $user->password = Hash::make($validatedData['password']);
    $user->is_admin = ($validatedData['role'] === 'admin') ? 1 : 0;

    $user->account = in_array('Account', $validatedData['navigationOptions'] ?? []) ? 1 : 0;
    $user->billing = in_array('Billing', $validatedData['navigationOptions'] ?? []) ? 1 : 0;
    $user->wage_calculator = in_array('Wage Calculator', $validatedData['navigationOptions'] ?? []) ? 1 : 0;
    $user->delivery_challan = in_array('Delivery Challen', $validatedData['navigationOptions'] ?? []) ? 1 : 0;
    $user->setup_department = in_array('Set-Up Department', $validatedData['navigationOptions'] ?? []) ? 1 : 0;
    $user->employee_department = in_array('Employee Department', $validatedData['navigationOptions'] ?? []) ? 1 : 0;
    $user->waste_sale = in_array('Waste Sale', $validatedData['navigationOptions'] ?? []) ? 1 : 0;
    $user->gate_ex = in_array('Gate Ex', $validatedData['navigationOptions'] ?? []) ? 1 : 0;
    $user->job_sheet = in_array('Job Sheet', $validatedData['navigationOptions'] ?? []) ? 1 : 0;
    $user->attendance_system = in_array('Attendance System', $validatedData['navigationOptions'] ?? []) ? 1 : 0;
    $user->gate_pass = in_array('Gate Pass', $validatedData['navigationOptions'] ?? []) ? 1 : 0;
    $user->purchase = in_array('Purchase', $validatedData['navigationOptions'] ?? []) ? 1 : 0;
    $user->inventory = in_array('Inventory', $validatedData['navigationOptions'] ?? []) ? 1 : 0;
    $user->sale = in_array('Sale', $validatedData['navigationOptions'] ?? []) ? 1 : 0;
    $user->manufacturing = in_array('Manufacturing', $validatedData['navigationOptions'] ?? []) ? 1 : 0;
    $user->product_registration = in_array('Product Registration', $validatedData['navigationOptions'] ?? []) ? 1 : 0;
    $user->setup = in_array('Set up', $validatedData['navigationOptions'] ?? []) ? 1 : 0;
    $user->employee = in_array('Employee', $validatedData['navigationOptions'] ?? []) ? 1 : 0;
    $user->report = in_array('Report', $validatedData['navigationOptions'] ?? []) ? 1 : 0;

    // Save the user data in the 'users' table

    $user->save();

    // Store permissions in the 'rights' table
    if (!empty($validatedData['permissions'])) {
        foreach ($validatedData['permissions'] as $permission) {
            if (!empty($permission['level'])) {
                $right = new Right();
                $right->user_id = $user->id;
                $right->app_name = $permission['level'];
                $right->add = $permission['add'];
                $right->edit = $permission['edit'];
                $right->del = $permission['del'];
                $right->save();
            }
        }
    } else {
        // If no permissions data is provided
         return redirect()->route('create_account.reports')->with('success', 'Account created successfully.');
    } 

    // Redirect with a success message
    return redirect()->route('create_account.reports')->with('success', 'Account created successfully.');
}



public function reports()
{
    // Retrieve all users and order them by 'is_admin' so that '1' (Admin) appears first
    $users = User::orderByDesc('is_admin')->get();

    // Return the view with the users data
    return view('create_account.index', compact('users'));
}



public function delete($id)
{
    // Check if the user is logged in and is an admin
    if (auth()->check() && auth()->user()->is_admin == 1) {
        // Find the user by ID
        $user = User::findOrFail($id);

        // Prevent the admin from deleting their own account
        if (auth()->user()->id === $user->id) {
            return redirect()->route('create_account.reports')->with('error', 'You cannot delete your own account.');
        }

        // Delete the user
        $user->delete();

        // Redirect to the list page with a success message
        return redirect()->route('create_account.reports')->with('success', 'User account deleted successfully.');
    } else {
        // If the user is not an admin or not logged in, show an error message
        return redirect()->route('create_account.reports')->with('error', 'You do not have permission to delete this account.');
    }
}

public function edit($id)
{
    // Find the user by ID
    $user = User::findOrFail($id);

    // Get user's rights/permissions
    $rights = Right::where('user_id', $id)->get()->keyBy('app_name');
    // dd($rights);
    // Pass user navigation options to the view
    $navigationOptions = [
        'Account' => $user->account,
        'Billing' => $user->billing,
        'Wage Calculator' => $user->wage_calculator,
        'Delivery Challen' => $user->delivery_challan,
        'Waste Sale' => $user->waste_sale,
        'Gate Pass' => $user->gate_pass,
        'Purchase' => $user->purchase,
        'Inventory' => $user->inventory,
        'Sale' => $user->sale,
        'Manufacturing' => $user->manufacturing,
        'Product Registration' => $user->product_registration,
        'Set up' => $user->setup,
        'Employee' => $user->employee,
        'Report' => $user->report,
        'Gate Ex' => $user->gate_ex,
        'Job Sheet' => $user->job_sheet,
        'Attendance System' => $user->attendance_system,
        'Set-Up Department' => $user->setup_department,
        'Employee Department' => $user->employee_department,
    ];

    return view('create_account.edit', compact('user', 'navigationOptions', 'rights'));
}

public function update(Request $request, $id)
{
    
    // Basic validation rules
    $validationRules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $id,
        'password' => 'nullable|string|min:8|confirmed',
        'role' => 'required|string|in:admin,user',
    ];

    $validatedData = $request->validate($validationRules);

    $user = User::findOrFail($id);

    // Update user data
    $user->name = $validatedData['name'];
    $user->email = $validatedData['email'];
    $user->is_admin = ($validatedData['role'] === 'admin') ? 1 : 0;

    // Define all navigation options and their corresponding database columns
    $navigationOptions = [
        'account' => 'account',
        'billing' => 'billing',
        'wage_calculator' => 'wage_calculator',
        'deliveryChallan' => 'delivery_challan',
        'wasteSale' => 'waste_sale',
        'gateEx' => 'gate_ex',
        'gatePass' => 'gate_pass',
        'purchase' => 'purchase',
        'inventory' => 'inventory',
        'productRegistration' => 'product_registration',
        'setup' => 'setup',
        'report' => 'report',
        'job_sheet' => 'job_sheet',
        'attendanceSystem' => 'attendance_system',
        'setupDepartment' => 'setup_department',
        'employeeDepartment' => 'employee_department',
    ];

    // Update navigation options
    foreach ($navigationOptions as $requestKey => $dbColumn) {
        $user->$dbColumn = $request->has($requestKey) ? 1 : 0;
    }

    // Update password if provided
    if ($request->filled('password')) {
        $user->password = Hash::make($validatedData['password']);
    }

    $user->save();

    // Handle permissions
    $permissions = $request->input('permissions', []);
    
    // Delete all existing permissions first
    Right::where('user_id', $id)->delete();

    // Process and save new permissions
    foreach ($permissions as $permission) {
        if (!empty($permission['level'])) {
            Right::create([
                'user_id' => $user->id,
                'app_name' => $permission['level'],
                'add' => isset($permission['add']) ? 1 : 0,
                'edit' => isset($permission['edit']) ? 1 : 0,
                'del' => isset($permission['del']) ? 1 : 0,
            ]);
        }
    }

    return redirect()->route('create_account.reports')->with('success', 'Account updated successfully.');
}


}
