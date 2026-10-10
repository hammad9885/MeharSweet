<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawSaleReturnInvoice extends Model
{
    use HasFactory;
    protected $table = 'raw_sale_return_invoices';
    protected $fillable = [
        'qty', 'rate', 'exclusive', 'tax_rate', 'tax_amount', 'inclusive', 
        'item_id', 'customer_id', 'v_no', 'cid', 'discount', 'discount_perc'
    ];

    public function item()
    {
        return $this->belongsTo(ItemMaster::class, 'item_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id','acc_id');
    }

    public function master()
    {
        return $this->belongsTo(RawSaleReturnMaster::class, 'v_no', 'v_no');
    }
}
