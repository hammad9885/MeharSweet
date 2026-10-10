<?php

namespace App\Http\Controllers;

use App\Models\Custom;
use Illuminate\Http\Request;

class CustomController extends Controller
{
    public function index()
    {
        $customs = Custom::all();
        return view('custom.index', compact('customs'));
    }
    
    public function list()
    {
        return view('custom.list'); // Return the form view (list.blade.php)
    }

    public function store(Request $request)
    {
        // Validate the form data
        $request->validate([
            'type_title' => 'required|string|max:255',
        ]);

        // Create a new Country entry and save it
        $customs = new Custom();
        $customs->custom_name = $request->type_title;
        $customs->rate = $request->rate;
        $customs->save();

        // Redirect to country.index with a success message
        return redirect()->route('custom.index')->with('success', 'Custom added successfully!');
    }
    
public function destroy($id)
{
    // Find the country by its ID
    $customs = Custom::findOrFail($id);

    // Delete the country record
    $customs->delete();

    // Redirect back with a success message
    return redirect()->route('custom.index')->with('success', 'Custom deleted successfully!');
}

}
