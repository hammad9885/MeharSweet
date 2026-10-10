<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Module as Model;
use App\Models\UserPermission;
use Illuminate\Http\Request;

class UserPermissionManage extends Controller
{
    //
    public function index()
    {
        // Get all users (supporting company cid filter or all users if null/admin)
        $cid = auth()->user()->cid;
        // Always scope user list to the current company. If no company ID is present, return an empty collection to avoid cross‑company data exposure.
        if ($cid) {
            $users = User::where('cid', $cid)->get();
        } else {
            $users = collect(); // No company context – return none
        }

        // Self-heal: ensure every module page referenced below exists in the module table.
        // This lets the page load all pages even when migrations could not run on the server.
        $allModuleNames = [
            // Accounts
            'Level1', 'Level2', 'Chart Of Account', 'Cash Payment', 'Cash Receipt',
            'Bank Payment', 'Bank Receipt', 'Journal Voucher', 'Opening Balance',
            'Ledger', 'Cash Book', 'Payables', 'Recieveable', 'Trial Balance',
            // Sale
            'Sale Invoice', 'Sale Order', 'Delivery Challan (SOR)', 'Sale Return',
            'Raw Sale Invoice', 'Raw Return Invoice', 'Customer', 'Customer Category',
            'Item Wise Sale', 'Category Wise Sale', 'Customer Wise Sale', 'Brand Wise Sale',
            'Transport Company', 'Reference Name', 'Sale Report', 'Contractor Lot Posting',
            // Purchase
            'Purchase Invoice', 'Purchase Return', 'Supplier', 'Supplier Wise Report',
            'Item Wise Purchase', 'Category Wise Purchase', 'Brand Wise Purchase',
            'Contractor', 'Purchase Report',
            // Manufacturing
            'Consumption', 'Production', 'Raw Material Report', 'Finished Product Report',
            // Raw Material
            'Item Category', 'Item Units', 'Item Registration', 'Brand Name',
            // Finished Product
            'Product Groups', 'Product Master', 'Mold Registration', 'Barcode Generate',
            // Stock Management
            'Sale Type', 'Stock Adjustment', 'Opening Stock', 'Stock In', 'Stock Issue',
            'Stock Value', 'Stock Report', 'Store Wise Report', 'Packing Lot Posting',
        ];
        foreach ($allModuleNames as $name) {
            Model::firstOrCreate(['name' => $name]);
        }

        // Categorize modules by department tabs
        $accountsModules = Model::whereIn('name', [
            'Level1', 'Level2', 'Chart Of Account', 'Cash Payment', 'Cash Receipt',
            'Bank Payment', 'Bank Receipt', 'Journal Voucher', 'Opening Balance',
            'Ledger', 'Cash Book', 'Payables', 'Recieveable', 'Trial Balance'
        ])->get();

        $saleModules = Model::whereIn('name', [
            'Sale Invoice', 'Sale Order', 'Delivery Challan (SOR)', 'Sale Return',
            'Raw Sale Invoice', 'Raw Return Invoice',
            'Customer', 'Customer Category',
            'Item Wise Sale', 'Category Wise Sale', 'Customer Wise Sale', 'Brand Wise Sale',
            'Transport Company', 'Reference Name',
            'Sale Report',
            'Contractor Lot Posting'
        ])->get();

        $purchaseModules = Model::whereIn('name', [
            'Purchase Invoice', 'Purchase Return',
            'Supplier', 'Supplier Wise Report',
            'Item Wise Purchase', 'Category Wise Purchase', 'Brand Wise Purchase',
            'Contractor',
            'Purchase Report'
        ])->get();

        $manufacturingModules = Model::whereIn('name', [
            'Consumption', 'Production',
            'Raw Material Report', 'Finished Product Report'
        ])->get();

        $rawMaterialModules = Model::whereIn('name', [
            'Item Category', 'Item Units', 'Item Registration',
            'Brand Name', 'Raw Material Report'
        ])->get();

        $finishedProductModules = Model::whereIn('name', [
            'Product Groups', 'Product Master', 'Mold Registration',
            'Barcode Generate', 'Finished Product Report'
        ])->get();

        // Stock Management modules — order in this array controls display order below
        $stockModuleNames = [
            'Sale Type', 'Stock Adjustment', 'Opening Stock', 'Stock In', 'Stock Issue', 'Stock Value',
            'Stock Report', 'Store Wise Report', 'Packing Lot Posting'
        ];

        $stockModules = Model::whereIn('name', $stockModuleNames)->get()
            ->sortBy(function ($module) use ($stockModuleNames) {
                return array_search($module->name, $stockModuleNames);
            })
            ->values();

        $setupModules = Model::whereIn('name', [
            'Item Category', 'Units', 'Brand Name', 'Item Registration',
            'Product Groups', 'Product Master', 'Mold Registration',
            'Barcode Generate'
        ])->get();

        return view('user.permissions', compact(
            'users',
            'accountsModules',
            'saleModules',
            'purchaseModules',
            'manufacturingModules',
            'rawMaterialModules',
            'finishedProductModules',
            'stockModules',
            'setupModules'
        ));
    }
     public function store(Request $request)
    {
        try {
            $userId = $request->userid;
            $permissions = $request->permissions ?? [];
            $userCid = auth()->user()->cid;

            foreach ($permissions as $moduleId => $permissionData) {
                UserPermission::updateOrCreate(
                    [
                        'userid' => $userId,
                        'module_id' => $moduleId,
                        'cid' => $userCid
                    ],
                    [
                        'read' => $permissionData['read'] ?? 0,
                        'write' => $permissionData['write'] ?? 0,
                        'update' => $permissionData['update'] ?? 0,
                        'delete' => $permissionData['delete'] ?? 0
                    ]
                );
            }

            // Remove permissions for modules not in the request
            UserPermission::where('userid', $userId)
                ->where('cid', $userCid)
                ->whereNotIn('module_id', array_keys($permissions))
                ->delete();

            return redirect()->back()->with('success', 'Permissions updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating permissions: ' . $e->getMessage());
        }
    }
      public function getUserPermissions($userId)
    {
        try {
            $userCid = auth()->user()->cid;
            $query = UserPermission::where('userid', $userId);
            if ($userCid) {
                $query->where('cid', $userCid);
            }
            $permissions = $query->get();

            return response()->json($permissions);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }
}