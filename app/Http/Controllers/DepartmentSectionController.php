<?php

namespace App\Http\Controllers;

use App\Models\DepartmentSection;
use Illuminate\Http\Request;

class DepartmentSectionController extends Controller
{
    public function index()
    {
        $printSections = DepartmentSection::all();
        return view('print.index', compact('printSections'));
    }
    
    public function list()
    {
        return view('print.list'); // Return the form view (list.blade.php)
    }

    public function store(Request $request)
    {
        // Validate the form data
        $request->validate([
            'type_title' => 'required|string|max:255',
        ]);

        // Create a new Country entry and save it
        $printSections = new DepartmentSection();
        $printSections->name = $request->type_title;
        $printSections->save();

        // Redirect to country.index with a success message
        return redirect()->route('print.index')->with('success', 'Print Section added successfully!');
    }
public function destroy($id)
{
    // Find the country by its ID
    $printSections = DepartmentSection::findOrFail($id);

    // Delete the country record
    $printSections->delete();

    // Redirect back with a success message
    return redirect()->route('print.index')->with('success', 'Print section deleted successfully!');
}

public function edit($id)
{
    $printSection = DepartmentSection::findOrFail($id);
    return view('print.edit', compact('printSection'));
}
    
public function update(Request $request, $id)
{
    // Validate the form data
    $request->validate([
        'type_title' => 'required|string|max:255',
    ]);

    // Find the existing DepartmentSection entry and update it
    $printSection = DepartmentSection::findOrFail($id);
    $printSection->name = $request->type_title;
    $printSection->save();

    // Redirect to print.index with a success message
    return redirect()->route('print.index')->with('success', 'Print Section updated successfully!');
}

}
