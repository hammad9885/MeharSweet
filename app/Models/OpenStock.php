<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpenStock extends Model
{
    use HasFactory;
    protected $table='opening_stock';
    protected $fillable=['item_id','cid','qty','rate','v_no','v_date'];

    public function item(){
        return $this->belongsTo(ItemMaster::class);
    }
}
