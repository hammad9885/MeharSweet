<?php

namespace App\Http\Controllers;

use App\Models\Level2;
use App\Models\ErpParam;
use Illuminate\Http\Request;
use App\Models\AccountMaster;
use Illuminate\Support\Facades\Auth;

class ErpParamController extends Controller
{
    public function index()
    {
        // $accountMasters = AccountMaster::all();
        $erp_params = ErpParam::with('level2')->where('cid', auth()->user()->cid)->get();
        return view('erp_params.list',get_defined_vars());
    }
    public function create()
    {
        $accountMasters = AccountMaster::where('cid', auth()->user()->cid)->get();
        $level2s = Level2::where('cid', auth()->user()->cid)->get();
        return view('erp_params.create',get_defined_vars());
    }
    public function store(Request $request)
{
    $user=Auth::User()->cid;
    // Validate incoming data
    $validated = $request->validate([
        'bank_level' => 'nullable|exists:level2s,id',
        'cash_level' => 'nullable|exists:level2s,id',
        'customer_level' => 'nullable|exists:level2s,id',
        'employee_level' => 'nullable|exists:level2s,id',
        'supplier_level' => 'nullable|exists:level2s,id',
        'purchase_account' => 'nullable|exists:account_masters,id',
        'sale_ac' => 'nullable|exists:account_masters,id',
        'cash_acc' => 'nullable|exists:account_masters,id',
        'capital_account' => 'nullable|exists:account_masters,id',
    ]);

    // Create the ErpParam record
    $data = array_merge($validated, ['cid' => $user]);
    ErpParam::create($data);

    return redirect()->route('erp_param.list')->with('success', 'Erp Param created successfully.');
}
public function edit($id)
{
    // Fetch the existing ErpParam by ID
    $erpParam = ErpParam::findOrFail($id);
    $accountMasters = AccountMaster::where('cid', auth()->user()->cid)->get();
    // Fetch the available levels
    $level2s = Level2::where('cid', auth()->user()->cid)->get();

    // Pass the ErpParam and levels to the view
    return view('erp_params.edit', get_defined_vars());
}

public function update(Request $request, $id)
{
    $erpParam = ErpParam::findOrFail($id);
    $erpParam->update([
        'bank_level' => $request->bank_level,
        'cash_level' => $request->cash_level,
        'employee_level' => $request->employee_level,
        'contractor_level' => $request->contractor_level,
       
        'supplier_level' => $request->supplier_level,
        'purchase_account' => $request->purchase_account,
       'capital_account' => $request->capital_account,  // New field added here
        'sale_ac' => $request->sale_ac,  // New field added here
        'customer_level' => $request->customer_level,  // New field added here
        'cash_acc' => $request->cash_acc,  // New field added here
       
    ]);

    return redirect()->route('erp_param.list')->with('success', 'Erp Param updated successfully.');
}


public function destroy($id)
{
    // Find and delete the ErpParam
    $erpParam = ErpParam::findOrFail($id);
    $erpParam->delete();

    return redirect()->route('erp_param.list')->with('success', 'Erp Param deleted successfully.');
}


}