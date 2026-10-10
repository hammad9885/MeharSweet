<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackingLotPostingDetail extends Model
{
    use HasFactory;

    protected $table = 'packing_lot_posting_details';

    protected $fillable = [
        'cid',
        'voucher_no',
        'contractor_id',
        'item_id',
        'qty',
        'unit',
        'mold_id',
        'mold_qty',
        'wages',
        'wages_amount',
    ];

    public function contractor()
    {
        return $this->belongsTo(AccountMaster::class, 'contractor_id', 'id');
    }

    public function item()
    {
        return $this->belongsTo(ItemMaster::class, 'item_id', 'id');
    }

    public function mold()
    {
        return $this->belongsTo(Mold::class, 'mold_id', 'id');
    }

    public function master()
    {
        return $this->belongsTo(PackingLotPostingMaster::class, 'voucher_no', 'voucher_no');
    }
}
