<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleInvoices extends Model
{
    use HasFactory;

    protected $table = 'sale_invoices'; // Specify the table name

    protected $fillable = [
        'qty',
        'rate',
        'exclusive',
        'tax_rate',
        'tax_amount',
        'inclusive',
        'item_id',
        'customer_id',
        'v_no','cid',
        'discount_perc',
        'discount'
    ];

    // Define a relationship with the ItemMaster model
    public function item()
    {
        return $this->belongsTo(ProductMaster::class, 'item_id');
    }

    // Define a relationship with the Customer model
    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id','acc_id');
    }
      public function master()
    {
        return $this->belongsTo(SaleMasters::class, 'v_no', 'v_no');
    }
}