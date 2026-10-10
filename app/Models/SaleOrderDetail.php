<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleOrderDetail extends Model
{
    use HasFactory;
    protected $table = 'sale_order_detail';
    protected $fillable = ['v_no', 'item_id', 'qty', 'original_qty', 'rate', 'cid'];

    public function master()
    {
        return $this->belongsTo(SaleOrderMaster::class, 'v_no', 'v_no');
    }

    public function item()
    {
        return $this->belongsTo(ProductMaster::class, 'item_id');
    }
}
