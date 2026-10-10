<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryChallanDetail extends Model
{
    use HasFactory;
    protected $table = 'delivery_challan_detail';
    protected $fillable = ['v_no', 'sale_order_vno', 'item_id', 'available_qty', 'load_qty', 'lot_no', 'sort_order', 'cid'];

    public function master()
    {
        return $this->belongsTo(DeliveryChallanMaster::class, 'v_no', 'v_no');
    }

    public function item()
    {
        return $this->belongsTo(ProductMaster::class, 'item_id');
    }
}
