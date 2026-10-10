<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::all();
        return view('country.index', compact('countries'));
    }
    
    public function list()
    {
        return view('country.list'); // Return the form view (list.blade.php)
    }

    public function store(Request $request)
    {
        // Validate the form data
        $request->validate([
            'type_title' => 'required|string|max:255',
        ]);

        // Create a new Country entry and save it
        $country = new Country();
        $country->country_name = $request->type_title;
        $country->save();

        // Redirect to country.index with a success message
        return redirect()->route('country.index')->with('success', 'Country added successfully!');
    }
public function destroy($id)
{
    // Find the country by its ID
    $country = Country::findOrFail($id);

    // Delete the country record
    $country->delete();

    // Redirect back with a success message
    return redirect()->route('country.index')->with('success', 'Country deleted successfully!');
}

}
