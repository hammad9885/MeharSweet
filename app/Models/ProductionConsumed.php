<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionConsumed extends Model
{
    use HasFactory;

    protected $table = 'production_consumed';

    protected $fillable = [
        'v_no',
        'fp_item_id',
        'rp_item_id',
        'qty',
        'rate',
        'cid',
    ];

    // 🧩 Relationships (optional)
    public function finishedProduct()
    {
        return $this->belongsTo(ItemMaster::class, 'fp_item_id');
    }

    public function rawProduct()
    {
        return $this->belongsTo(ItemMaster::class, 'rp_item_id');
    }
}
