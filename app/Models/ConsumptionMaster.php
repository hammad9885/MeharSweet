<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumptionMaster extends Model
{
    use HasFactory;

    protected $fillable = [
        'cid',
        'v_no',
        'date',
        'descr',
        'amount',
        'qty',
        'cost_price',
        'tamount',
        'is_expired',
        'item_id',

        // NEW FIELDS
        'item_title',
        'mold_id',
        'mold_qty',
        'machine_capacity',
        'stroke',
        'grams',
        'contractor_account_id',
        'wages_rate',
        'wages_amount',
        'lot_no',
        'lot_qty',
        'qty_per_carton',
        'cartons_in_lot',
        'target_qty',
        'sale_rate',
        'packing_rate',
        'carton_rate',
        'remarks',
        'prepared_by',
        'calc_qty',
        'unit',
        'material_rate',
        'mat_amount',
        'hatt'
    ];

    public function details()
    {
        return $this->hasMany(ConsumptionDetail::class, 'v_no', 'v_no');
    }

    public function item()
    {
        return $this->belongsTo(ItemMaster::class, 'item_id', 'id');
    }

    // NEW RELATIONS

    public function mold()
    {
        return $this->belongsTo(Mold::class, 'mold_id');
    }

    public function contractor()
    {
        return $this->belongsTo(AccountMaster::class, 'contractor_account_id');
    }
}