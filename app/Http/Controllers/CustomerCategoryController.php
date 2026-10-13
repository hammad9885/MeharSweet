<?php

namespace App\Http\Controllers;

use App\Models\CustomerCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerCategoryController extends Controller
{
    public function index()
    {
        $categories = CustomerCategory::where('cid', Auth::user()->cid)->get();
        return \Inertia\Inertia::render('Sales/CustomerCategories/Index', [
            'categories' => $categories
        ]);
    }

    public function create()
    {
        return $this->index();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        CustomerCategory::create([
            'name' => $request->name,
            'cid'  => Auth::user()->cid,
        ]);

        return redirect()->route('customer-categories.index')->with('success', 'Customer category created successfully.');
    }

    public function edit($id)
    {
        $categories = CustomerCategory::where('cid', Auth::user()->cid)->get();
        $editingCategory = CustomerCategory::where('cid', Auth::user()->cid)->findOrFail($id);
        return \Inertia\Inertia::render('Sales/CustomerCategories/Index', [
            'categories' => $categories,
            'editingCategory' => $editingCategory,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = CustomerCategory::where('cid', Auth::user()->cid)->findOrFail($id);
        $category->update(['name' => $request->name]);

        return redirect()->route('customer-categories.index')->with('success', 'Customer category updated successfully.');
    }

    public function destroy($id)
    {
        $category = CustomerCategory::where('cid', Auth::user()->cid)->findOrFail($id);
        $category->delete();
        return redirect()->route('customer-categories.index')->with('success', 'Customer category deleted successfully.');
    }
}
