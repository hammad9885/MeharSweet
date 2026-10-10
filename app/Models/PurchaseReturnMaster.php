<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturnMaster extends Model
{
    use HasFactory;

    // Table for purchase return invoices
    protected $table = "purchase_return_master";

    // Fillable fields
    protected $fillable = ['v_no', 'gate_pass_no','grand_total', 'discount', 'cid','v_date','supplier_id'];

    // Define relationship to purchase return invoices
    public function items()
    {
        return $this->hasMany(PurchaseReturnInvoice::class, 'v_no', 'v_no');
    }
     public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id','acc_id');
    }
}
