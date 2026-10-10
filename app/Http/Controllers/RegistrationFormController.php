<?php

namespace App\Http\Controllers;

use App\Models\AccountMaster;
use App\Models\ItemMaster;
use App\Models\Country;
use App\Models\ProductMaster;
use App\Models\ProductLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegistrationFormController extends Controller
{
    public function index()
    {
        $loggedInUser = Auth::user();
        $accounts = AccountMaster::where('level2_id', 7)->get(); // Fetch all accounts
        $items = ItemMaster::all();        // Fetch all items
        $countries = Country::all();       // Fetch all countries

        return view('registration_form.list', compact('loggedInUser', 'accounts', 'items', 'countries'));
    }

    public function store(Request $request)
    {
        
        $request->validate([
        'product_type' => 'required|in:Local,Export', // Ensures the value is either 'Local' or 'Export'
        'productName' => 'required|string', // Example for product name validation
        // Add other validation rules here as necessary
    ]);
    
        // Create new ProductMaster entry
        $productMaster = new ProductMaster();
        $productMaster->aid = $request->account; // Store the account id
        $productMaster->prod_name = $request->productName;
        $productMaster->product_type = $request->product_type; // Store the product name
        $productMaster->country_id = $request->country; // Store the country id
        $productMaster->item_id = $request->item; // Store the item id
        $productMaster->grammage = $request->grammage; // Store the grammage
        $productMaster->length = $request->length; // Store the length
        $productMaster->width = $request->width; // Store the width
        $productMaster->rate = $request->rate; // Store the rate
        $productMaster->ups = $request->ups; // Store the rate
        $productMaster->descr = $request->description; // Store the description

        // Check if lamination is checked and store accordingly
        $productMaster->lamination = $request->lamination; // Value directly from hidden input
        $productMaster->lam_size = $request->lamination ? $request->lsize : null;
        $productMaster->lam_item = $request->lamination ? $request->litem : null;
        $productMaster->limpression = $request->lamination ? $request->limpression : null;

        // Check if UV is checked
        $productMaster->uv = $request->uv; // Value directly from hidden input
        $productMaster->simple = $request->simple ?? 0;  
         $productMaster->simple_rate = $request->simple ? $request->simple_rate : null;
        $productMaster->spot = $request->spot ?? 0;  
         $productMaster->spot_rate = $request->spot ? $request->spot_rate : null;


        // Check if corrugation is checked and store accordingly
        $productMaster->corrugation = $request->corrugation; // Value directly from hidden input
        $productMaster->curr_size = $request->corrugation ? $request->csize : null;
        $productMaster->curr_item = $request->corrugation ? $request->citem : null;
        $productMaster->clabour = $request->corrugation ? $request->clabour : null;

        // Check if color is checked and store accordingly
        $productMaster->color = $request->noColor; // Value directly from hidden input
        $productMaster->color_no = $request->noColor ? $request->color : null;
        
        $productMaster->breaking = $request->breaking; // Value directly from hidden input
        $productMaster->breaking_rate = $request->breaking ? $request->breaking_rate : null;
        
        $productMaster->manual_pasting_rate = $request->manual_pasting_rate; 
        
        $productMaster->auto_pasting_rate = $request->auto_pasting_rate; 
        

       if ($request->hasFile('file')) {
    $file = $request->file('file');
    
    // Generate a unique filename
    $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
    
    // Store the file in the 'uploads' directory within 'public/storage'
    $file->move(public_path('storage/uploads'), $fileName);
    
    // Save the relative file path (without 'storage/' as it's already in public/storage)
    $productMaster->file_path = 'uploads/' . $fileName;
}

        // Save the product entry
        $productMaster->save();

        // Redirect back with success message
        return redirect()->route('registration_form.reports')->with('success', 'Product added successfully!');
    }
    
public function reports(Request $request)
{
    // Fetch distinct account IDs and associated titles from ProductMaster
    $accounts = ProductMaster::select('aid')->distinct()->with('account')->get();
    
    // Fetch distinct country IDs from ProductMaster
    $countries = ProductMaster::select('country_id')->distinct()->with('country')->get();
    
    // Fetch unique product names for the Product Name dropdown
    $productNames = ProductMaster::select('prod_name')->distinct()->get();

    // Initialize the query for products with relationships
    $query = ProductMaster::with(['account', 'country']); // Eager load 'account' and 'country'

    // Apply filters based on request parameters
    if ($request->has('start_date') && $request->start_date != '') {
        $query->whereDate('created_at', '>=', $request->start_date);
    }

    if ($request->has('end_date') && $request->end_date != '') {
        $query->whereDate('created_at', '<=', $request->end_date);
    }

    if ($request->has('productName') && $request->productName != '') {
        $query->where('prod_name', $request->productName);
    }

    if ($request->has('country') && $request->country != '') {
        $query->where('country_id', $request->country);
    }

    if ($request->has('account') && $request->account != '') {
        $query->where('aid', $request->account);
    }

    // Get the filtered products
    $products = $query->get();

    // Pass the data to the view
    return view('registration_form.index', compact('products', 'accounts', 'countries', 'productNames'));
}


public function edit($id)

{   $itemsAll = ItemMaster::all();    
    $product = ProductMaster::find($id); // Replace with your model
    $accounts = AccountMaster::where('level2_id', 7)->get(); // Replace with your model for accounts
    
    $countries = Country::all(); // Replace with your model for countries
    $items = ItemMaster::where('type_id', 4)->get(); // Only items with type_id == 4
    $itemsCo = ItemMaster::where('type_id', 2)->get(); // Only items with type_id == 4
    
    return view('registration_form.edit', compact('itemsAll', 'product', 'accounts', 'countries', 'items', 'itemsCo'));
}


public function update(Request $request, $id)
{
    // Retrieve the product to update by its ID
    $productMaster = ProductMaster::findOrFail($id);

    // Capture the old data before updating
    $oldRate = $productMaster->rate;
    $oldProdName = $productMaster->prod_name;
    $oldProdType = $productMaster->product_type;

    // Store the old data in the ProductLog table with action "Add"
    ProductLog::create([
        'prod_id' => $productMaster->id,
        'prod_name' => $oldProdName, // Store the old product name
        'product_type' => $oldProdType, // Store the old product name
        'old_rate' => $oldRate, // Old rate
        'new_rate' => $request->rate, // New rate from the form
        'action' => 'Add', // Explicitly set action to "Add" for update
        'updated_at' => now(), // Set the current timestamp
    ]);

    // Now update the product with new values from the request
    $productMaster->aid = $request->account;
    $productMaster->prod_name = $request->productName;
    $productMaster->product_type = $request->product_type; // Store the product name
    $productMaster->country_id = $request->country;
    $productMaster->item_id = $request->item;
    $productMaster->grammage = $request->grammage;
    $productMaster->length = $request->length;
    $productMaster->width = $request->width;
    $productMaster->rate = $request->rate; // Update the rate
    $productMaster->ups = $request->ups; // Update the rate
    $productMaster->descr = $request->description;

    // Check if lamination is checked and store accordingly
    $productMaster->lamination = $request->lamination;
    $productMaster->lam_size = $request->lamination ? $request->lsize : null;
    $productMaster->lam_item = $request->lamination ? $request->litem : null;
    
     $productMaster->clabour = $request->corrugation ? $request->clabour : null;
        $productMaster->limpression = $request->lamination ? $request->limpression : null;
        
    // Check if UV is checked
    $productMaster->uv = $request->uv;
     $productMaster->simple = $request->simple ?? 0;  
     $productMaster->simple_rate = $request->simple ? $request->simple_rate : null;
$productMaster->spot = $request->spot ?? 0;  
$productMaster->spot_rate = $request->spot ? $request->spot_rate : null;

    // Check if corrugation is checked and store accordingly
    $productMaster->corrugation = $request->corrugation;
    $productMaster->curr_size = $request->corrugation ? $request->csize : null;
    $productMaster->curr_item = $request->corrugation ? $request->citem : null;

    // Check if color is checked and store accordingly
    $productMaster->color = $request->noColor;
    $productMaster->color_no = $request->noColor ? $request->color : null;


     $productMaster->breaking = $request->breaking; // Value directly from hidden input
        $productMaster->breaking_rate = $request->breaking ? $request->breaking_rate : null;
        
        $productMaster->manual_pasting_rate = $request->manual_pasting_rate; 
        
        $productMaster->auto_pasting_rate = $request->auto_pasting_rate; 
        

    // Check if a new file is uploaded, and update the file if necessary
    if ($request->hasFile('file')) {
    $file = $request->file('file');
    
    // Generate a unique filename
    $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
    
    // Store the file in the 'uploads' directory within 'public/storage'
    $file->move(public_path('storage/uploads'), $fileName);
    
    // Save the relative file path (without 'storage/' as it's already in public/storage)
    $productMaster->file_path = 'uploads/' . $fileName;
}

    // Save the updated product data
    $productMaster->save();

    // Redirect back with a success message
    return redirect()->route('registration_form.reports')->with('success', 'Product updated successfully!');
}


public function destroy($id)
{
    // Retrieve the product by its ID from the ProductMaster table
    $productMaster = ProductMaster::findOrFail($id);

    // Update all entries in ProductLog with the same prod_id to have action 'Del'
    ProductLog::where('prod_id', $id)->update([
        'action' => 'Del', // Set action to "Del"
        'updated_at' => now(), // Optionally update the timestamp
    ]);

    // Now delete the product record from the ProductMaster table
    $productMaster->delete();

    // Redirect back with a success message
    return redirect()->route('registration_form.reports')->with('success', 'Product deleted successfully!');
}





public function removeImage($id)
{
    $productMaster = ProductMaster::find($id); // Find the product by its ID

    if ($productMaster && $productMaster->file_path) {
        // Delete the file from storage
        $filePath = public_path('/storage/' . $productMaster->file_path);
        if (file_exists($filePath)) {
            unlink($filePath); // Delete the file from storage
        }

        // Remove the file path from the database
        $productMaster->file_path = null;
        $productMaster->save(); // Save the changes in the database
    }

    // Return a success response
    return response()->json(['success' => 'Image removed successfully!']);
}

}
