<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleType extends Model
{
    use HasFactory;
    protected $table="sale_type";
    protected $fillable = [
        'sale_type','cid'
    ];
    public function itemmasters()
    {
        return $this->hasMany(ItemMaster::class, 's_type');
    }
}
