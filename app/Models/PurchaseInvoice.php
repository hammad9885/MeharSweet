<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseInvoice extends Model
{
    use HasFactory;

    protected $table = 'purchase_invoices'; // Specify the table name

    protected $fillable = [
        'qty',
        'rate',
        'exclusive',
        'tax_rate',
        'tax_amount',
        'inclusive',
        'item_id',
        'supplier_id',
        'v_no','cid','discount','discount_perc','lot_no','contractor_id'
    ];

    // Define a relationship with the ItemMaster model
    public function item()
    {
        return $this->belongsTo(ItemMaster::class, 'item_id');
    }

    // Define a relationship with the Customer model
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id','acc_id');
    }
    public function contractor()
    {
        return $this->belongsTo(AccountMaster::class, 'contractor_id');
    }
      public function master()
    {
        return $this->belongsTo(PurchaseMaster::class, 'v_no', 'v_no');
    }
}