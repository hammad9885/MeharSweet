<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
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
