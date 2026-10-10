<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'item_code',
        'width',
        'lenght',
        'grammage',
        'total_wt',
        'qty',
        'rate',
        'amount',
        'vorcher_no',
        'freight',
        'freight_type',
    ];
    public function trndtls()
    {
        return $this->hasMany(TRNDTL::class, 'r_id');
    }
    public function items()
    {
        return $this->belongsTo(ItemMaster::class, 'item_code');
    }
}
