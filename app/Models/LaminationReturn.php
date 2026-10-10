<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaminationReturn extends Model
{
    use HasFactory;
    protected $fillable = [
        'qty',
        'rate',
        'amount',
        'size',
        'vorcher_no',
        'item_id',
    ];
    public function trndtls()
    {
        return $this->belongsTo(TRNDTL::class, 'r_id');
    }
     public function item()
{
    return $this->belongsTo(ItemMaster::class, 'item_id', 'id');
}

}
