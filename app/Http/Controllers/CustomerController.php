<?php

namespace App\Http\Controllers;

use App\Models\ErpParam;
use App\Models\PurchaseInvoice;
use App\Models\Customers;
use App\Models\CustomerCategory;
use App\Models\AccountMaster;
use App\Models\TransportCompany;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customers::with('accountMaster')->where('cid', auth()->user()->cid)->get();
        $customerNames = Customers::where('cid', auth()->user()->cid)->pluck('name')->unique();
        $categories = CustomerCategory::where('cid', auth()->user()->cid)->get();
        return \Inertia\Inertia::render('Sales/Customers/Index', [
            'customers' => $customers,
            'customerNames' => $customerNames,
            'categories' => $categories,
            'filters' => request()->all()
        ]);
    }

    public function create()
    {
        $categories = CustomerCategory::where('cid', auth()->user()->cid)->get();
        $transportCompanies = TransportCompany::where('cid', auth()->user()->cid)->get();
        $lastCustomer = Customers::where('cid', auth()->user()->cid)->orderBy('id', 'desc')->first();
        $next_id = ($lastCustomer->id ?? 0) + 1;

        return \Inertia\Inertia::render('Sales/Customers/CreateEdit', [
            'categories' => $categories,
            'transportCompanies' => $transportCompanies,
            'next_id' => $next_id,
        ]);
    }

public function store(Request $request)
{
    $user = auth()->user();
    $cid = $user->cid ?? null;

    $request->validate([
        'city'    => 'required|string',
        'address' => 'required|string',
        'cnic'    => 'required|string',
        'mobile'  => 'required|string',
        'email'   => 'required|email',
        'ntn'     => 'required|string',
         'name'     => 'required|string',
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
        'name'    => $request->name,
        'city'    => $request->city,
        'address' => $request->address,
        'cnic'    => $request->cnic,
        'mobile'  => $request->mobile,
        'email'   => $request->email,
        'ntn'     => $request->ntn,
        'acc_id'  => $account->id,
        'cid'     => $cid,
        'customer_category_id' => $request->customer_category_id,
        'seq_number' => $request->seq_number,
    ]);

    return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
}



    public function show(Customer $Customer)
    {
        return view('customers.show', compact('Customer'));
    }

  public function edit($id)
{
    $customer = Customers::findOrFail($id);
    $categories = CustomerCategory::where('cid', auth()->user()->cid)->get();
    $transportCompanies = TransportCompany::where('cid', auth()->user()->cid)->get();
    return \Inertia\Inertia::render('Sales/Customers/CreateEdit', [
        'customer' => $customer,
        'categories' => $categories,
        'transportCompanies' => $transportCompanies,
    ]);
}

    public function update(Request $request, Customers $customer)
    {
        $request->validate([
            'city' => 'required|string',
            'address' => 'required|string',
            'cnic' => 'required|string',
            'mobile' => 'required|string',
            'email' => 'required|email',
            'ntn' => 'required|string',
        ]);

        $customer->update($request->all());

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

  public function destroy(Customers $customer)
{
    $userCid = auth()->user()->cid;

    // Check if Customer exists in PurchaseInvoice
    $purchaseExists = PurchaseInvoice::where('Customer_id', $customer->id)
                        ->where('cid', $userCid)
                        ->exists();

    // Check if Customer exists in AccountMaster
    $accountExists = AccountMaster::where('acc_id', $customer->id)
                        ->where('cid', $userCid)
                        ->exists();

    if ($purchaseExists || $accountExists) {
        return redirect()->route('customers.index')
            ->with('error', 'Cannot delete this Customer because it is used in Purchase Invoices or Account Masters.');
    }

    // Safe to delete
    $customer->delete();

    return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
}
}
