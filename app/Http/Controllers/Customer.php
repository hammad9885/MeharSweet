<?php

namespace App\Http\Controllers;

use App\Models\ErpParam;
use App\Models\PurchaseInvoice;
use App\Models\Customers ;
use App\Models\AccountMaster;
use App\Models\CustomerCategory;
use Illuminate\Http\Request;

class Customer extends Controller
{
    public function index()
    {
        $query = Customers::with('accountMaster')->where('cid', auth()->user()->cid);

        if ($search = request('search')) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $customers = $query->get();
        $customerNames = Customers::where('cid', auth()->user()->cid)->pluck('name');

        return view('customers.index', compact('customers', 'customerNames'));
    }

    public function create()
    {
        $categories = CustomerCategory::where('cid', auth()->user()->cid)->get();
        $transportCompanies = \App\Models\TransportCompany::where('cid', auth()->user()->cid)->get();
        $lastCustomer = Customers::where('cid', auth()->user()->cid)->orderBy('id', 'desc')->first();
        $next_id = ($lastCustomer->id ?? 0) + 1;
        return view('customers.create', compact('categories', 'next_id', 'transportCompanies'));
    }

public function store(Request $request)
{
    $user = auth()->user();
    $cid = $user->cid ?? null;

   $request->validate([
    'company_title'    => 'nullable|string',
    'urdu_title'       => 'nullable|string',
    'city'             => 'nullable|string',
    'address'          => 'nullable|string',
    'cnic'             => 'nullable|string',
    'mobile'           => 'nullable|string',
    'email'            => 'nullable|email',
    'ntn'              => 'nullable|string',
    'name'             => 'nullable|string',
    'b_type'           => 'nullable|string|in:Individual,AOP,PVT LTD,Ltd',
    'registration_tax' => 'nullable|string|in:Sale tax,Income Tax,Both',
    'strn'             => 'nullable|string',
    'file_no'          => 'nullable|string',
    'customer_category_id' => 'required',
    'seq_number'       => 'required',
    'disc'             => 'nullable|numeric|min:0|max:100',
]);


    // 🔹 Fetch ERP params (customer_level points to Level2 ID for customers)
    $erpParams = ErpParam::where('cid', $cid)->first();

    if (!$erpParams || !$erpParams->customer_level) {
        return back()->withErrors(['error' => 'Customer Chart of Account configuration is missing.']);
    }

    // 🔹 Generate new account_code under Customer's level2_id
    $lastLevel = AccountMaster::where('level2_id', $erpParams->customer_level)
        ->where('cid', $cid)
        ->orderBy('id', 'desc')
        ->first();

    $newCode = $lastLevel
        ? str_pad((int)$lastLevel->account_code + 1, 4, '0', STR_PAD_LEFT)
        : '0001';

    // 🔹 Create AccountMaster entry (Chart of Account)
    $account = AccountMaster::create([
        'title'        => $request->name . ' Customer',
        'level2_id'    => $erpParams->customer_level,
        'opening_date' => now(),
        'account_code' => $newCode,
        'cid'          => $cid,
    ]);

    // 🔹 Now create Customer linked to the new account
   Customers::create([
    'company_title'    => $request->company_title,
    'urdu_title'       => $request->urdu_title,
    'name'             => $request->name,
    'city'             => $request->city,
    'address'          => $request->address,
    'cnic'             => $request->cnic,
    'mobile'           => $request->mobile,
    'email'            => $request->email,
    'ntn'              => $request->ntn,
    'b_type'           => $request->b_type,
    'registration_tax' => $request->registration_tax,
    'strn'             => $request->strn,
    'file_no'          => $request->file_no,
    'acc_id'           => $account->id,
    'cid'              => $cid,
    'customer_category_id' => $request->customer_category_id,
    'seq_number'       => $request->seq_number,
    'disc'             => $request->disc,
]);


    return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
}



    public function getByAccId($accId)
    {
        $customer = Customers::where('acc_id', $accId)->where('cid', auth()->user()->cid)->first();
        return response()->json($customer);
    }

    public function show(Customers $Customer)
    {
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json($Customer);
        }
        return view('customers.show', compact('Customer'));
    }

  public function edit($id)
{
    $customer = Customers::findOrFail($id);
    $categories = CustomerCategory::where('cid', auth()->user()->cid)->get();
    $transportCompanies = \App\Models\TransportCompany::where('cid', auth()->user()->cid)->get();
    return view('customers.edit', compact('customer', 'categories', 'transportCompanies'));
}

    public function update(Request $request, Customers $Customer)
    {
      $request->validate([
    'company_title'    => 'nullable|string',
    'urdu_title'       => 'nullable|string',
    'city'             => 'nullable|string',
    'address'          => 'nullable|string',
    'cnic'             => 'nullable|string',
    'mobile'           => 'nullable|string',
    'email'            => 'nullable|email',
    'ntn'              => 'nullable|string',
    'b_type'           => 'nullable|string|in:Individual,AOP,PVT LTD,Ltd',
    'registration_tax' => 'nullable|string|in:Sale tax,Income Tax,Both',
    'strn'             => 'nullable|string',
    'file_no'          => 'nullable|string',
    'disc'             => 'nullable|numeric|min:0|max:100',
]);

        $Customer->update($request->all());

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

  public function destroy(Customers $Customer)
{
    $userCid = auth()->user()->cid;

    // Check if Customer exists in PurchaseInvoice
    $purchaseExists = PurchaseInvoice::where('supplier_id', $Customer->id)
                        ->where('cid', $userCid)
                        ->exists();

    // Check if Customer exists in AccountMaster
    $accountExists = AccountMaster::where('id', $Customer->id)
                        ->where('cid', $userCid)
                        ->exists();

    if ($purchaseExists || $accountExists) {
        return redirect()->route('customers.index')
            ->with('error', 'Cannot delete this Customer because it is used in Purchase Invoices or Account Masters.');
    }

    // Safe to delete
    $Customer->delete();

    return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
}
}
