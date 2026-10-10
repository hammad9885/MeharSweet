<?php

namespace App\Http\Controllers;

use App\Models\PasteSection;
use Illuminate\Http\Request;

class PasteSectionController extends Controller
{
    public function index()
    {
        $pastingSections = PasteSection::all();
        return view('paste.index', compact('pastingSections'));
    }
    
    public function list()
    {
        return view('paste.list'); // Return the form view (list.blade.php)
    }

    public function store(Request $request)
    {
        // Validate the form data
        $request->validate([
            'type_title' => 'required|string|max:255',
        ]);

        // Create a new Country entry and save it
        $pasteSections = new PasteSection();
        $pasteSections->name = $request->type_title;
        $pasteSections->save();

        // Redirect to country.index with a success message
        return redirect()->route('paste.index')->with('success', 'Paste Section added successfully!');
    }
public function destroy($id)
{
    // Find the country by its ID
    $pasteSections = DyeSection::findOrFail($id);

    // Delete the country record
    $pasteSections->delete();

    // Redirect back with a success message
    return redirect()->route('paste.index')->with('success', 'Paste section deleted successfully!');
}

}
