<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturnInvoice extends Model
{
    use HasFactory;

    // Table for purchase return invoices
    protected $table = 'purchase_return_invoice';

    protected $fillable = [
        'qty',
        'rate',
        'exclusive',
        'tax_rate',
        'tax_amount',
        'inclusive',
        'item_id',
        'supplier_id',
        'v_no','discount',
        'cid','discount_perc'
    ];

    // Relationship with ItemMaster
    public function item()
    {
        return $this->belongsTo(ItemMaster::class, 'item_id');
    }

    // Relationship with Supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id','acc_id');
    }

    // Relationship back to master
    public function master()
    {
        return $this->belongsTo(PurchaseReturnMaster::class, 'v_no', 'v_no');
    }
}
