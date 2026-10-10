<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleInvoice extends Model
{
    use HasFactory;

    protected $table = 'sale_invoices'; // Ensure this matches your table name

    protected $fillable = [
        'v_no',
        'product_name',
        'item',
        'box',
        'packing',
        'rate',
        'batch_no',
        'total',
        'created_at',
        'updated_at',
        'old_vno',
        'account_id',
        'billing_no'
       
        
    ];
    
    
    public function trndtls()
    {
        return $this->hasMany(TRNDTL::class, 'r_id');
    }
    public function items()
    {
        return $this->belongsTo(ItemMaster::class, 'item_code');
    }
    public function product()
{
    return $this->belongsTo(ProductMaster::class, 'product_name', 'id');
}
public function itemType()
{
    return $this->belongsTo(ItemType::class, 'item', 'id');
}

    public function accountMaster()
{
    return $this->belongsTo(AccountMaster::class, 'party', 'id');
}
 public function master()
    {
        return $this->belongsTo(SaleMasters::class, 'v_no', 'v_no');
    }

}
