<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DyeReturn extends Model
{
    use HasFactory;
    protected $fillable = [
    'v_no',
    'rate',
    'amount',
    'description',
    'file_path',
    'item_code',
    'qty',
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
