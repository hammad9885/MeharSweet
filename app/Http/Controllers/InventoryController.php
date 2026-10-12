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
        public function createitemmaster(Request $request)
        {
            $companyId = Auth::user()->cid;
            
            // Auto-generate next sequence number
            $lastItem = ItemMaster::where('cid', $companyId)->latest('id')->first();
            $nextSeq = str_pad(($lastItem->id ?? 0) + 1, 4, '0', STR_PAD_LEFT);

            $itemtypes = ItemType::where('cid', $companyId)->get();
            if ($itemtypes->isEmpty()) {
                $itemtypes = ItemType::all();
            }
            $itemUnits = Units::where('cid', $companyId)->get();
            if ($itemUnits->isEmpty()) {
                $itemUnits = Units::all();
            }
            $saleType = SaleType::where('cid', $companyId)->get();
            
            return \Inertia\Inertia::render('Inventory/CreateEdit', [
                'nextSeq'   => $nextSeq,
                'itemtypes' => $itemtypes,
                'itemUnits' => $itemUnits,
                'item'      => null,
            ]);
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

            $request->validate([
                'name'       => 'nullable|string|max:255',
                'item_code'  => 'nullable|string|max:255',
                'type_id'    => 'required|exists:item_types,id',
                'unit_id'    => 'required|exists:units,id',
                'rate'       => 'nullable|numeric|min:0',
                'purchase'   => 'nullable|numeric|min:0',
                'sale_rate'  => 'nullable|numeric|min:0',
                'urdu_title' => 'nullable|string|max:255',
                'seq_number' => 'nullable|string|max:255',
            ]);

            $itemName = $request->input('name') ?: $request->input('item_code');
            if (empty($itemName)) {
                return redirect()->back()->withErrors(['name' => 'Name is required'])->withInput();
            }

            $rate = $request->input('rate');
            $purchase = $request->filled('rate') ? $rate : ($request->input('purchase') ?? 0);
            $saleRate = $request->filled('rate') ? $rate : ($request->input('sale_rate') ?? 0);
            $costPrice = $request->filled('rate') ? $rate : ($request->input('cost_price') ?? 0);

            // Auto-determine item_type from selected type_id
            $itemTypeModel = ItemType::find($request->type_id);
            $itemType = $request->input('item_type') ?: ($itemTypeModel ? $itemTypeModel->item_type : 'rp');

            ItemMaster::create([
                'cid'                      => $companyId,
                'item_code'                => $itemName,
                'type_id'                  => $request->type_id,
                'item_type'                => $itemType,
                'unit_id'                  => $request->unit_id,
                'purchase'                 => $purchase,
                'sale_rate'                => $saleRate,
                'cost_price'               => $costPrice,
                'urdu_title'               => $request->urdu_title,
                'seq_number'               => $request->seq_number,
                'auto_load_on_consumption' => $request->has('auto_load_on_consumption') ? 1 : 0,
                'load_posting'             => 0,
            ]);

            return redirect()->route('inventory.itemmaster.list')
                ->with('success', 'Item created successfully.');
        }

        // 🔹 Edit Item Master
        public function itemmasteredit(Request $request, $id)
        {
            $companyId   = Auth::user()->cid;
            $itemMasters = ItemMaster::where('cid', $companyId)->find($id) ?: ItemMaster::findOrFail($id);
            $itemtypes   = ItemType::where('cid', $companyId)->get();
            if ($itemtypes->isEmpty()) {
                $itemtypes = ItemType::all();
            }
            $itemUnits   = Units::where('cid', $companyId)->get();
            if ($itemUnits->isEmpty()) {
                $itemUnits = Units::all();
            }
            $saleType    = SaleType::where('cid', $companyId)->get();

            return \Inertia\Inertia::render('Inventory/CreateEdit', [
                'nextSeq'   => $itemMasters->seq_number,
                'itemtypes' => $itemtypes,
                'itemUnits' => $itemUnits,
                'item'      => $itemMasters,
            ]);
        }

        // 🔹 Update Item Master
        public function itemmasterupdate(Request $request, $id)
        {
            $companyId = Auth::user()->cid;

            $request->validate([
                'name'       => 'nullable|string|max:255',
                'item_code'  => 'nullable|string|max:255',
                'type_id'    => 'required|exists:item_types,id',
                'unit_id'    => 'required|exists:units,id',
                'rate'       => 'nullable|numeric|min:0',
                'purchase'   => 'nullable|numeric|min:0',
                'sale_rate'  => 'nullable|numeric|min:0',
                'urdu_title' => 'nullable|string|max:255',
                'seq_number' => 'nullable|string|max:255',
            ]);

            $itemMaster = ItemMaster::where('cid', $companyId)->find($id) ?: ItemMaster::findOrFail($id);

            $itemName = $request->input('name') ?: $request->input('item_code');
            if (empty($itemName)) {
                return redirect()->back()->withErrors(['name' => 'Name is required'])->withInput();
            }

            $rate = $request->input('rate');
            $purchase = $request->filled('rate') ? $rate : ($request->input('purchase') ?? $itemMaster->purchase);
            $saleRate = $request->filled('rate') ? $rate : ($request->input('sale_rate') ?? $itemMaster->sale_rate);
            $costPrice = $request->filled('rate') ? $rate : ($request->input('cost_price') ?? $itemMaster->cost_price);

            $itemTypeModel = ItemType::find($request->type_id);
            $itemType = $request->input('item_type') ?: ($itemTypeModel ? $itemTypeModel->item_type : ($itemMaster->item_type ?? 'rp'));

            $itemMaster->update([
                'item_code'   => $itemName,
                'type_id'     => $request->type_id,
                'item_type'   => $itemType,
                'unit_id'     => $request->unit_id,
                'purchase'    => $purchase,
                'sale_rate'   => $saleRate,
                'cost_price'  => $costPrice,
                'urdu_title'  => $request->urdu_title,
                'seq_number'  => $request->seq_number ?? $itemMaster->seq_number,
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
