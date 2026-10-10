<?php

namespace App\Http\Controllers\account;

use App\Models\Level1;
use App\Models\Level2;
use App\Models\Module;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\AccountMaster;

class Level2Controller extends Controller
{
    public function index()
    {
        $user = auth()->user();
     $moduleId=Module::where('name','Level2')->first();
        $permiision=UserPermission::where('module_id',$moduleId->id)->where('cid',$user->cid)

 ->where('userid',$user->id)
        ->where('read',1);
        if(!$permiision->exists()){
            return redirect()->back()->with('error','You have not Permission. Please Contact with your admin');
        }
        $cid = $user->cid ?? null;
        $level2s = Level2::with('level1s.groups')
            ->where('cid', $cid)
            ->get();

        return \Inertia\Inertia::render('Accounts/Level2', [
            'level2s' => $level2s,
        ]);
    }
    public function create()
    {
         $user = auth()->user();
     $moduleId=Module::where('name','Level2')->first();
        $permiision=UserPermission::where('module_id',$moduleId->id)->where('cid',$user->cid)

 ->where('userid',$user->id)
        ->where('write',1);
        if(!$permiision->exists()){
            return redirect()->back()->with('error','You have not Permission. Please Contact with your admin');
        }
        $level1s = Level1::where('cid', auth()->user()->cid)->get();
        return view('accounts.level2.create',get_defined_vars());
    }
    public function store(Request $request)
    {
        $user = auth()->user();
        $cid = $user->cid ?? null;

        // Validation rules
        $request->validate([
            'title' => 'required|string|max:255',
            'level1_id' => 'required|exists:level1s,id',  // Ensure a valid level1 is selected
        ]);
        $lastLevel = Level2::where('level1_id', $request->level1_id)
            ->where('cid', $cid)
            ->orderBy('id', 'desc')
            ->first();

        // If no previous record exists, start from '001'. Otherwise, increment the last code.
        $newCode = $lastLevel ? str_pad((int)$lastLevel->level2_code + 1, 3, '0', STR_PAD_LEFT) : '001';

        // Ensure that the code is generated
        if ($newCode === null) {
            return back()->withErrors(['level1_code' => 'Failed to generate a valid level1_code']);
        }

        // Store Level2 with cid
        Level2::create([
            'title' => $request->title,
            'level1_id' => $request->level1_id,
            'level2_code' => $newCode,
            'cid' => $cid,
        ]);

        // Redirect with success message
        return redirect()->route('level2.list')->with('success', 'Level2 created successfully.');
    }

    public function edit($id)
    {
         $user = auth()->user();
     $moduleId=Module::where('name','Level2')->first();
        $permiision=UserPermission::where('module_id',$moduleId->id)->where('cid',$user->cid)

 ->where('userid',$user->id)
        ->where('update',1);
        if(!$permiision->exists()){
            return redirect()->back()->with('error','You have not Permission. Please Contact with your admin');
        }
        $level2 = Level2::findOrFail($id);
        $level1s = Level1::where('cid', auth()->user()->cid)->get();
        $selectedGroup = $level2->level1_id;
        return view('accounts.level2.edit', get_defined_vars());
    }
   public function update(Request $request, $id)
{
    // Validate the input fields
    $request->validate([
        'title' => 'required|string|max:255',
        'level1_id' => 'required|exists:level1s,id', // Ensures that the selected level1 exists
    ]);

    try {
        // Find the specific Level2 record
        $level2 = Level2::findOrFail($id);

        // Update the Level2 record with new values
        $level2->title = $request->input('title');
        $level2->level1_id = $request->input('level1_id');
        $level2->save();

    } catch (\Exception $e) {
        dd($e->getMessage()); // Debugging message if an error occurs
    }

    // Redirect back to the list page with a success message
    return redirect()->route('level2.list')->with('success', 'Level2 updated successfully');
}


    public function destroy($id)
{
     $user = auth()->user();
     $moduleId=Module::where('name','Level2')->first();
        $permiision=UserPermission::where('module_id',$moduleId->id)->where('cid',$user->cid)

 ->where('userid',$user->id)
        ->where('delete',1);
        if(!$permiision->exists()){
            return redirect()->back()->with('error','You have not Permission. Please Contact with your admin');
        }
    // Find the Level2 record by ID
    $level2 = Level2::findOrFail($id);
    $trnd = AccountMaster::where('level2_id', $id)
               ->first(); // get first matching record

if ($trnd) {
    // Record exists, return or prevent action
    return back()->with('error', 'this account not allowed delete because is linked with Accounts.');
}
    // Check if the Level2 ID is used in the ERP parameters
    $isUsedInBankLevel = DB::table('erp_params')->where('bank_level', $id)->exists();
    $isUsedInCashLevel = DB::table('erp_params')->where('cash_level', $id)->exists();
    $isUsedInSupplierLevel = DB::table('erp_params')->where('supplier_level', $id)->exists();

    // If the Level2 ID is used, prevent deletion and show an error message
    if ($isUsedInBankLevel || $isUsedInCashLevel || $isUsedInSupplierLevel) {
        return redirect()->route('level2.list')->with('error', 'The "' . $level2->title . '" is used in ERP parameters. To delete, you must first remove it from ERP parameters.');
    }

    // Proceed with deletion
    $level2->delete();

    return redirect()->route('level2.list')->with('success', 'Level2 deleted successfully');
}


}
