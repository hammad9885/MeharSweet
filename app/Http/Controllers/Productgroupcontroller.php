<?php

namespace App\Http\Controllers;

use App\Models\ProductGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductGroupController extends Controller
{
    // 🔹 List Product Groups
    public function index(Request $request)
    {
        $companyId = Auth::user()->cid;

        $group_name = $request->input('group_name');

        $query = ProductGroup::where('cid', $companyId);

        if ($group_name) {
            $query->where('group_name', 'like', '%' . $group_name . '%');
        }

        $productGroups = $query->get();

        $groups = ProductGroup::where('cid', $companyId)
            ->pluck('group_name', 'group_name')
            ->unique();

        return view('product_groups.List', get_defined_vars());
    }

    // 🔹 Create Product Group (form)
    public function create()
    {
        $companyId = Auth::user()->cid;

        return view('product_groups.Create', get_defined_vars());
    }

    // 🔹 Store Product Group
    public function store(Request $request)
    {
        $companyId = Auth::user()->cid;

        $request->validate([
            'group_name' => 'required|string|max:255',
        ]);

        ProductGroup::create([
            'cid'        => $companyId,
            'group_name' => $request->group_name,
        ]);

        return redirect()->route('product-groups.index')
            ->with('success', 'Product Group created successfully.');
    }

    // 🔹 Edit Product Group
    public function edit($id)
    {
        $companyId = Auth::user()->cid;
        $productGroup = ProductGroup::where('cid', $companyId)->findOrFail($id);

        return view('product_groups.Edit', get_defined_vars());
    }

    // 🔹 Update Product Group
    public function update(Request $request, $id)
    {
        $companyId = Auth::user()->cid;

        $request->validate([
            'group_name' => 'required|string|max:255',
        ]);

        try {
            $productGroup = ProductGroup::where('cid', $companyId)->findOrFail($id);
            $productGroup->update([
                'group_name' => $request->input('group_name'),
            ]);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

        return redirect()->route('product-groups.index')
            ->with('success', 'Product Group updated successfully');
    }

    // 🔹 Delete Product Group
    public function destroy($id)
    {
        $companyId = Auth::user()->cid;
        $productGroup = ProductGroup::where('cid', $companyId)->findOrFail($id);

        // Check if product group is being used (add your relationship checks here)
        // Example: 
        // $itemMaster = ItemMaster::where('group_id', $productGroup->id)->first();
        // if($itemMaster){
        //     return redirect()->back()->with('error','This Product Group is already in use and cannot be deleted');
        // }

        $productGroup->delete();

        return redirect()->route('product-groups.index')
            ->with('success', 'Product Group deleted successfully');
    }
}