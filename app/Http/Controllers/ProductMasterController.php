<?php

namespace App\Http\Controllers;
use App\Models\ProductMaster;
use App\Models\ProductGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ItemType;
use App\Models\Units;
class ProductMasterController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->cid;
        $product_name = $request->input('product_name');
        $type_id = $request->input('type_id');
        $group_id = $request->input('group_id');
        $query = ProductMaster::where('cid', $companyId)->with(['group','type','unit']);
        if ($product_name) {
            $query->where('product_name', 'like', '%' . $product_name . '%');
        }

       if ($type_id) {
            $query->where('type_id', $type_id);
        }

        if ($group_id) {
            $query->where('group_id', $group_id);
        }

        $productMasters = $query->get();
        $groups = ProductGroup::where('cid', $companyId)->pluck('group_name', 'id');
        $types  = ItemType::where('cid', $companyId)->where('item_type', 'rp')->pluck('type_title', 'id');
        $units  = Units::where('cid', $companyId)->pluck('name', 'id');
        return view('product.List_Product_Master', get_defined_vars());
    }

    public function create()
    {
        $companyId = Auth::user()->cid;
        $groups = ProductGroup::where('cid', $companyId)->get();
        $types = ItemType::where('cid', $companyId)->where('item_type', 'rp')->pluck('type_title', 'id');
        $units = Units::where('cid', $companyId)->pluck('name', 'id');

        return view('product.create_product_master', get_defined_vars());
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->cid;
        $request->validate([
            'product_name' => 'nullable|string|max:255',
            'type_id' => 'nullable',
            'unit_id' => 'nullable',
            'seq_number' => 'nullable|string|max:255',
            'urdu_title' => 'nullable|string|max:255',
            'min_qty' => 'nullable|numeric|min:0',
            'stroke_wt' => 'nullable|string|max:255',
            'stroke' => 'nullable|numeric|min:0',
            'lot_no_sale' => 'nullable|string|max:255',
            'lot_no_purchase' => 'nullable|string|max:255',
            'finished' => 'nullable',
            'dye_no' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'rate' => 'nullable|numeric|min:0',
            'group_id' => 'nullable',
        ]);

        $rateValue = $request->filled('cost_price')
            ? $request->cost_price
            : ($request->filled('rate') ? $request->rate : 0);

        ProductMaster::create([
            'cid' => $companyId,
            'product_name' => $request->product_name,
            'type_id' => $request->filled('type_id') ? $request->type_id : '',
            'unit_id' => $request->filled('unit_id') ? $request->unit_id : '',
            'seq_number' => $request->seq_number,
            'urdu_title' => $request->urdu_title,
            'min_qty' => $request->min_qty,
            'stroke_wt' => $request->stroke_wt,
            'stroke' => $request->stroke,
            'lot_no_sale' => $request->lot_no_sale,
            'lot_no_purchase' => $request->lot_no_purchase,
            'finished' => $request->has('finished') ? 1 : 0,
            'dye_no' => $request->dye_no,
            'cost_price' => $rateValue,
            'rate' => $rateValue,
            'group_id' => $request->filled('group_id') ? $request->group_id : null,
        ]);

        return redirect()->route('product.master.list')
            ->with('success', 'Product Master created successfully.');
    }
 
    public function edit($id)
    {
        $companyId = Auth::user()->cid;
        $productMaster = ProductMaster::where('cid', $companyId)->findOrFail($id);
        $groups = ProductGroup::where('cid', $companyId)->get();
        $types = ItemType::where('cid', $companyId)->where('item_type', 'rp')->pluck('type_title', 'id');
        $units = Units::where('cid', $companyId)->pluck('name', 'id');
        return view('product.edit_product_master', get_defined_vars());
    }
    
    public function update(Request $request, $id)
    {
        $companyId = Auth::user()->cid;
        $request->validate([
            'product_name' => 'nullable|string|max:255',
            'type_id' => 'nullable',
            'unit_id' => 'nullable',
            'seq_number' => 'nullable|string|max:255',
            'urdu_title' => 'nullable|string|max:255',
            'min_qty' => 'nullable|numeric|min:0',
            'stroke_wt' => 'nullable|string|max:255',
            'stroke' => 'nullable|numeric|min:0',
            'lot_no_sale' => 'nullable|string|max:255',
            'lot_no_purchase' => 'nullable|string|max:255',
            'finished' => 'nullable',
            'dye_no' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'rate' => 'nullable|numeric|min:0',
            'group_id' => 'nullable',
        ]);

        $productMaster = ProductMaster::where('cid', $companyId)->findOrFail($id);

        $data = [];
        foreach (['product_name', 'seq_number', 'urdu_title', 'min_qty', 'stroke_wt', 'stroke', 'lot_no_sale', 'lot_no_purchase', 'dye_no'] as $f) {
            if ($request->has($f)) {
                $data[$f] = $request->$f;
            }
        }

        $data['type_id'] = $request->filled('type_id') ? $request->type_id : '';
        $data['unit_id'] = $request->filled('unit_id') ? $request->unit_id : '';
        $data['group_id'] = $request->filled('group_id') ? $request->group_id : null;
        $data['finished'] = $request->has('finished') ? 1 : 0;

        if ($request->filled('cost_price')) {
            $data['cost_price'] = $request->cost_price;
            $data['rate'] = $request->cost_price;
        } elseif ($request->filled('rate')) {
            $data['cost_price'] = $request->rate;
            $data['rate'] = $request->rate;
        } elseif ($request->has('cost_price') || $request->has('rate')) {
            $data['cost_price'] = 0;
            $data['rate'] = 0;
        }

        $productMaster->update($data);

        return redirect()->route('product.master.list')
            ->with('success', 'Product Master updated successfully.');
    }
 
    public function destroy($id)
    {
        $companyId = Auth::user()->cid;
        $productMaster = ProductMaster::where('cid', $companyId)->findOrFail($id);
        $productMaster->delete();
        return redirect()->route('product.master.list')->with('success', 'Product Master deleted successfully.');
    }
}