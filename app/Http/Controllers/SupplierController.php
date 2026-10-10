<?php

namespace App\Http\Controllers;

use App\Models\ErpParam;
use App\Models\PurchaseInvoice;
use App\Models\Supplier;
use App\Models\AccountMaster;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::with('accountMaster')
            ->where('cid', auth()->user()->cid)
            ->get();

        $suppliers = Supplier::sortByOrderNumber($suppliers);

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        $lastSupplier = Supplier::where('cid', auth()->user()->cid)->orderBy('id', 'desc')->first();
        $next_id = ($lastSupplier->id ?? 0) + 1;
        return view('suppliers.create', compact('next_id'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $cid = $user->cid ?? null;

        $request->validate([
            'city'         => 'nullable',
            'address'      => 'nullable',
            'cnic'         => 'nullable',
            'mobile'       => 'nullable',
            'email'        => 'nullable',
            'ntn'          => 'nullable',
            'name'         => 'required|string',
            'order_number' => 'nullable|string',
        ], [], [
            'mobile' => 'Urdu Title',
        ]);

        $erpParams = ErpParam::where('cid', $cid)->first();

        if (!$erpParams || !$erpParams->supplier_level) {
            return back()->withErrors(['error' => 'Supplier Chart of Account configuration is missing.']);
        }

        $lastLevel = AccountMaster::where('level2_id', $erpParams->supplier_level)
            ->where('cid', $cid)
            ->orderBy('id', 'desc')
            ->first();

        $newCode = $lastLevel
            ? str_pad((int)$lastLevel->account_code + 1, 4, '0', STR_PAD_LEFT)
            : '0001';

        $account = AccountMaster::create([
            'title'        => $request->name . ' Supplier',
            'level2_id'    => $erpParams->supplier_level,
            'opening_date' => now(),
            'account_code' => $newCode,
            'cid'          => $cid,
        ]);

        $normalizedOrderNumber = Supplier::normalizeOrderNumber($request->order_number);

        Supplier::create([
            'name'         => $request->name,
            'city'         => $request->city,
            'address'      => $request->address,
            'cnic'         => $request->cnic,
            'mobile'       => $request->mobile,
            'email'        => $request->email,
            'ntn'          => $request->ntn,
            'acc_id'       => $account->id,
            'cid'          => $cid,
            'seq_number'   => $request->seq_number,
            'order_number' => $normalizedOrderNumber,
        ]);

        return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully.');
    }

    public function show(Supplier $supplier)
    {
        return view('suppliers.show', compact('supplier'));
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'city'         => 'nullable',
            'address'      => 'nullable',
            'cnic'         => 'nullable',
            'mobile'       => 'nullable',
            'email'        => 'nullable',
            'ntn'          => 'nullable',
            'name'         => 'required|string',
            'order_number' => 'nullable|string',
        ], [], [
            'mobile' => 'Urdu Title',
        ]);

        $request->merge([
            'order_number' => Supplier::normalizeOrderNumber($request->order_number),
        ]);

        $supplier->update($request->all());

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        $userCid = auth()->user()->cid;

        $purchaseExists = PurchaseInvoice::where('supplier_id', $supplier->id)
            ->where('cid', $userCid)
            ->exists();

        $accountExists = AccountMaster::where('id', $supplier->id)
            ->where('cid', $userCid)
            ->exists();

        if ($purchaseExists || $accountExists) {
            return redirect()->route('suppliers.index')
                ->with('error', 'Cannot delete this supplier because it is used in Purchase Invoices or Account Masters.');
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');
    }
}