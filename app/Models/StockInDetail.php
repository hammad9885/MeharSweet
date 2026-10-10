<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockInDetail extends Model
{
    use HasFactory;

    protected $table = 'stock_in_details';

    protected $fillable = ['cid', 'inv_no', 'product_id', 'contractor_id', 'lot_no', 'qty'];    public function product()
    {
        return $this->belongsTo(ProductMaster::class, 'product_id', 'id');
    }

    public function contractor()
    {
        return $this->belongsTo(AccountMaster::class, 'contractor_id', 'id');
    }

    public function master()
    {
        return $this->belongsTo(StockInMaster::class, 'inv_no', 'inv_no');
    }
}
