<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InkPurchase extends Model
{
    use HasFactory;
    protected $fillable = [
        'item_code',
        'qty',
        'rate',
        'amount',
        'vorcher_no',
                'freight',
                'freight_type',
    ];
    public function trndtls()
    {
        return $this->belongsTo(TRNDTL::class, 'r_id');
    }
    public function items()
    {
        return $this->belongsTo(ItemMaster::class, 'item_code');
    }
}
