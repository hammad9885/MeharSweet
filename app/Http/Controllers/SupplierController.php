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

        return \Inertia\Inertia::render('Purchase/Suppliers/Index', compact('suppliers'));
    }

    public function create()
    {
        $lastSupplier = Supplier::where('cid', auth()->user()->cid)->orderBy('id', 'desc')->first();
        $next_id = ($lastSupplier->id ?? 0) + 1;

        return \Inertia\Inertia::render('Purchase/Suppliers/CreateEdit', [
            'next_id' => $next_id,
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $cid = $user->cid ?? null;

        $request->validate([
            'name'       => 'required|string',
            'urdu_title' => 'nullable|string',
            'mobile'     => 'nullable|string',
            'city'       => 'nullable|string',
            'seq_number' => 'nullable|string',
        ]);

        $erpParams = ErpParam::where('cid', $cid)->first();

        $accId = null;
        if ($erpParams && $erpParams->supplier_level) {
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
            $accId = $account->id;
        }

        Supplier::create([
            'name'       => $request->name,
            'urdu_title' => $request->urdu_title,
            'mobile'     => $request->mobile,
            'city'       => $request->city,
            'acc_id'     => $accId,
            'cid'        => $cid,
            'seq_number' => $request->seq_number,
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

        return \Inertia\Inertia::render('Purchase/Suppliers/CreateEdit', [
            'supplier' => $supplier,
        ]);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name'       => 'required|string',
            'urdu_title' => 'nullable|string',
            'mobile'     => 'nullable|string',
            'city'       => 'nullable|string',
            'seq_number' => 'nullable|string',
        ]);

        $supplier->update([
            'name'       => $request->name,
            'urdu_title' => $request->urdu_title,
            'mobile'     => $request->mobile,
            'city'       => $request->city,
            'seq_number' => $request->seq_number ?? $supplier->seq_number,
        ]);

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