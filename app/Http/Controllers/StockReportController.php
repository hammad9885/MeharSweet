<?php

namespace App\Http\Controllers;

use App\Models\ItemMaster;
use Illuminate\Http\Request;
use App\Models\AccountMaster;
use App\Models\SaleInvoice;
use App\Models\DeliveryDetail;
use App\Models\DeliveryMaster;
use App\Models\ProdMaster;
use App\Models\ProductMaster;
use App\Models\ProdCon;
use App\Models\ProdPro;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Carbon\Carbon;

class StockReportController extends Controller
{
    public function index()
    {
        $loggedInUser = Auth::user();
        $accounts = AccountMaster::all();
        $items = ItemMaster::all();
        $product = ProductMaster::all();
        return view('stock_report.list',get_defined_vars());
    }
    

public function store(Request $request)
{
    // Parse the date using Carbon
    $formattedDate = Carbon::parse($request->date)->format('Y-m-d');
    
    // Fetch the last entry in the ProductMaster table
    $lastEntry = ProdMaster::orderBy('id', 'desc')->first();
    
    // Determine the new invoice number: start from 1 if no entries exist, otherwise increment by 1
    $newInvoiceNumber = $lastEntry ? ((int) $lastEntry->v_no + 1) : 1;

    // Store the stock report entry using Eloquent
    $prodMaster = ProdMaster::create([
        'v_no' => $newInvoiceNumber,
        'date' => $formattedDate,
        'prepared_by' => $request->prepared_by,
    ]);

    // Check if entries are provided and loop through them
    if ($request->has('entries')) {
        foreach ($request->entries as $entry) {
            // Store Consumed Items
            Prodcon::create([
                'stock_report_id' => $newInvoiceNumber, 
                'item_code' => $entry['citem'],
                'cquantity' => $entry['cquantity'],
            ]);

            // Store Produced Items
            ProdPro::create([
                'stock_report_id' => $newInvoiceNumber,
                'item_code' => $entry['pitem'],
                'pquantity' => $entry['pquantity'],
            ]);
        }
    }

    return redirect()->back()->with('success', 'Stock report saved successfully!');
}

public function reports()
{
    // Fetch the corresponding Consumed and Produced Items using relationships
    $prodCons = Prodcon::all();
    $prodPros = ProdPro::all();

    // Pass the data to the view
    return view('stock_report.index', compact('prodCons', 'prodPros'));
}




}
