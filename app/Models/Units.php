<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Units extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'cid'
    ];

    // ✅ Correct relation with ItemMaster
    public function itemmasters()
    {
        return $this->hasMany(ItemMaster::class, 'unit_id');
    }

    // ✅ (Recommended) relation with ProductMaster
    public function productMasters()
    {
        return $this->hasMany(ProductMaster::class, 'unit_id');
    }
}