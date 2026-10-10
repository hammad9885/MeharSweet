<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawSaleReturnMaster extends Model
{
    use HasFactory;
    protected $table = "raw_sale_return_masters";
    protected $fillable = ['v_no','gate_pass_no', 'grand_total', 'discount', 'cid','v_date','customer_id'];

    public function items()
    {
        return $this->hasMany(RawSaleReturnInvoice::class, 'v_no', 'v_no');
    }

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id','acc_id');
    }
}
