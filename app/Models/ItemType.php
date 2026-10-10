<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemType extends Model
{
    use HasFactory;
    protected $fillable = [
        'type_title',
        'item_type',
        'cid',
        'load_posting'
    ];
    public function itemmasters()
    {
        return $this->hasMany(ItemMaster::class, 'type_id');
    }
}
