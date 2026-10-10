<?php

namespace App\Http\Controllers;

use App\Models\Contractor;
use App\Models\AccountMaster;
use App\Models\ErpParam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractorController extends Controller
{
    public function index()
    {
        $user = Auth::user()->cid;
        $contractors = Contractor::where('cid', $user)->get();
        return view('contractors.index', compact('contractors'));
    }

    public function create()
    {
        return view('contractors.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user()->cid;
        $request->validate(['name' => 'required|string|max:255']);
        $contractor = Contractor::create([
            'name' => $request->name,
            'cid' => $user,
        ]);

        $erpParams = ErpParam::where('cid', $user)->first();
        if ($erpParams && $erpParams->contractor_level) {
            $lastLevel = AccountMaster::where('level2_id', $erpParams->contractor_level)
                ->where('cid', $user)
                ->orderBy('account_code', 'desc')
                ->first();
            $newCode = $lastLevel && $lastLevel->account_code
                ? str_pad((int)$lastLevel->account_code + 1, 4, '0', STR_PAD_LEFT)
                : '0001';
            AccountMaster::create([
                'level2_id' => $erpParams->contractor_level,
                'title' => $request->name,
                'cid' => $user,
                'opening_date' => now(),
                'account_code' => $newCode,
            ]);
        }

        return redirect()->route('contractors.index')->with('success', 'Contractor created successfully.');
    }

    public function edit(Contractor $contractor)
    {
        return view('contractors.edit', compact('contractor'));
    }

    public function update(Request $request, Contractor $contractor)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $contractor->update(['name' => $request->name]);

        $ac = AccountMaster::where('title', $contractor->getOriginal('name'))
            ->where('cid', $contractor->cid)->first();
        if ($ac) {
            $ac->update(['title' => $request->name]);
        }

        return redirect()->route('contractors.index')->with('success', 'Contractor updated successfully.');
    }

    public function destroy(Contractor $contractor)
    {
        $contractor->delete();
        return redirect()->route('contractors.index')->with('success', 'Contractor deleted successfully.');
    }
}
