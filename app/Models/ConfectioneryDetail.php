<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfectioneryDetail extends Model
{
    use HasFactory;
    protected $fillable = ['v_no','product_name', 'item_code','box','pack_qty', 'po_no', 'total','freight','account_id','freight_type','sequence_no', 'job_sheet'];
    public function confectionerymasters()
    {
        return $this->hasMany(ConfectioneryMaster::class, 'confectionery_detail_id');
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
