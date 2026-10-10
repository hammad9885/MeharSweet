<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleReturnMaster extends Model
{
    use HasFactory;

    // Table for sale return masters
    protected $table = "sale_return_masters";

    // Fillable fields
    protected $fillable = ['v_no','gate_pass_no', 'grand_total', 'discount', 'cid','v_date','customer_id'];

    // Define relationship to Sale Return
    public function items()
    {
        return $this->hasMany(SaleReturnInvoice::class, 'v_no', 'v_no');
    }
  public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id','acc_id');
    }
}
