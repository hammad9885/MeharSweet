<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionDetail extends Model
{
    use HasFactory;

    protected $table = 'production_detail'; // since table is not plural

    protected $fillable = [
        'v_no',
        'item_id',
        'qty',
        'rate',
        'cid',
        'amount',
    ];

    // ✅ Optional: Define relationships if needed
    public function item()
    {
        return $this->belongsTo(ItemMaster::class, 'item_id');
    }
}
