<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WastageSale extends Model
{
    use HasFactory;
    protected $fillable = [
    'v_no',
    'weight',
    'rate',
    'total',
    'description',
    'item_code',
     'file_path'
    ];

    public function trndtls()
    {
        return $this->hasMany(TRNDTL::class, 'r_id');
    }
    public function items()
    {
        return $this->belongsTo(ItemMaster::class, 'item_code');
    }
}
