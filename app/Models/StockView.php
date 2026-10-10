<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockView extends Model
{
    use HasFactory;
    protected $table='stock_qty';    protected $fillable = [
        'item_id',
        'v_date',
        'qty','source'
    ];

      public function item()
    {
        return $this->belongsTo(ItemMaster::class, 'item_id');
    }

}
