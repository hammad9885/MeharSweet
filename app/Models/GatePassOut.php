<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GatePassOut extends Model
{
    use HasFactory;
    protected $fillable = [
    'v_no',
    'qty',
    'rate',
    'total',
    'description',
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
