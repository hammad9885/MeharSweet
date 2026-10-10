<?php

namespace App\Http\Controllers;

use App\Models\ItemMaster;
use App\Models\TRNDTL;
use App\Models\ErpParam;
use Illuminate\Http\Request;
use App\Models\AccountMaster;
use App\Models\WastageSale;
use App\Models\DeliveryMaster;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WastageController extends Controller
{
  public function reports(Request $request)
{
    // Default start date: 1st of the current month
    $defaultStartDate = date('Y-m-01');
    // Default end date: today
    $defaultEndDate = date('Y-m-d');

    // Get start and end dates from the request
    $startDate = $request->input('start_date', $defaultStartDate);
    $endDate = $request->input('end_date', $defaultEndDate);
  
    // Base query
   $query = "
    SELECT 
        MAX(DATE(ws.created_at)) AS latest_date, 
        IFNULL(im.item_code, ws.item_code) AS item_code, 
        SUM(ws.total) AS total_sum
    FROM 
        wastage_sales ws
    LEFT JOIN 
        item_masters im ON ws.item_code = im.id
    WHERE 
        DATE(ws.created_at) BETWEEN ? AND ?
    GROUP BY 
        ws.item_code, im.item_code;
";

    // Execute the query with the date range
    $wastageData = DB::select($query, [$startDate, $endDate]);

    // Pass the data and date range to the Blade view
    return view('wastage.index', compact('wastageData', 'startDate', 'endDate'));
}

}
