<?php

namespace App\Http\Controllers;

use App\Models\AccountMaster;
use App\Models\Company;
use App\Models\ErpParam;
use App\Models\Group;
use App\Models\Level1;
use App\Models\Level2;
use App\Models\Module;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RegisterUser extends Controller
{
    public function setupAccountsForCompany($cid)
    {


        DB::beginTransaction();

   

        try {
            // Get predefined groups (they already exist in the database)
            $groups = Group::all()->keyBy('title');

            // Predefined Level1 accounts based on your existing data pattern
            $level1Accounts = [
                'Capital' => [
                    ['title' => 'Authorized Capital', 'level1_code' => '001'],
                ],
                'Liabilities' => [
                    ['title' => 'Short Term Liabilities', 'level1_code' => '001'],
                    ['title' => 'Long Term Liabilities', 'level1_code' => '002'],
                ],
                'Assets' => [
                    ['title' => 'Current Assets', 'level1_code' => '001'],
                    ['title' => 'Fixed Assets', 'level1_code' => '002'],
                ],
                'Revenue' => [
                    ['title' => 'Branch Revenue', 'level1_code' => '001'],
                ],
                'Expenses' => [
                    ['title' => 'Direct Expenses', 'level1_code' => '001'],
                    ['title' => 'In-Direct Expenses', 'level1_code' => '002'],
                ],
            ];


            $createdLevel1 = [];

            // Create Level1 accounts
            foreach ($level1Accounts as $groupName => $accounts) {
                if (isset($groups[$groupName])) {
                    $groupId = $groups[$groupName]->group_code;

                    foreach ($accounts as $account) {
                        $level1 = Level1::create([
                            'title' => $account['title'],
                            'level1_code' => $account['level1_code'],
                            'group_id' => $groupId,
                            'cid' => $cid,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $createdLevel1[$account['title']] = $level1;
                    }
                }
            }

            // Predefined Level2 accounts based on your existing data pattern
            $level2Accounts = [
                'Authorized Capital' => [
                    ['title' => 'Shared Capital', 'level2_code' => '001'],
                ],
                'Short Term Liabilities' => [
                    ['title' => 'Suppliers', 'level2_code' => '001'],
                    ['title' => 'Employees', 'level2_code' => '002'],
                ],
                'Long Term Liabilities' => [
                    ['title' => 'Long Term Loans', 'level2_code' => '001'],
                ],
                'Current Assets' => [
                    ['title' => 'Customers', 'level2_code' => '001'],
                    ['title' => 'Stocks', 'level2_code' => '002'],
                    ['title' => 'Cash Accounts', 'level2_code' => '003'],
                    ['title' => 'Bank Accounts', 'level2_code' => '004'],
                ],
                'Fixed Assets' => [
                    ['title' => 'Land and Building', 'level2_code' => '001'],
                ],
                'Branch Revenue' => [
                    ['title' => 'Revenue from Sales', 'level2_code' => '001'],
                    ['title' => 'Revenue from Other Operations', 'level2_code' => '002'],
                ],
                'In-Direct Expenses' => [
                    ['title' => 'Office Expenses', 'level2_code' => '001'],
                    ['title' => 'Utility Bills', 'level2_code' => '002'],
                    ['title' => 'Taxation', 'level2_code' => '003'],
                ],
            ];


            $createdLevel2 = [];

            // Create Level2 accounts
            foreach ($level2Accounts as $level1Title => $accounts) {
                if (isset($createdLevel1[$level1Title])) {
                    $level1Id = $createdLevel1[$level1Title]->id;

                    foreach ($accounts as $account) {
                        $level2 = Level2::create([
                            'title' => $account['title'],
                            'level2_code' => $account['level2_code'],
                            'level1_id' => $level1Id,
                            'cid' => $cid,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $createdLevel2[$account['title']] = $level2;
                    }
                }
            }

            // Create default account masters
            $accountMasters = [
                'Shared Capital' => [
                    ['title' => 'Partner 1 Capital', 'account_code' => '0001', 'opening_date' => now()->format('Y-m-d')],
                    ['title' => 'Partner 2 Capital', 'account_code' => '0002', 'opening_date' => now()->format('Y-m-d')],
                ],
                'Cash Accounts' => [
                    ['title' => 'Main Cash A/c', 'account_code' => '0001', 'opening_date' => now()->format('Y-m-d')],
                ],
                'Bank Accounts' => [
                    ['title' => 'Meezan Account # 1234', 'account_code' => '0001', 'opening_date' => now()->format('Y-m-d')],
                ],
                'Stocks' => [
                    ['title' => 'Stock A/c', 'account_code' => '0001', 'opening_date' => now()->format('Y-m-d')],
                ],
                'Revenue from Sales' => [
                    ['title' => 'Sales Account', 'account_code' => '0001', 'opening_date' => now()->format('Y-m-d')],
                ],
                'Office Expenses' => [
                    ['title' => 'Salary Expenses', 'account_code' => '0001', 'opening_date' => now()->format('Y-m-d')],
                    ['title' => 'Misc Expenses', 'account_code' => '0002', 'opening_date' => now()->format('Y-m-d')],
                    ['title' => 'Stationary Expenses', 'account_code' => '0003', 'opening_date' => now()->format('Y-m-d')],
                    ['title' => 'Entertainment Expenses', 'account_code' => '0004', 'opening_date' => now()->format('Y-m-d')],
                ],
                'Utility Bills' => [
                    ['title' => 'Electricity Bill', 'account_code' => '0001', 'opening_date' => now()->format('Y-m-d')],
                    ['title' => 'Mobile & Telephone bills', 'account_code' => '0002', 'opening_date' => now()->format('Y-m-d')],
                    ['title' => 'Gas Bills', 'account_code' => '0003', 'opening_date' => now()->format('Y-m-d')],
                    ['title' => 'Internet Bill', 'account_code' => '0004', 'opening_date' => now()->format('Y-m-d')],
                ],
            ];



            // Create Account Masters
            foreach ($accountMasters as $level2Title => $accounts) {
                if (isset($createdLevel2[$level2Title])) {
                    $level2Id = $createdLevel2[$level2Title]->id;

                    foreach ($accounts as $account) {
                        AccountMaster::create([
                            'title' => $account['title'],
                            'account_code' => $account['account_code'],
                            'level2_id' => $level2Id,
                            'opening_date' => $account['opening_date'],
                            'cid' => $cid,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
            // Create ERP Parameters using created accounts
            ErpParam::create([
                'cid' => $cid,

                // Level2 references
                'cash_level'     => $createdLevel2['Cash Accounts']->id ?? null,
                'bank_level'     => $createdLevel2['Bank Accounts']->id ?? null,
                'supplier_level' => $createdLevel2['Suppliers']->id ?? null,
                'customer_level' => $createdLevel2['Customers']->id ?? null,
                'employee_level' => $createdLevel2['Employees']->id ?? null,

                // AccountMaster references
                'capital_account'   => AccountMaster::where('title', 'Partner 1 Capital')->where('cid', $cid)->value('id'),
                'purchase_account'  => AccountMaster::where('title', 'Stock A/c')->where('cid', $cid)->value('id'),
                'sale_ac'           => AccountMaster::where('title', 'Sales Account')->where('cid', $cid)->value('id'),
                'cash_acc'          => AccountMaster::where('title', 'Main Cash A/c')->where('cid', $cid)->value('id'),
            ]);
            DB::commit();
            \Log::info('Account setup completed successfully for CID: ' . $cid);
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Account setup failed for CID: ' . $cid . ' - Error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return false;
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::all();
        return view('auth.register', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $companies = Company::all();
        return view('auth.register', compact('companies'));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        // ✅ Validate form input (improved)
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8',
            'cid'      => 'required',
        ]);

        try {
            // ✅ Create user (admin)
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'cid'      => $request->cid,
                'is_admin' => 1,
            ]);
            $inserted = $this->setupAccountsForCompany($request->cid);
            // ✅ Assign all permissions to the new admin
            if ($inserted) {
                $allModules = Module::all();
                if ($allModules->isNotEmpty()) {
                    // Prepare bulk insert data (more efficient than loop)
                    $permissionsData = [];
                    foreach ($allModules as $module) {
                        $permissionsData[] = [
                            'module_id' => $module->id,
                            'read'      => 1,
                            'write'     => 1,
                            'delete'    => 1,
                            'update'    => 1,
                            'cid'       => $request->cid,
                            'userid'    => $user->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    // Bulk insert
                    DB::table('user_permission')->insert($permissionsData);
                }
            }

            return redirect()->route('dashboard.admin')->with('success', 'Admin registration successful! User account created with all permissions assigned.');
        } catch (\Exception $e) {
            \Log::error('Failed to register user: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Registration failed: ' . $e->getMessage());
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
