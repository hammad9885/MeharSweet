<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipperReturn extends Model
{
    use HasFactory;
    protected $fillable = [
        'item_code',
        'qty',
        'rate',
        'amount',
        'vorcher_no',
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
