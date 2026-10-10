<?php

namespace App\Http\Controllers\account;

use App\Models\Group;
use App\Models\Level1;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\Level2;
class Level1Controller extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $cid = $user->cid ?? null;
        $level1s = Level1::with('groups')
            ->where('cid', $cid)
            ->get();

        return \Inertia\Inertia::render('Accounts/Level1', [
            'level1s' => $level1s,
        ]);
    }
    public function create()
    {
        $groups = Group::all();
        return view('accounts.level1.create',get_defined_vars());
    }
    public function store(Request $request)
    {
        $user = auth()->user();
        $cid = $user->cid ?? null;

        // Validate the request data
        $request->validate([
            'title' => 'required|string|max:255',
            'group_id' => 'required|exists:groups,id',
        ]);

        // Automatically generate the next level1_code if not provided
        $lastLevel = Level1::where('group_id', $request->group_id)
                            ->where('cid', $cid)
                            ->orderBy('id', 'desc')
                            ->first();

        // If no previous record exists, start from '001'. Otherwise, increment the last code.
        $newCode = $lastLevel ? str_pad((int)$lastLevel->level1_code + 1, 3, '0', STR_PAD_LEFT) : '01';

        // Ensure that the code is generated
        if ($newCode === null) {
            return back()->withErrors(['level1_code' => 'Failed to generate a valid level1_code']);
        }

        // Create the Level1 record with the new level1_code and cid
        Level1::create([
            'title' => $request->title,
            'group_id' => $request->group_id,
            'level1_code' => $newCode,  // Ensure this value is passed
            'cid' => $cid,
        ]);

        return redirect()->route('level1.list')->with('success', 'Level1 created successfully.');
    }







    public function edit($id)
    {
        $level1 = Level1::findOrFail($id);
        $groups = Group::all();
        $selectedGroup = $level1->group_id;
        return view('accounts.level1.edit', get_defined_vars());
    }
   public function update(Request $request, $id)
{
    // Validate the request data
    $request->validate([
        'title' => 'required|string|max:255',  // The title is required and must be a string with a maximum length of 255 characters
        'group_id' => 'required|exists:groups,id',  // The group_id must be selected and exist in the groups table
    ]);

    try {
        // Find the level1 entry by ID and update its values
        $level1 = Level1::findOrFail($id);
        $level1->title = $request->input('title');
        $level1->group_id = $request->input('group_id');
        $level1->save();
    } catch (\Exception $e) {
        dd($e->getMessage()); // Dump error message if an exception occurs
    }

    return redirect()->route('level1.list')->with('success', 'Level1 updated successfully.');
}


    public function destroy($id)
{
    // Find the Level1 record by ID
    $level1 = Level1::findOrFail($id);
      $trnd = Level2::where('level1_id', $id)
               ->first(); // get first matching record

if ($trnd) {
    // Record exists, return or prevent action
    return back()->with('error', 'this account not allowed delete because is linked with Accounts.');
}
    
    // Check if the Level1 ID is used in the level2s table
    $isUsedInLevel2s = DB::table('level2s')->where('level1_id', $id)->exists();

    // If the Level1 ID is used, prevent deletion and show an error message
    if ($isUsedInLevel2s) {
        return redirect()->route('level1.list')->with('error', 'The Level1 title "' . $level1->title . '" is used in Level2. To delete, you must first remove it from Level2.');
    }

    // Proceed with deletion
    $level1->delete();

    return redirect()->route('level1.list')->with('success', 'Level1 deleted successfully');
}

}
