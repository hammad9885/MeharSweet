<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TRNDTL;
use App\Models\ErpParam;
use App\Models\Custom;
use App\Models\ItemMaster;
use App\Models\AccountMaster;
use App\Models\JobDetail;
use App\Models\Solna;
use App\Models\Lamination;
use App\Models\DyeJob;
use App\Models\ProcessSection;
use App\Models\Employee;
use App\Models\DepartmentSection;
use App\Models\PasteSection;
use App\Models\PurchaseDetail; 
use App\Models\Corrugation; 
use App\Models\Breaking; 
use App\Models\ProductMaster;
use App\Models\ShipperPurchases;
use App\Models\CorrugationPurchase;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class JobSheetController extends Controller 
{
 public function index(Request $request)
{
    $loggedInUser = Auth::user();
    $items = ItemMaster::all();
    $processSections = ProcessSection::all();
    $departmentSections = DepartmentSection::all();
    $productMasters = ProductMaster::all();
    $employeeNames = Employee::all();
    
    $employeeTypes = DB::table('employee_type_details')->get();
    $employeeProcess = DB::table('employee_processes')->get();
    
    $productMasters2 = DB::table('product_master')
        ->join('account_masters', 'product_master.aid', '=', 'account_masters.id')
        ->select('product_master.aid', 'account_masters.title')
        ->get()
        ->unique('title');
    
    $accountSuppliers = AccountMaster::all();
    $erpParams = ErpParam::with('level2')->get();

    // Get next v_no - max existing + 1 (or 1 if no records)
    $nextVNo = (DB::table('job_details')->max('v_no') ?? 0) + 1;

    // In your controller where you get $boxboardData
$boxboardData = DB::table('boxboard_view')
    ->select('item_id', 'item_code', 'width', 'length', 'remain_qty')
    ->get();
    
    
        
    // Explicitly pass all variables to the view
    return view('job_sheet.list', [
        'loggedInUser' => $loggedInUser,
        'items' => $items,
        'processSections' => $processSections,
        'departmentSections' => $departmentSections,
        'productMasters' => $productMasters,
        'employeeNames' => $employeeNames,
        'employeeTypes' => $employeeTypes,
        'employeeProcess' => $employeeProcess,
        'productMasters2' => $productMasters2,
        'accountSuppliers' => $accountSuppliers,
        'erpParams' => $erpParams,
        'boxboardData' => $boxboardData,
        'nextVNo' => $nextVNo, // Our important variable
        'accountMasters' => collect(), // Initialize empty collection
        'purchaseAccount' => null,
    ]);
}
    
    public function getinkDetails($item_id)
{
    $ink = DB::table('ink_view')
        ->select('item', 'remain_qty', 'item_code')
        ->where('item_code', $item_id)
        ->first();

    return response()->json($ink);
}

public function getLaminationDetails(Request $request)
{
    $request->validate([
        'item_id' => 'required|integer',
        'size' => 'required|string'
    ]);

    try {
        $stock = DB::table('lamination_view') // Replace with your actual table
            ->where('item_id', $request->item_id)
            ->where('size', $request->size)
            ->first();

        return response()->json([
            'remain_qty' => $stock ? $stock->remain_qty : 0,
            'size' => $stock ? $stock->size : 0
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Failed to fetch stock details',
            'message' => $e->getMessage()
        ], 500);
    }
}

 public function getglueDetails($item_id)
{
    $glue = DB::table('glue_view')
        ->select('item', 'remain_qty', 'item_code')
        ->where('item_code', $item_id)
        ->first();

    return response()->json($glue);
}

 public function getshipperDetails($item_id)
{
    $shipper = DB::table('shipper_view')
        ->select('item', 'remain_qty', 'item_code')
        ->where('item_code', $item_id)
        ->first();

    return response()->json($shipper);
}

    public function edit($v_no)
{
  
    $jobDetails = JobDetail::where('v_no', $v_no)->get();
    $currentJobDetail = $jobDetails->first();
    // Fetch the related product (if any)
    $product = null;
    if ($currentJobDetail && $currentJobDetail->product_id) {
        $product = ProductMaster::find($currentJobDetail->product_id);
    }
    $solnas = Solna::where('v_no', $v_no)->get();
    
    $breakings = Breaking::where('v_no', $v_no)
                    ->get();
    
    $dyes = DyeJob::where('v_no', $v_no)->get();
    $corrugations = Corrugation::where('v_no', $v_no)->get();
    $laminations = Lamination::where('v_no', $v_no)->get();
    
    $departments = DB::table('employee_type_details')->pluck('department_name', 'department_id');
    $designations = DB::table('employee_type_details')->pluck('designation_name', 'designation_id');
    $employees = DB::table('employee_type_details')->pluck('employee_name', 'cnic_no');

    // If no records found, redirect back with an error message
    if ($jobDetails->isEmpty()) {
        return back()->with('error', "No job details found for V No {$v_no}");
    }

    $itemMasters = ItemMaster::all();
    $accountMasters = AccountMaster::all();
    $processSections = ProcessSection::all();
    $loggedInUser = Auth::user();
     $productMasters2 = DB::table('product_master')
    ->join('account_masters', 'product_master.aid', '=', 'account_masters.id')
    ->select('product_master.aid', 'account_masters.title')
    ->get()
    ->unique('title'); 
    
     $boxboardData = DB::table('boxboard_view')
    ->select('item_id', 'item_code', 'width', 'length', 'remain_qty')
    ->get();
    
     $inkData = DB::table('ink_view')
    ->select('item', 'remain_qty', 'item_code')
    ->get();
    
    $glueData = DB::table('glue_view')
    ->select('item', 'remain_qty', 'item_code')
    ->get();
    
    $shipperData = DB::table('shipper_view')
    ->select('item', 'remain_qty', 'item_code')
    ->get();
    
     $laminationData = DB::table('lamination_view')
    ->select('total_qty', 'remain_qty', 'item_id', 'size', 'item_name')
    ->get();
    
    $boxMachine = DB::table('machine_view')
    ->select('dept_id', 'department_name', 'process_name')
    ->get();
    
     $solnaMachine = DB::table('machine_view')
    ->select('dept_id', 'department_name', 'process_name', 'process_id')
    ->get();
    
     $dyeMachine = DB::table('machine_view')
    ->select('dept_id', 'department_name', 'process_name', 'process_id')
    ->get();
    
     $laminationMachine = DB::table('machine_view')
    ->select('dept_id', 'department_name', 'process_name', 'process_id')
    ->get(); 
    
    
     $corrugationMachine = DB::table('machine_view')
    ->select('dept_id', 'department_name', 'process_name', 'process_id')
    ->get(); 
    
    
    $employeeTypes = DB::table('employee_type_details')->get();
    $employeeProcess = DB::table('employee_processes')->get();
    
    $employeeTypeBox = DB::table('employee_type_details')
                ->where('department_id', 21)
                ->select('cnic_no', 'employee_name', 'department_name') 
                ->orderBy('employee_name')
                ->get();
    
    
    $employeeTypeSolna = DB::table('employee_type_details')
    ->whereIn('department_id', [23, 25])
    ->where('designation_id', 7)
    ->select('cnic_no', 'department_id' , 'employee_name', 'department_name', 'designation_name')
    ->orderBy('employee_name')
    ->get();            
    
     $employeeTypeSolnaHelper = DB::table('employee_type_details')
    ->whereIn('department_id', [23, 25])
    ->where('designation_id', 8)
    ->select('cnic_no', 'department_id' , 'employee_name', 'department_name', 'designation_name')
    ->orderBy('employee_name')
    ->get();  
    
     $employeeTypedye = DB::table('employee_type_details')
    ->whereIn('department_id', [28, 31])
    ->where('designation_id', 7)
    ->select('cnic_no', 'department_id' , 'employee_name', 'department_name', 'designation_name')
    ->orderBy('employee_name')
    ->get();    
    
    
    $employeeTypedyeHelper = DB::table('employee_type_details')
    ->whereIn('department_id', [28, 31])
    ->where('designation_id', 8)
    ->select('cnic_no', 'department_id' , 'employee_name', 'department_name', 'designation_name')
    ->orderBy('employee_name')
    ->get();   
    
    
    $employeeTypeLamination = DB::table('employee_type_details')
    ->whereIn('department_id', [22])
    ->where('designation_id', 7)
    ->select('cnic_no', 'department_id' , 'employee_name', 'department_name', 'designation_name')
    ->orderBy('employee_name')
    ->get();  
    
    $employeeTypebreaking = DB::table('employee_type_details')
    ->whereIn('department_id', [20])
    ->where('designation_id', 10)
    ->select('cnic_no', 'department_id' , 'employee_name', 'department_name', 'designation_name')
    ->orderBy('employee_name')
    ->get();  

    
    return view('job_sheet.edit', compact('jobDetails', 'currentJobDetail', 'product', 'dyeMachine' , 'shipperData' ,'employeeTypebreaking', 'laminationData' ,'corrugations', 'breakings' ,'laminationMachine', 'corrugationMachine' ,'solnaMachine', 'glueData' ,'laminations' ,'employeeTypeLamination' ,'employeeTypedye' ,'employeeTypedyeHelper' ,'dyes' ,'inkData' , 'solnas' ,'employeeTypeSolnaHelper' ,'itemMasters', 'employeeTypeSolna' ,'employeeTypeBox' , 'boxMachine' ,'accountMasters', 'processSections', 'loggedInUser','productMasters2','boxboardData', 'employeeTypes', 'employeeProcess','departments', 'designations', 'employees'));
}
    

   public function getProductDetails(Request $request)
{
    try {
        $product = ProductMaster::find($request->id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        // Fetch item_code from item_masters using item_id from product_masters
        $itemCode = ItemMaster::where('id', $product->item_id)->value('item_code');

        // Calculate packet_size
        $packetSize = "L:{$product->length}, W:{$product->width}, G:{$product->grammage}";


        // Calculate product_qty (ups * packet_size)
      

        return response()->json([
            'item_id' => $product->item_id, // ID from product_masters
            'item_code' => $itemCode, // Item Code from item_masters
            'ups' => $product->ups,
            'lam_size' => $product->lam_size,
            'curr_size' => $product->curr_size,
            'uv' => $product->uv,
            'simple' => $product->simple,
            'spot' => $product->spot,
            'color_no' => $product->color_no,
            'descr' => $product->descr,
            'file_path' => $product->file_path,
            'packet_size' => $packetSize, 
          
        ]);
    } catch (\Exception $e) {
        Log::error('Error fetching product details: ' . $e->getMessage());
        return response()->json(['error' => 'Something went wrong'], 500);
    }
}

public function getProducts($customerId)
{
    $products = DB::table('product_master')
                ->where('aid', $customerId)
                ->select('id', 'prod_name')
                ->groupBy('id', 'prod_name') // Add prod_name to GROUP BY
                ->get();
    
    return response()->json($products);
}

public function fetchRate(Request $request)
{
    $custom = ProcessSection::find($request->id);
    
    if ($custom) {
        return response()->json([
            'success' => true,
            'rate' => $custom->rate, 
        ]);
    } else {
        return response()->json(['success' => false]);
    }
}




public function store(Request $request)
{
    $filteredBoxItems = [];
    foreach ($request->box_item as $index => $itemId) {
        if (!empty($itemId)) {
            $filteredBoxItems[] = [
                'item' => $itemId,
                'length' => $request->box_length[$index],
                'width' => $request->box_width[$index],
                'qty' => $request->box_qty[$index],
            ];
        }
    }

    $validated = $request->validate([
        'job_type' => 'required|string',
        'aid' => 'required|exists:account_masters,id',
        'account' => 'required|exists:product_master,id',
        'packets' => 'required|numeric',
        'delivery_date' => 'required|date',
        'department_name' => 'required|array',
        'department_name.*' => 'exists:employee_type_details,department_id',
        'designation_sup' => 'required|array',
        'employee_sup' => 'required|array',
        'batch_no' => 'sometimes|array',
        'batch_qty' => 'sometimes|array',
        'batch_qty.*' => 'numeric',
        'box_item' => 'required|array',
        'box_item.*' => 'required|numeric',
        'box_width' => 'required|array',
        'box_width.*' => 'required|numeric',
        'box_length' => 'required|array',
        'box_length.*' => 'required|numeric',
        'box_qty' => 'required|array',
        'box_qty.*' => 'numeric|min:0',
    ]);

    DB::beginTransaction();

    try {
        $newVNo = (JobDetail::max('v_no') ?? 0) + 1;

        $baseData = [
            'v_no' => $newVNo,
            'prepared_by' => auth()->user()->name,
            'job_type' => $request->job_type,
            'job_status' => 'Pending',
            'aid' => $request->aid,
            'product_id' => $request->account,
            'packets' => $request->packets,
            'product_qty' => $request->product_qty,
            'delivery_date' => $request->delivery_date,
            'sum_batch_no' => $request->sum_batch_no,
            'custom_descr' => $request->custom_descr,
            'date' => $request->date,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $createdJobDetails = [];

        // Get counts for all data types
        $deptCount = count($request->department_name);
        $batchCount = max(
            count($request->batch_no ?? []),
            count($request->batch_qty ?? [])
        );
        $boxCount = count($request->box_item);
        
        // Determine how many records we need to create
        $maxCount = max($deptCount, $batchCount, $boxCount);

        for ($index = 0; $index < $maxCount; $index++) {
            $jobData = array_merge($baseData, [
                'department_name' => $request->department_name[$index] ?? null,
                'designation_sup' => $request->designation_sup[$index] ?? null,
                'employee_sup' => $request->employee_sup[$index] ?? null,
                'batch_no' => $request->batch_no[$index] ?? null,
                'batch_qty' => $request->batch_qty[$index] ?? null,
            ]);

            // Add box item data if it exists at this index
            if (isset($request->box_item[$index])) {
                $jobData['box_item'] = $request->box_item[$index];
                $jobData['box_width'] = $request->box_width[$index];
                $jobData['box_length'] = $request->box_length[$index];
                $jobData['box_qty'] = $request->box_qty[$index];
            }

            // Special handling for Cutting department
            if (isset($request->department_name[$index]) && $request->department_name[$index] == 14) {
                $jobData = array_merge($jobData, [
                    'length' => json_encode($request->length ?? []),
                    'width' =>  json_encode($request->width ?? []),
                    'no_of_cut' => json_encode($request->no_of_cut ?? []),
                    'department_Process' => json_encode($request->department_Process ?? []),
                ]);
            }

            $jobDetail = JobDetail::create($jobData);
            $createdJobDetails[] = $jobDetail;
        }

        DB::commit();
        return redirect()->route('job.report')->with('success', 'Job sheet created successfully!');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Error creating job sheet: ' . $e->getMessage());
    }
}

public function report(Request $request)
{
    $query = JobDetail::query()->orderBy('created_at', 'desc');

    // Apply filters (unchanged)
    if ($request->has('start_date') && $request->start_date) {
        $query->whereDate('created_at', '>=', $request->start_date);
    }

    if ($request->has('end_date') && $request->end_date) {
        $query->whereDate('created_at', '<=', $request->end_date);
    }

    if ($request->has('v_no') && $request->v_no) {
        $query->where('v_no', $request->v_no);
    }

    if ($request->has('aid') && $request->aid) {
        $query->where('aid', $request->aid);
    }

    if ($request->has('product_id') && $request->product_id) {
        $query->where('product_id', $request->product_id);
    }

    $jobDetails = $query->get();
    $vehicleNumbers = JobDetail::select('v_no')->distinct()->pluck('v_no');

    // Get all unique IDs (unchanged)
    $customerIds = $jobDetails->pluck('aid')->unique()->filter()->values();
    $productIds = $jobDetails->pluck('product_id')->unique()->filter()->values();
    $departmentIds = $jobDetails->pluck('department_name')->unique()->filter()->values();
    $designationIds = $jobDetails->pluck('designation_sup')->unique()->filter()->values();
    $employeeCnicNos = $jobDetails->pluck('employee_sup')->unique()->filter()->values();

    // Get all unique box_item IDs
    $boxItemIds = $jobDetails->pluck('box_item')
        ->filter()
        ->unique()
        ->values();

    // Preload data (unchanged)
    $customers = DB::table('account_masters')
        ->whereIn('id', $customerIds)
        ->get()
        ->keyBy('id');

    $products = DB::table('product_master')
        ->whereIn('id', $productIds)
        ->get()
        ->keyBy('id');
    
    $employeeTypeDetails = DB::table('employee_type_details')
        ->whereIn('id', $departmentIds)
        ->orWhereIn('id', $designationIds)
        ->orWhereIn('cnic_no', $employeeCnicNos)
        ->get();

    // Create mappings (unchanged)
    $departmentMap = $employeeTypeDetails->pluck('department_name', 'department_id');
    $designationMap = $employeeTypeDetails->pluck('designation_name', 'designation_id');
    $employeeMap = $employeeTypeDetails->pluck('employee_name', 'cnic_no');

    $itemIds = collect($products)->pluck('item_id')->unique()->filter()->values();
    $items = DB::table('item_masters')
        ->whereIn('id', $itemIds)
        ->get()
        ->keyBy('id');

    // Load box items from item_masters
    $boxItems = DB::table('item_masters')
        ->whereIn('id', $boxItemIds)
        ->get()
        ->keyBy('id');

    $countryIds = collect($products)->pluck('country_id')->unique()->filter()->values();
    $countries = DB::table('countries')
        ->whereIn('id', $countryIds)
        ->get()
        ->keyBy('id');

    // Group job details by v_no with all related data
    $groupedJobDetails = $jobDetails->groupBy('v_no')->map(function ($group) use ($customers, $products, $items, $countries, $departmentMap, $designationMap, $employeeMap, $boxItems) {
        $firstItem = $group->first();
        
        $customer = $customers[$firstItem->aid] ?? null;
        $product = $products[$firstItem->product_id] ?? null;
        $item = $product && $product->item_id ? ($items[$product->item_id] ?? null) : null;
        $country = $product && $product->country_id ? ($countries[$product->country_id] ?? null) : null;

        // Get all unique departments, designations, and employees for this v_no
        $departments = $group->map(function($item) use ($departmentMap) {
            return $departmentMap[$item->department_name] ?? $item->department_name ?? 'N/A';
        })->filter()->values()->all();

        $designations = $group->map(function($item) use ($designationMap) {
            return $designationMap[$item->designation_sup] ?? $item->designation_sup ?? 'N/A';
        })->filter()->values()->all();

        $employees = $group->map(function($item) use ($employeeMap) {
            return $employeeMap[$item->employee_sup] ?? $item->employee_sup ?? 'N/A';
        })->filter()->values()->all();

        $batchInfo = $group->map(function($item) {
            return [
                'batch_no' => $item->batch_no ?? 'N/A',
                'batch_qty' => $item->batch_qty ?? 'N/A',
                'job_status' => $item->job_status ?? 'N/A',
            ];
        })->all();

        $boxLengths = $group->pluck('box_length')->filter()->values()->all();
        $boxWidths = $group->pluck('box_width')->filter()->values()->all();
        $boxQtys = $group->pluck('box_qty')->filter()->values()->all();
        
        // Map box_item IDs to item_codes
        $boxItemCodes = $group->pluck('box_item')->map(function($itemId) use ($boxItems) {
            return $itemId ? ($boxItems[$itemId]->item_code ?? $itemId) : null;
        })->filter()->values()->all();

        return [
            'v_no' => $firstItem->v_no,
            'prepared_by' => $firstItem->prepared_by ?? 'N/A',
            'job_type' => $firstItem->job_type ?? 'N/A',
            'aid' => $firstItem->aid,
            'product_id' => $firstItem->product_id,
            
            // Updated to arrays
            'departments' => $departments,
            'designations' => $designations,
            'employees' => $employees,
            
            'packets' => $firstItem->packets,
            'batch_info' => $batchInfo,
            'product_name' => $product->prod_name ?? 'N/A',
            'item_id' => $product->item_id ?? 'N/A',
            'item_code' => $item->item_code ?? 'N/A',
            'delivery_date' => $firstItem->delivery_date,
            'department_Process' => $firstItem->department_process ?? 'N/A',
            'length' => $firstItem->length,
            'width' => $firstItem->width,
            'no_of_cut' => $firstItem->no_of_cut,
            'custom_descr' => $firstItem->custom_descr ?? 'N/A',
            'job_status' => $firstItem->job_status ?? 'N/A',
            'account_title' => $customer->title ?? 'N/A',
            'created_at' => $firstItem->created_at,
            'product_length' => $product->length ?? 'N/A',
            'product_width' => $product->width ?? 'N/A',
            'product_grammage' => $product->grammage ?? 'N/A',
            'product_ups' => $product->ups ?? 'N/A',
            'product_color' => $product->color_no ?? 'N/A',
            'product_country' => $country->country_name ?? 'N/A',
            'product_country_id' => $product->country_id ?? 'N/A',
            'product_lam_size' => $product->lam_size ?? 'N/A',
            'product_curr_size' => $product->curr_size ?? 'N/A',
            'product_simple' => $product->simple ?? 'N/A',
            'product_spot' => $product->spot ?? 'N/A',
            'product_description' => $product->descr ?? 'N/A',
            'product_img' => $product->file_path ?? 'N/A',
            
            'box_length' => $boxLengths,
            'box_width' => $boxWidths,
            'box_qty' => $boxQtys,
            'box_item' => $boxItemCodes, // Now contains item_codes instead of IDs
            'box_item_ids' => $group->pluck('box_item')->filter()->values()->all(), // Keep original IDs if needed
        ];
    });

    // Get unique customers and products for dropdowns (unchanged)
    $uniqueCustomers = $customers->pluck('title', 'id');
    $uniqueItems = $products->pluck('prod_name', 'id');

    return view('job_sheet.index', [
        'groupedJobDetails' => $groupedJobDetails,
        'vehicleNumbers' => $vehicleNumbers,
        'uniqueCustomers' => $uniqueCustomers,
        'uniqueItems' => $uniqueItems,
        'accountTitles' => $uniqueCustomers,
    ]);
}


public function destroy(Request $request)
{
    // Validate the incoming request to ensure 'v_no' is present
    $request->validate([
        'v_no' => 'required|integer',
    ]);

    // Retrieve the 'v_no' from the request
    $vNo = $request->input('v_no');

    // Delete all records with the specified 'v_no'
    JobDetail::where('v_no', $vNo)->delete();
    Solna::where('v_no', $vNo)->delete();
    DyeJob::where('v_no', $vNo)->delete();
    Lamination::where('v_no', $vNo)->delete();

    // Redirect back with a success message
    return back()->with('success', "Job details with V No {$vNo} deleted successfully!");
}
    
    
    
public function update(Request $request, $v_no)
{
    // First, update the existing rows where v_no and department_name match
    $updatedRows = JobDetail::where('v_no', $v_no)
        ->where('department_name', 14)
        ->update([
            'box_employee' => $request->input('box_employee'),
            'box_machine' => $request->input('box_machine'),
            'box_date_boxboard' => $request->input('box_date_boxboard'),
            'box_status' => $request->input('box_status'),
        ]);
        
    $updatedRows = JobDetail::where('v_no', $v_no)
        ->update([
            'job_status' => $request->input('job_status'),
        ]);


// Department 23 25


$solnaIds = is_array($request->solna_id) ? $request->solna_id : [$request->solna_id];
$solnaMan = is_array($request->solna_man) ? $request->solna_man : [$request->solna_man];
$solnaMachine = is_array($request->solna_machine) ? $request->solna_machine : [$request->solna_machine];
$solnaDateMachine = is_array($request->solna_date_machine) ? $request->solna_date_machine : [$request->solna_date_machine];
$solnaManImp = is_array($request->solna_man_impression) ? $request->solna_man_impression : [$request->solna_man_impression];
$solnaManWaste = is_array($request->solna_man_waste) ? $request->solna_man_waste : [$request->solna_man_waste];

$solnaDateHelper = is_array($request->solna_date_helper) ? $request->solna_date_helper : [$request->solna_date_helper];
$solnaMachineHelper = is_array($request->solna_machine_helper) ? $request->solna_machine_helper : [$request->solna_machine_helper];
$solnaHelper = is_array($request->solna_helper) ? $request->solna_helper : [$request->solna_helper];
$solnaHelperImp = is_array($request->solna_helper_impression) ? $request->solna_helper_impression : [$request->solna_helper_impression];
$solnaHelperWaste = is_array($request->solna_helper_waste) ? $request->solna_helper_waste : [$request->solna_helper_waste];

$inkItem = is_array($request->solna_ink) ? $request->solna_ink : [$request->solna_ink];
$inkQty = is_array($request->ink_qty) ? $request->ink_qty : [$request->ink_qty];

// Get the maximum count from all relevant arrays (excluding ink items)
$max = max(
    count($solnaIds),
    count($solnaMan),
    count($solnaMachine),
    count($solnaManImp),
    count($solnaManWaste),
    count($solnaDateHelper),
    count($solnaDateMachine),
    count($solnaMachineHelper),
    count($solnaHelper),
    count($solnaHelperImp),
    count($solnaHelperWaste)
);

// First loop through and collect all department IDs used
$departments = [];

for ($i = 0; $i < $max; $i++) {
    $cnicDepartment = explode('|', $solnaMan[$i] ?? '|');
    $department = $cnicDepartment[1] ?? null;
    if ($department) {
        $departments[] = $department;
    }
}

$departments = array_unique($departments);

// Delete old records based on v_no and department_id
foreach ($departments as $deptId) {
    Solna::where('v_no', $request->v_no)
         ->where('department_id', $deptId)
         ->delete();
}

// Insert new records for departments (23 and 25)
for ($i = 0; $i < $max; $i++) {
    $cnicDepartment = explode('|', $solnaMan[$i] ?? '|');
    $solnaManCnic = $cnicDepartment[0] ?? null;
    $department = $cnicDepartment[1] ?? null;

    // Check if department is 23 or 25 before inserting
    if (!in_array((int)$department, [23, 25])) {
        continue;
    }

    $solna = new Solna();
    $solna->v_no = $request->v_no;
    $solna->department_id = $department;

    $solna->solna_man = $solnaManCnic;
    $solna->solna_date_machine = $solnaDateMachine[$i] ?? null;
    $solna->solna_machine = $solnaMachine[$i] ?? null;
    $solna->solna_man_impression = $solnaManImp[$i] ?? null;
    $solna->solna_man_waste = $solnaManWaste[$i] ?? null;

    $solna->solna_date_helper = $solnaDateHelper[$i] ?? null;
    $solna->solna_machine_helper = $solnaMachineHelper[$i] ?? null;
    $solna->solna_helper = $solnaHelper[$i];
    $solna->solna_helper_impression = $solnaHelperImp[$i] ?? null;
    $solna->solna_helper_waste = $solnaHelperWaste[$i] ?? null;

    // Add ink data if available at this index
    if (isset($inkItem[$i]) && isset($inkQty[$i])) {
        $solna->ink_item = $inkItem[$i];
        $solna->ink_qty = $inkQty[$i];
    }

    $solna->manual_impression = $request->manual_impression;
    $solna->helper_impression = $request->helper_impression;
    $solna->solna_total_job_sheet_impression = $request->solna_total_job_sheet_impression;
    $solna->solna_supervisor_impression = $request->solna_supervisor_impression;

    $solna->created_at = Carbon::now();
    $solna->updated_at = Carbon::now();

    $solna->save();
}

// Handle any additional ink entries that didn't have corresponding lamination data
$inkMax = max(count($inkItem), count($inkQty));
for ($i = $max; $i < $inkMax; $i++) {
    if (isset($inkItem[$i]))  {
            $solna = new Solna();
            $solna->v_no = $request->v_no;
            $solna->department_id = $department;

            $solna->ink_item = $inkItem[$i];
            $solna->ink_qty = $inkQty[$i];

            $solna->manual_impression = $request->manual_impression;
            $solna->helper_impression = $request->helper_impression;
            $solna->solna_total_job_sheet_impression = $request->solna_total_job_sheet_impression;
            $solna->solna_supervisor_impression = $request->solna_supervisor_impression;
    
            $solna->created_at = Carbon::now();
            $solna->updated_at = Carbon::now();
            $solna->save();
        }
    }




// FOR DEPARTMENT 31 AND 28
$dyeIds = is_array($request->dye_id) ? $request->dye_id : [$request->dye_id];
$dyeMan = is_array($request->dye_man) ? $request->dye_man : [$request->dye_man];
$dyeMachine = is_array($request->dye_machine) ? $request->dye_machine : [$request->dye_machine];
$dyeDateMachine = is_array($request->dye_date_machine) ? $request->dye_date_machine : [$request->dye_date_machine];
$dyeManImp = is_array($request->dye_man_impression) ? $request->dye_man_impression : [$request->dye_man_impression];
$dyeManWaste = is_array($request->dye_man_waste) ? $request->dye_man_waste : [$request->dye_man_waste];

$dyeDateHelper = is_array($request->dye_date_helper) ? $request->dye_date_helper : [$request->dye_date_helper];
$dyeMachineHelper = is_array($request->dye_machine_helper) ? $request->dye_machine_helper : [$request->dye_machine_helper];
$dyeHelper = is_array($request->dye_helper) ? $request->dye_helper : [$request->dye_helper];
$dyeHelperImp = is_array($request->dye_helper_impression) ? $request->dye_helper_impression : [$request->dye_helper_impression];
$dyeHelperWaste = is_array($request->dye_helper_waste) ? $request->dye_helper_waste : [$request->dye_helper_waste];



// Get the maximum count from all relevant arrays
$max = max(
    count($dyeIds),
    count($dyeMan),
    count($dyeManImp),
    count($dyeManWaste),
    count($dyeHelper),
    count($dyeHelperImp),
    count($dyeHelperWaste),
    count($dyeMachine),
    count($dyeMachineHelper),
    count($dyeDateMachine),
    count($dyeDateHelper),
);

// First loop through and collect all department IDs used
$departments = [];

for ($i = 0; $i < $max; $i++) {
    $cnicDepartment = explode('|', $dyeMan[$i] ?? '|');
    $department = $cnicDepartment[1] ?? null;
    if ($department) {
        $departments[] = $department;
    }
}

$departments = array_unique($departments);

// Delete old records based on v_no and department_id
foreach ($departments as $deptId) {
    DyeJob::where('v_no', $request->v_no)
         ->where('department_id', $deptId)
         ->delete();
}

// Insert new records for departments (31 and 28)
for ($i = 0; $i < $max; $i++) {
    $cnicDepartment = explode('|', $dyeMan[$i] ?? '|');
    $dyeManCnic = $cnicDepartment[0] ?? null;
    $department = $cnicDepartment[1] ?? null;

    // Check if department is 28 or 31 before inserting
    if (!in_array((int)$department, [28, 31])) {
        continue;
    }

    $dye = new DyeJob();
    $dye->v_no = $request->v_no;
    $dye->department_id = $department;

    $dye->dye_man = $dyeManCnic;
    $dye->dye_machine = $dyeMachine[$i] ?? null;
    $dye->dye_date_machine = $dyeDateMachine[$i] ?? null;
    $dye->dye_man_impression = $dyeManImp[$i] ?? null;
    $dye->dye_man_waste = $dyeManWaste[$i] ?? null;

    $dye->dye_date_helper = $dyeDateHelper[$i] ?? null;
    $dye->dye_machine_helper = $dyeMachineHelper[$i] ?? null;
    $dye->dye_helper = $dyeHelper[$i] ?? null;
    $dye->dye_helper_impression = $dyeHelperImp[$i] ?? null;
    $dye->dye_helper_waste = $dyeHelperWaste[$i] ?? null;

    $dye->total_manual_impression = $request->total_manual_impression;
    $dye->total_helper_impression = $request->total_helper_impression;

    $dye->created_at = Carbon::now();
    $dye->updated_at = Carbon::now();

    $dye->save();
}



// FOR DEPARTMENT 22 - LAMINATION

// Get all input arrays, defaulting to empty array if not present
// Get all input arrays
$laminationMachine = $request->lamination_machine ?? [];
$laminationDateMachine = $request->lamination_date_machine ?? [];
$laminationMan = $request->lamination_man ?? [];
$laminationManImp = $request->lamination_man_impression ?? [];
$laminationManWaste = $request->lamination_man_waste ?? [];
$glueItem = $request->lamination_glue ?? [];
$glueQty = $request->glue_qty ?? [];
$laminationItem = $request->lamination_item ?? [];
$laminationQty = $request->lamination_qty ?? [];
$size = $request->size ?? []; // This is coming from name="size[]"

// Convert to arrays if they aren't already
if (!is_array($laminationDateMachine)) $laminationDateMachine = [$laminationDateMachine];
if (!is_array($laminationMachine)) $laminationMachine = [$laminationMachine];
if (!is_array($laminationMan)) $laminationMan = [$laminationMan];
if (!is_array($laminationManImp)) $laminationManImp = [$laminationManImp];
if (!is_array($laminationManWaste)) $laminationManWaste = [$laminationManWaste];
if (!is_array($glueItem)) $glueItem = [$glueItem];
if (!is_array($glueQty)) $glueQty = [$glueQty];
if (!is_array($laminationItem)) $laminationItem = [$laminationItem];
if (!is_array($laminationQty)) $laminationQty = [$laminationQty];
if (!is_array($size)) $size = [$size];

// Get the maximum count of relevant arrays
$max = max(
    count($laminationMan),
    count($laminationManImp),
    count($laminationManWaste),
    count($laminationMachine),
    count($laminationDateMachine),
);

// Delete all existing lamination records for this v_no and department 22
Lamination::where('v_no', $request->v_no)
     ->where('department_id', 22)
     ->delete();

// Process each lamination entry
for ($i = 0; $i < $max; $i++) {
    // Extract CNIC and department from lamination_man
    $cnicDepartment = explode('|', $laminationMan[$i] ?? '|');
    $laminationManCnic = $cnicDepartment[0] ?? null;
    $department = $cnicDepartment[1] ?? null;

    // Only process if department is 22
    $department = isset($cnicDepartment[1]) ? (int)$cnicDepartment[1] : null;
    if ($department !== 22) {
        continue;
    }
    
    // Create new lamination record
    $lamination = new Lamination();
    $lamination->v_no = $request->v_no;
    $lamination->department_id = 22; // Force department to 22
    
    $lamination->lamination_man = $laminationManCnic;
    $lamination->lamination_date_machine = $laminationDateMachine[$i] ?? null;
    $lamination->lamination_machine = $laminationMachine[$i] ?? null;
    $lamination->lamination_man_impression = $laminationManImp[$i] ?? null;
    $lamination->lamination_man_waste = $laminationManWaste[$i] ?? null;

    // Add glue data if available at this index
    if (isset($glueItem[$i]) && isset($glueQty[$i])) {
        $lamination->glue_item = $glueItem[$i];
        $lamination->glue_qty = $glueQty[$i];
    }

    // Add lamination item data if available at this index
    if (isset($laminationItem[$i])) {
        $lamination->lamination_item = $laminationItem[$i];
        $lamination->lamination_qty = $laminationQty[$i] ?? null;
        $lamination->lamination_size = $size[$i] ?? null; // Map size to lamination_size
    }

    $lamination->lamination_manual_impression = $request->lamination_manual_impression;
    $lamination->created_at = Carbon::now();
    $lamination->updated_at = Carbon::now();
    
    $lamination->save();
}

// Handle any additional glue entries that didn't have corresponding lamination data
$glueMax = max(count($glueItem), count($glueQty));
for ($i = $max; $i < $glueMax; $i++) {
    if (isset($glueItem[$i])) {
        $lamination = new Lamination();
        $lamination->v_no = $request->v_no;
        $lamination->department_id = 22;
        
        $lamination->glue_item = $glueItem[$i] ?? null;
        $lamination->glue_qty = $glueQty[$i] ?? null;
        $lamination->lamination_manual_impression = $request->lamination_manual_impression;
        
        $lamination->created_at = Carbon::now();
        $lamination->updated_at = Carbon::now();
        
        $lamination->save();
    }
}

// Handle any additional lamination item entries that didn't have corresponding lamination data
$laminationItemMax = max(count($laminationItem), count($laminationQty), count($size));
for ($i = $max; $i < $laminationItemMax; $i++) {
    if (isset($laminationItem[$i])) {
        $lamination = new Lamination();
        $lamination->v_no = $request->v_no;
        $lamination->department_id = 22;
        
        $lamination->lamination_item = $laminationItem[$i] ?? null;
        $lamination->lamination_qty = $laminationQty[$i] ?? null;
        $lamination->lamination_size = $size[$i] ?? null; // Map size to lamination_size
        $lamination->lamination_manual_impression = $request->lamination_manual_impression;
        
        $lamination->created_at = Carbon::now();
        $lamination->updated_at = Carbon::now();
        
        $lamination->save();
    }
}

// CORRUGATIONS 


// Normalize all input arrays with default empty array fallback
$corrugationDateMachine = $request->corrugation_date_machine ?? [];
$corrugationBox = $request->corrugation_box ?? [];
$corrugationPacking = $request->corrugation_packing ?? [];
$corrugationTotalBox = $request->corrugation_total_boxes ?? [];
$shipperItem = $request->corrugation_shipper ?? [];  // Note: using corrugation_shipper from blade
$shipperQty = $request->shipper_qty ?? [];

// Convert to arrays if they aren't already
if (!is_array($corrugationDateMachine)) $corrugationDateMachine = [$corrugationDateMachine];
if (!is_array($corrugationBox)) $corrugationBox = [$corrugationBox];
if (!is_array($corrugationPacking)) $corrugationPacking = [$corrugationPacking];
if (!is_array($corrugationTotalBox)) $corrugationTotalBox = [$corrugationTotalBox];
if (!is_array($shipperItem)) $shipperItem = [$shipperItem];
if (!is_array($shipperQty)) $shipperQty = [$shipperQty];

// Get the maximum count of all relevant arrays
$max = max(
    count($corrugationDateMachine),
    count($corrugationBox),
    count($corrugationPacking),
    count($corrugationTotalBox),
    count($shipperItem),
    count($shipperQty)
);

// Delete all existing corrugation records for this v_no and department 13
Corrugation::where('v_no', $request->v_no)
    ->where('department_id', 13)
    ->delete();

// Process all entries
for ($i = 0; $i < $max; $i++) {
    $data = [
        'v_no' => $request->v_no,
        'department_id' => 13,
        
        // Corrugation data
        'corrugation_date_machine' => $corrugationDateMachine[$i] ?? null,
        'corrugation_box' => $corrugationBox[$i] ?? null,
        'corrugation_packing' => $corrugationPacking[$i] ?? null,
        'corrugation_total_boxes' => $corrugationTotalBox[$i] ?? null,
        
        // Shipper data (mapping from corrugation_shipper to shipper_item)
        'shipper_item' => $shipperItem[$i] ?? null,
        'shipper_qty' => $shipperQty[$i] ?? null,
        
        // Common fields
        'corrugation_item_type' => $request->corrugation_item_type,
        'po_order_qty' => $request->po_order_qty,
        'finish_product_qty' => $request->finish_product_qty,
        'created_at' => now(),
        'updated_at' => now()
    ];

    // Add manual impression if available
    if ($request->has('corrugation_manual_impression')) {
        $data['corrugation_manual_impression'] = $request->corrugation_manual_impression;
    }

    Corrugation::create($data);
}


    // Breaking
 
 
// Normalize input arrays
$departmentName = $request->department_name;
    
// Find the department in your departmentsections table
$department = DepartmentSection::where('name', $departmentName)->first();
    
// Normalize input arrays
$breakingDateMachine = is_array($request->breaking_date_machine) 
    ? $request->breaking_date_machine 
    : [$request->breaking_date_machine];
    
$breakingContractor = is_array($request->breaking_contractor) 
    ? $request->breaking_contractor 
    : [$request->breaking_contractor];
    
$breakingImpression = is_array($request->breaking_impression) 
    ? $request->breaking_impression 
    : [$request->breaking_impression];
    
$breakingWaste = is_array($request->breaking_waste) 
    ? $request->breaking_waste 
    : [$request->breaking_waste];

// Process contractor values to get only the first part before the pipe
$breakingContractor = array_map(function($value) {
    if ($value && strpos($value, '|') !== false) {
        return trim(explode('|', $value)[0]); // Get first part and trim whitespace
    }
    return $value;
}, $breakingContractor);

// Get max count
$max = max(
    count($breakingDateMachine),
    count($breakingContractor),
    count($breakingImpression),
    count($breakingWaste),
);

// Delete old records
Breaking::where('v_no', $request->v_no)
    ->where('department_id', 20)
    ->delete();

// Insert new records
for ($i = 0; $i < $max; $i++) {
    Breaking::create([
        'v_no' => $request->v_no,
        'department_id' => 20,
        'breaking_date_machine' => $breakingDateMachine[$i] ?? null,
        'breaking_contractor' => $breakingContractor[$i] ?? null,
        'breaking_impression' => $breakingImpression[$i] ?? null,
        'breaking_waste' => $breakingWaste[$i] ?? null,
        'breaking_total_impression' => $request->breaking_total_impression,
        'breaking_total_waste' => $request->breaking_total_waste,
        'created_at' => now(),
        'updated_at' => now()
    ]);
}



  


return redirect()->route('job.report')->with('success', "Data for V No {$request->v_no} updated successfully!");
}
    
    
}