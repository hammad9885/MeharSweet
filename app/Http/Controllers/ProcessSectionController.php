<?php

namespace App\Http\Controllers;

use App\Models\ProcessSection;
use App\Models\DepartmentSection;
use Illuminate\Http\Request;

class ProcessSectionController extends Controller
{
    public function index()
    {
        
        $processSections = ProcessSection::all();
        $departmentSections = DepartmentSection::all();
        return view('process.index', compact('processSections','departmentSections'));
    }
    
    public function list()
    {
        $departmentSections = DepartmentSection::all();
        return view('process.list', compact('departmentSections'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type_title' => 'required|string|max:255',
        ]);

        $processSections = new ProcessSection();
        $processSections->name = $request->type_title;
        $processSections->rate = $request->rate;
        $processSections->dept_id = $request->dept_id;
        $processSections->save();

        return redirect()->route('process.index')->with('success', 'Process Section added successfully!');
    }
public function destroy($id)
{
    // Find the country by its ID
    $processSections = ProcessSection::findOrFail($id);

    // Delete the country record
    $processSections->delete();

    // Redirect back with a success message
    return redirect()->route('process.index')->with('success', 'Process section deleted successfully!');
}

}
