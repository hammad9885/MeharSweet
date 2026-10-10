<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockInMaster extends Model
{
    use HasFactory;

    protected $table = 'stock_in_masters';

    protected $fillable = ['cid', 'inv_no', 'date', 'ref_name_id', 'dala_no'];

    public function details()
    {
        return $this->hasMany(StockInDetail::class, 'inv_no', 'inv_no');
    }

    public function refName()
    {
        return $this->belongsTo(ReferenceName::class, 'ref_name_id', 'id');
    }
}
