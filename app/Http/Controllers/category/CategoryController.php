<?php

namespace App\Http\Controllers\category;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('category.list',get_defined_vars());
    }
    public function create()
    {
        return view('category.create');
    }
   public function store(Request $request)
{
    // Validate the title field
    $request->validate([
        'title' => 'required|string|max:255|unique:categories,title', // The title is required, must be unique, and a string with a max length of 255
    ]);

    // Create the category after validation passes
    Category::create([
        'title' => $request->title,
    ]);

    // Redirect to the category list with a success message
    return redirect()->route('category.list')->with('success', 'Category created successfully.');
}

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('category.edit', get_defined_vars());
    }
    public function update(Request $request, $id)
{
    $request->validate([
        'title' => 'required|string|max:255',
    ]);

    try {
        $category = Category::findOrFail($id);
        $category->title = $request->input('title');
        $category->save();
    } catch (\Exception $e) {
        dd($e->getMessage()); // Dump the error message and stop execution
    }

    return redirect()->route('category.list')->with('success', 'Category updated successfully');
}

    public function destroy($id)
    {
        $categories = Category::findOrFail($id);
        $categories->delete();

        return redirect()->route('category.list')->with('success', 'Category deleted successfully');
    }
}
