<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleReturnInvoice extends Model
{
    use HasFactory;

    // Table for Sale Return
    protected $table = 'sale_return_invoices';

    protected $fillable = [
        'qty',
        'rate',
        'exclusive',
        'tax_rate',
        'tax_amount',
        'inclusive',
        'item_id',
        'customer_id',
        'v_no',
        'cid',
        'discount',
        'discount_perc',
       
    ];

    // Relationship with ItemMaster
    public function item()
    {
        return $this->belongsTo(ProductMaster::class, 'item_id');
    }

    // Relationship with Customer
    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id','acc_id');
    }

    // Relationship back to master
    public function master()
    {
        return $this->belongsTo(SaleReturnMaster::class, 'v_no', 'v_no');
    }
}
