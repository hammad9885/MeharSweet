<?php

namespace App\Http\Controllers;

use App\Models\ExtraTime;
use Illuminate\Http\Request;

class ExtraTimeController extends Controller
{
    public function index()
    {
        $printSections = ExtraTime::all();
        return view('extra_time.index', compact('printSections'));
    }
    
    public function list()
    {
        return view('extra_time.list');
    }

    public function store(Request $request)
    {
        // Validate the form data
        $request->validate([
            'type_title' => 'required|string|max:255',
        ]);

        // Create a new Country entry and save it
        $printSections = new ExtraTime();
        $printSections->name = $request->type_title;
        $printSections->rate = $request->rate;
        $printSections->save();

        // Redirect to country.index with a success message
        return redirect()->route('extra_time.index')->with('success', 'Print Section added successfully!');
    }
    
public function destroy($id)
{
    
    $printSections = ExtraTime::findOrFail($id);

    
    $printSections->delete();

   
    return redirect()->route('extra_time.index')->with('success', 'Extra Time deleted successfully!');
}

public function edit($id)
{
    $printSection = ExtraTime::findOrFail($id);
    return view('extra_time.edit', compact('printSection'));
}
    
public function update(Request $request, $id)
{
  
   

   
    $printSection = ExtraTime::findOrFail($id);
    $printSection->title = $request->title;
    $printSection->rate = $request->rate;
    $printSection->save();

    return redirect()->route('extra_time.index')->with('success', 'Extra Time updated successfully!');
}

}
