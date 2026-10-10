<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumptionDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'cid',
        'v_no',
        'item_id',
        'qty',
        'rate',
        'amount',
        'mold_id',
        'machine_capacity',
        'stroke',
        'grams',
        'calc_qty',
        'unit',
        'contractor_account_id',
        'material_rate',
        'mat_amount',
        'hatt',
    ];

    public function master()
    {
        return $this->belongsTo(ConsumptionMaster::class, 'v_no', 'v_no');
    }
    public function item()
    {
        return $this->hasMany(ItemMaster::class, 'id', 'item_id');
    }
}
