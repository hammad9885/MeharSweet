<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackingLotPostingMaster extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'packing_lot_posting_masters';

    protected $fillable = [
        'cid',
        'voucher_no',
        'date',
        'product_id',
        'prepared_by',
        'remarks',
        'lot_no',
        'prev_lot_no',
        'prev_lot_amount',
    ];

    public function details()
    {
        return $this->hasMany(PackingLotPostingDetail::class, 'voucher_no', 'voucher_no');
    }

    public function product()
    {
        return $this->belongsTo(ProductMaster::class, 'product_id', 'id');
    }
}
