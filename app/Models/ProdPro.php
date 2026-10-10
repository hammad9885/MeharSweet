<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdPro extends Model
{
    use HasFactory;
    protected $fillable = [
                 'stock_report_id',
        'item_code',
        'pquantity',
    ];
   
   public function prodMaster()
    {
        return $this->belongsTo(ProdMaster::class, 'stock_report_id');
    }
    public function itemMaster()
{
    return $this->belongsTo(ItemMaster::class, 'item_code', 'id');
}

}
