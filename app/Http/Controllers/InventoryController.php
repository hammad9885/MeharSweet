<?php
namespace App\Http\Controllers;

    use App\Models\Party;
    use App\Models\BoxBoard;
    use App\Models\ConsumptionDetail;
    use App\Models\ConsumptionMaster;
    use App\Models\ItemType;
    use App\Models\ItemMaster;
    use App\Models\ItemLog;
    use App\Models\ProductionMaster;
    use App\Models\PurchaseDetail;
    use App\Models\PurchaseInvoice;
    use App\Models\PurchaseReturnInvoice;
    use App\Models\SaleInvoices;
    use App\Models\SaleReturnInvoice;
    use App\Models\SaleReturnMaster;
    use App\Models\SaleType;
    use App\Models\Units;
    use App\Models\Mold;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;

    class InventoryController extends Controller
    {
        // 🔹 List Item Masters
        public function index_itemmaster(Request $request)
        {
            $companyId = Auth::user()->cid;

            $item_code = $request->input('item_code');
            $item_type = $request->input('item_type');
            $type_id   = $request->input('type_id');

            $query = ItemMaster::where('cid', $companyId)->with('itemunits');

            if ($item_code) {
                $query->where('item_code', $item_code);
            }

            if ($type_id) {
                $query->where('type_id', $type_id);
            }
            if ($item_type) {
                $query->where('item_type', $item_type);
            }

            $itemmasters = $query->with('itemTypes')->get();

            $items     = ItemMaster::where('cid', $companyId)->pluck('item_code', 'item_code')->unique();
            $itemTypes = ItemType::where('cid', $companyId)->pluck('type_title', 'id')->unique();
        
            return \Inertia\Inertia::render('Inventory/ItemMaster', [
                'itemmasters' => $itemmasters,
                'items'       => $items,
                'itemTypes'   => $itemTypes,
                'filters'     => $request->only(['item_code', 'type_id', 'item_type']),
            ]);
        }

        // 🔹 List Item Types (Raw Material)
        public function index_itemtype()
        {
            $companyId = Auth::user()->cid;
            $itemtypes = ItemType::where('cid', $companyId)->where('item_type', 'fp')->get();

            return view('inventory.list_item_type', get_defined_vars());
        }

        // 🔹 List Product Types (Finished Product)
        public function index_producttype()
        {
            $companyId = Auth::user()->cid;
            $itemtypes = ItemType::where('cid', $companyId)->where('item_type', 'rp')->get();

            return view('inventory.list_product_type', get_defined_vars());
        }

        // 🔹 Create Item Master (form)
        public function createitemmaster()
        {
            $companyId = Auth::user()->cid;
            
            // Auto-generate next sequence number
            $lastItem = ItemMaster::where('cid', $companyId)->latest('id')->first();
            $nextSeq = str_pad(($lastItem->id ?? 0) + 1, 4, '0', STR_PAD_LEFT);

            $itemtypes = ItemType::where('cid', $companyId)->get();
            $itemUnits = Units::where('cid', $companyId)->get();
            $saleType = SaleType::where('cid', $companyId)->get();
            
            return view('inventory.create_item_master', get_defined_vars());
        }

        // 🔹 Create Item Type (form)
        public function createitemtype()
        {
            $companyId  = Auth::user()->cid;
            $itemMasters = ItemMaster::where('cid', $companyId)->get();

            return view('inventory.create_item_type', get_defined_vars());
        }

        // 🔹 Create Product Type (form)
        public function createproducttype()
        {
            $companyId  = Auth::user()->cid;
            $itemMasters = ItemMaster::where('cid', $companyId)->get();

            return view('inventory.create_product_type', get_defined_vars());
        }

        // 🔹 Store BoxBoard
        public function boxboard(Request $request)
        {
            $companyId = Auth::user()->cid;

            BoxBoard::create([
                'cid'          => $companyId,
                'name'         => $request->name,
                'lenght'       => $request->lenght,
                'width'        => $request->width,
                'gsm'          => $request->gsm,
                'no_of_packets'=> $request->no_of_packets,
                'party_name'   => $request->party_name,
                'gate_pass_in' => $request->gate_pass_in,
            ]);

            return redirect()->back()->with('success', 'BoxBoard created successfully.');
        }

        // 🔹 Store Item Master
    public function itemmaster(Request $request)
    {
        $companyId = Auth::user()->cid;
// dd($request->all());
    $request->validate([
        'item_code' => 'required|string|max:255',
        'type_id'   => 'required|exists:item_types,id',
        'unit_id'   => 'required|exists:units,id',
        'purchase'  => 'nullable|numeric|min:0',
        'sale_rate' => 'nullable|numeric|min:0',
       


        // ✅ NEW FIELDS (ADD HERE)
        'urdu_title' => 'nullable|string',
     
        'set_qty'    => 'nullable|integer',
        'seq_number' => 'nullable|string',
    'auto_load_on_consumption' => 'nullable',
    'load_posting' => 'nullable',
    ]);

    ItemMaster::create([
        'cid'       => $companyId,
        'item_code' => $request->item_code,
        'type_id'   => $request->type_id,
        'item_type' => $request->item_type,
        'unit_id'   => $request->unit_id,
        'purchase'  => $request->purchase,
        'sale_rate' => $request->sale_rate,

  
        'cost_price'=> $request->cost_price,

        // ✅ NEW FIELDS
        'urdu_title' => $request->urdu_title,
        'set_qty'    => $request->set_qty,
        'seq_number' => $request->seq_number,
        'auto_load_on_consumption' => $request->has('auto_load_on_consumption'),
        'load_posting' => $request->has('load_posting'),
    ]);

        return redirect()->route('inventory.itemmaster.list')
            ->with('success', 'Item created successfully.');
    }


        // 🔹 Edit Item Master
    public function itemmasteredit($id)
    {
        $companyId   = Auth::user()->cid;
        $itemMasters = ItemMaster::where('cid', $companyId)->findOrFail($id);
            $itemtypes = ItemType::where('cid', $companyId)->get();
        $itemUnits   = Units::where('cid', $companyId)->get();
        $saleType    = SaleType::where('cid', $companyId)->get();

        return view('inventory.edit_item_master', compact('itemMasters', 'itemtypes', 'itemUnits', 'saleType'));
    }
        // 🔹 Update Item Master
        public function itemmasterupdate(Request $request, $id)
    {
        $companyId = Auth::user()->cid;

        $request->validate([
        'item_code' => 'required|string|max:255',
        'type_id'   => 'required|exists:item_types,id',
        'unit_id'   => 'required|exists:units,id',
        'purchase'  => 'nullable|numeric|min:0',
        'sale_rate' => 'nullable|numeric|min:0',

        'item_type' => 'required|string|max:255',

        // ✅ NEW FIELDS (ADD HERE)
        'urdu_title' => 'nullable|string',
        'set_qty'    => 'nullable|integer',
        'seq_number' => 'nullable|string',
    'auto_load_on_consumption' => 'nullable',
    'load_posting' => 'nullable',
    ]);

        $itemMaster = ItemMaster::where('cid', $companyId)->findOrFail($id);
        $oldPurchase = $itemMaster->purchase;

        $itemMaster->update([
        'item_code' => $request->item_code,
        'type_id'   => $request->type_id,
        'unit_id'   => $request->unit_id,
        'purchase'  => $request->purchase,
        'sale_rate' => $request->sale_rate,
      
        'cost_price'=> $request->cost_price,
        'item_type' => $request->item_type,

        // ✅ NEW FIELDS
        'urdu_title' => $request->urdu_title,
        'set_qty'    => $request->set_qty,
        'seq_number' => $request->seq_number,
        'auto_load_on_consumption' => $request->has('auto_load_on_consumption'),
        'load_posting' => $request->has('load_posting'),
    ]);
        return redirect()->route('inventory.itemmaster.list')->with('success', 'Item Master updated successfully');
    }


        // 🔹 List Item Logs
        public function itemlogList(Request $request)
        {
            $companyId = Auth::user()->cid;

            $items = ItemLog::where('cid', $companyId)->distinct()->pluck('item_code');

            $itemLogs = ItemLog::where('cid', $companyId)
                ->when($request->item_code, function ($query) use ($request) {
                    return $query->where('item_code', $request->item_code);
                })
                ->get();

            return view('inventory.item_log', compact('itemLogs', 'items'));
        }

        // 🔹 Delete Item Master
        public function itemmasterdestroy($id)
        {
            $companyId  = Auth::user()->cid;
            $itemMaster = ItemMaster::where('cid', $companyId)->findOrFail($id);
            $production=ProductionMaster::where('cid',$companyId)->whereHas('details',function($q) use ($id){
                $q->where('item_id',$id);
            })->exists();
            $consumed=ConsumptionMaster::where('cid',$companyId)->where('item_id',$id)->exists();
            $consumedreturn=ConsumptionDetail::where('cid',$companyId)->where('item_id',$id)->exists();
            $saleInvoice=SaleInvoices::where('cid',$companyId)->where('item_id',$id)->exists();
            $saleReturnInvoice=SaleReturnInvoice::where('cid',$companyId)->where('item_id',$id)->exists();
            $purchase=PurchaseInvoice::where('cid',$companyId)->where('item_id',$id)->exists();
            $purchasereturn=PurchaseReturnInvoice::where('cid',$companyId)->where('item_id',$id)->exists();
            if($production || $saleInvoice || $saleReturnInvoice|| $purchase || $purchasereturn || $consumed || $consumedreturn){
                return redirect()->route('inventory.itemmaster.list')->with('error', 'This item is in used and cannot be deleted');
            }

            $itemMaster->delete();

            return redirect()->route('inventory.itemmaster.list')->with('success', 'Item Master deleted successfully');
        }

        // 🔹 Store Item Type
        public function itemtype(Request $request)
        {
            $companyId = Auth::user()->cid;

            $request->validate([
                'type_title' => 'required|string|max:255',
                'item_type' => 'required|string|max:255',
            ]);

            ItemType::create([
                'cid'        => $companyId,
                'type_title' => $request->type_title,
                'item_type'  => $request->item_type,
                'load_posting' => $request->has('load_posting'),
            ]);

            $routeName = ($request->item_type == 'rp') ? 'inventory.producttype.list' : 'inventory.itemtype.list';
            return redirect()->route($routeName)->with('success', 'Item/Product Type created successfully.');
        }

        // 🔹 Edit Item Type
        public function itemtypeedit($id)
        {
            $companyId = Auth::user()->cid;
            $itemtypes = ItemType::where('cid', $companyId)->findOrFail($id);

            return view('inventory.edit_item_type', get_defined_vars());
        }

        // 🔹 Edit Product Type
        public function producttypeedit($id)
        {
            $companyId = Auth::user()->cid;
            $itemtypes = ItemType::where('cid', $companyId)->findOrFail($id);

            return view('inventory.edit_product_type', get_defined_vars());
        }

        // 🔹 Update Item Type
        public function itemtypeupdate(Request $request, $id)
        {
            $companyId = Auth::user()->cid;

            $request->validate([
                'type_title' => 'required|string|max:255',
                'item_type'  => 'required|string|max:255',
            ]);

            try {
                $itemtypes = ItemType::where('cid', $companyId)->findOrFail($id);
                $itemtypes->update([
                    'type_title' => $request->input('type_title'),
                    'item_type'  => $request->input('item_type'),
                    'load_posting' => $request->has('load_posting'),
                ]);
            } catch (\Exception $e) {
                dd($e->getMessage());
            }

                $routeName = ($itemtypes->item_type == 'rp') ? 'inventory.producttype.list' : 'inventory.itemtype.list';
                return redirect()->route($routeName)->with('success', 'Item/Product Type updated successfully');
        }
    
        public function storemold(Request $request){
            $request->validate([
            'mold_name' => 'required|string|max:255',
        ]);

        // Auto-generate Mold ID again (safe)
        $last = Mold::latest()->first();
        $moldId = 'MOLD-' . str_pad(($last->id ?? 0) + 1, 4, '0', STR_PAD_LEFT);

        $total = ($request->wages ?? 0) + ($request->expense ?? 0);

        Mold::create([
            'mold_id'   => $moldId,
            'mold_name' => $request->mold_name,
            'item_type' => $request->item_type,
            'weight'    => $request->weight,
            'wages'     => $request->wages,
            'expense'   => $request->expense,
            'total'     => $total,
            'remarks'   => $request->remarks,
        ]);

        return redirect()->back()->with('success', 'Mold Added Successfully');
        
        }

        // 🔹 Delete Item Type
        public function itemtypedestroy($id)
        {
            $companyId = Auth::user()->cid;
            $itemtypes = ItemType::where('cid', $companyId)->findOrFail($id);
            $itemMaster=ItemMaster::where('type_id',$itemtypes->id)->first();
            if($itemMaster){
                return redirect()->back()->with('error','This Categry Already exist In Item Registration');
            }
            $routeName = ($itemtypes->item_type == 'rp') ? 'inventory.producttype.list' : 'inventory.itemtype.list';
            $itemtypes->delete();

            return redirect()->route($routeName)->with('success', 'Item/Product Type deleted successfully');
        }
    }
