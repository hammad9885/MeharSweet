<?php

namespace App\Http\Controllers\account;

use App\Models\Group;
use App\Models\Level1;
use App\Models\Level2;
use App\Models\Level3;
use App\Models\Module;
use Illuminate\Http\Request;
use App\Models\AccountMaster;
use App\Http\Controllers\Controller;
use App\Models\Customers;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use App\Models\TRNDTL;
use App\Models\UserPermission;

class AccountMasterController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
     $moduleId=Module::where('name','Chart Of Account')->first();
        $permiision=UserPermission::where('module_id',$moduleId->id)->where('cid',$user->cid)

 ->where('userid',$user->id)
        ->where('read',1);
        if(!$permiision->exists()){
            return redirect()->back()->with('error','You have not Permission. Please Contact with your admin');
        }
        $user = auth()->user();
        $cid = $user->cid ?? null;
        $level2s = Level2::where('cid', $cid)->get(); 
        $query = AccountMaster::with('level2s')->where('cid', $cid);

        $title = $request->input('title');
        $level2_id = $request->input('level2_id');

        if ($title) {
            $query->where('title', 'like', "%{$title}%");
        }

        if ($level2_id) {
            $query->where('level2_id', $level2_id);
        }

         $account_masters = $query->orderBy('opening_date', 'desc')->get();
        $cid = auth()->user()->cid;

$groups = Group::with([
    'level1s' => function($query) use ($cid) {
        $query->where('cid', $cid);
    },
    'level1s.level2s' => function($query) use ($cid) {
        $query->where('cid', $cid);
    },
    'level1s.level2s.AccountMasters' => function($query) use ($cid) {
        $query->where('cid', $cid);
    }
])->get();

        // Fetching distinct titles and level2 IDs for dropdowns
        $titles = AccountMaster::where('cid', $cid)->orderBy('title')->get();
        $level2_ids = AccountMaster::where('cid', $cid)->distinct()->pluck('level2_id');

        return \Inertia\Inertia::render('Accounts/ChartOfAccounts', [
            'account_masters' => $account_masters,
            'groups'          => $groups,
            'titles'          => $titles,
            'level2s'         => $level2s,
            'filters'         => [
                'title'     => $title,
                'level2_id' => $level2_id,
            ],
        ]);
    }


    public function create()
    {
         $user = auth()->user();
     $moduleId=Module::where('name','Chart Of Account')->first();
        $permiision=UserPermission::where('module_id',$moduleId->id)->where('cid',$user->cid)

 ->where('userid',$user->id)
        ->where('write',1);
        if(!$permiision->exists()){
            return redirect()->back()->with('error','You have not Permission. Please Contact with your admin');
        }
        $user = auth()->user();
        $cid = $user->cid ?? null;
        $level2s = Level2::where('cid', $cid)->get();
        return view('accounts.account_master.create', compact('level2s'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $cid = $user->cid ?? null;

        $request->validate([
            'title' => 'required|string|max:255',
            'level2_id' => 'required|exists:level2s,id',
            'opening_date' => 'required|date',
        ]);

        $lastLevel = AccountMaster::where('level2_id', $request->level2_id)
            ->where('cid', $cid)
            ->orderBy('id', 'desc')
            ->first();

        $newCode = $lastLevel ? str_pad((int)$lastLevel->account_code + 1, 4, '0', STR_PAD_LEFT) : '0001';

        if ($newCode === null) {
            return back()->withErrors(['level1_code' => 'Failed to generate a valid level1_code']);
        }

        AccountMaster::create([
            'title' => $request->title,
            'level2_id' => $request->level2_id,
            'opening_date' => $request->opening_date,
            'account_code' => $newCode,
            'cid' => $cid,
        ]);

        return redirect()->route('amaster.list')->with('success', 'Account created successfully.');
    }

    public function edit($id)
    {
         $user = auth()->user();
     $moduleId=Module::where('name','Chart Of Account')->first();
        $permiision=UserPermission::where('module_id',$moduleId->id)->where('cid',$user->cid)

 ->where('userid',$user->id)
        ->where('update',1);
        if(!$permiision->exists()){
            return redirect()->back()->with('error','You have not Permission. Please Contact with your admin');
        }
        $account_masters = AccountMaster::findOrFail($id);
        $level2s = Level2::all();
        $selectedGroup = $account_masters->level2_id;
        return view('accounts.account_master.edit', compact('account_masters', 'level2s', 'selectedGroup'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'opening_date' => 'required|date',
            'level2_id' => 'required|exists:level2s,id',
        ]);

        try {
            $level2 = AccountMaster::findOrFail($id);
            $level2->title = $request->input('title');
            $level2->opening_date = $request->input('opening_date');
            $level2->level2_id = $request->input('level2_id');
            $level2->save();

            return redirect()->route('amaster.list')->with('success', 'Account updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating account: ' . $e->getMessage());
        }
    }

    public function destroy($id)
{
     $user = auth()->user();
     $moduleId=Module::where('name','Chart Of Account')->first();
        $permiision=UserPermission::where('module_id',$moduleId->id)->where('cid',$user->cid)

 ->where('userid',$user->id)
        ->where('delete',1);
        if(!$permiision->exists()){
            return redirect()->back()->with('error','You have not Permission. Please Contact with your admin');
        }
    // Find the account master record
    $account_master = AccountMaster::findOrFail($id);
    $trnd = TRNDTL::where('account_id', $id)
               ->orWhere('cash_id', $id)
               ->first(); // get first matching record

if ($trnd) {
    // Record exists, return or prevent action
    return back()->with('error', 'Transaction exists for this account or cash. Action not allowed.');
}
    // Check if the account is used in ERP parameters
    $isUsedInPurchaseAccount = DB::table('erp_params')->where('purchase_account', $id)->exists();
    $isUsedInSaleAccount = DB::table('erp_params')->where('sale_ac', $id)->exists();

    // If the account is used, prevent deletion and return a message
    if ($isUsedInPurchaseAccount || $isUsedInSaleAccount) {
        return redirect()->route('amaster.list')->with('error', 'The "' . $account_master->title . '" is used in ERP parameters. To delete, you must first remove it from ERP parameters.');
    }

    // Proceed with deletion
    $account_master->delete();
    Supplier::where('acc_id', $id)->delete();
    Customers::where('acc_id', $id)->delete();
    return redirect()->route('amaster.list')->with('success', 'Account Master deleted successfully');
}


    public function reports()
    {
        $groups = Group::with('level1s.level2s.AccountMasters')->get(); // Fetch groups data
        return view('account_reports.account', compact('groups'));
    }
}
