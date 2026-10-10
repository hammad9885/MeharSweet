<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryDetail extends Model
{
    use HasFactory;
    protected $fillable = ['v_no','product_name', 'item_code','box','pack_qty', 'batch_no', 'total', 'freight','freight_type', 'job_sheet'];
    public function deliverymasters()
    {
        return $this->hasMany(DeliveryMaster::class, 'delivery_detail_id');
    }
    public function items()
    {
        return $this->belongsTo(ItemType::class, 'type_title');
    }
     public function itemType()
{
    return $this->belongsTo(ItemType::class, 'item_code', 'id');
}

    public function products()
    {
        return $this->belongsTo(ProductMaster::class, 'product_name');
    }
    
}
