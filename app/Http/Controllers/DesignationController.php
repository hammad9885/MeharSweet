<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    public function index()
    {
        $printSections = Designation::all();
        return view('designation.index', compact('printSections'));
    }
    
    public function list()
    {
        return view('designation.list'); // Return the form view (list.blade.php)
    }

    public function store(Request $request)
    {
        // Validate the form data
        $request->validate([
            'type_title' => 'required|string|max:255',
        ]);

        // Create a new Country entry and save it
        $printSections = new Designation();
        $printSections->name = $request->type_title;
        $printSections->save();

        // Redirect to country.index with a success message
        return redirect()->route('designation.index')->with('success', 'Print Section added successfully!');
    }
public function destroy($id)
{
    // Find the country by its ID
    $printSections = Designation::findOrFail($id);

    // Delete the country record
    $printSections->delete();

    // Redirect back with a success message
    return redirect()->route('designation.index')->with('success', 'Print section deleted successfully!');
}

public function edit($id)
{
    $printSection = Designation::findOrFail($id);
    return view('designation.edit', compact('printSection'));
}
    
public function update(Request $request, $id)
{
    // Validate the form data
    $request->validate([
        'type_title' => 'required|string|max:255',
    ]);

    // Find the existing DepartmentSection entry and update it
    $printSection = Designation::findOrFail($id);
    $printSection->name = $request->type_title;
    $printSection->save();

    // Redirect to print.index with a success message
    return redirect()->route('designation.index')->with('success', 'Print Section updated successfully!');
}

}
