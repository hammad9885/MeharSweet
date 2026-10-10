<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawSaleInvoice extends Model
{
    use HasFactory;
    protected $table = 'raw_sale_invoices';
    protected $fillable = [
        'v_no', 'customer_id', 'item_id', 'qty', 'rate', 'exclusive', 
        'tax_rate', 'tax_amount', 'inclusive', 'discount', 'cid', 'discount_perc'
    ];

    public function item()
    {
        return $this->belongsTo(ItemMaster::class, 'item_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id', 'acc_id');
    }

    public function master()
    {
        return $this->belongsTo(RawSaleMaster::class, 'v_no', 'v_no');
    }
}
