<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMaster extends Model
{
    use HasFactory;

    protected $table = 'product_masters';

    protected $fillable = [
        'cid',
        'product_name',
        'type_id',     // ✅ updated
        'unit_id',     // ✅ updated
        'group_id',
        'dye_no',
        'stroke',
        'stroke_wt',
        'min_qty',
        'lot_no_purchase',
        'lot_no_sale',
        'seq_number',
        'urdu_title',
        'finished',
        'cost_price',
        'rate',
    ];



    // ✅ Relationship: Product Group
    public function group()
    {
        return $this->belongsTo(ProductGroup::class, 'group_id');
    }

    // ✅ Relationship: Item Type
    public function type()
    {
        return $this->belongsTo(ItemType::class, 'type_id');
    }

    // ✅ Relationship: Unit
    public function unit()
    {
        return $this->belongsTo(Units::class, 'unit_id');
    }
}