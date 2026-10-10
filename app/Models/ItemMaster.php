<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemMaster extends Model
{
    use HasFactory;

   protected $fillable = [
    'item_code',
    'type_id',
    'purchase',
    'sale_rate',
    'unit_id',
    'cid',
    's_type',
    'sale_in',
    'sale_out',
    'cost_price',
    'item_type',
    // ✅ ADD THESE
    'urdu_title',
    'seq_number',
    'set_qty',
    'auto_load_on_consumption',
    'load_posting',
];
    // ========== Basic Relations ==========

    public function itemtypes()
    {
        return $this->belongsTo(ItemType::class, 'type_id');
    }


    public function itemunits()
    {
        return $this->belongsTo(Units::class, 'unit_id');
    }

    public function itemsaletype()
    {
        return $this->belongsTo(SaleType::class, 's_type');
    }

    // ========== Transactions (Filtered by cid) ==========

    public function purchasedetails()
    {
        return $this->hasMany(PurchaseDetail::class, 'item_code', 'item_code')
            ->where('cid', $this->cid);
    }

    public function consumed()
    {
        return $this->hasMany(ProductionConsumed::class, 'rp_item_id', 'id')
            ->where('cid', $this->cid);
    }
    public function  openingstock()
    {
        return $this->hasMany(OpenStock::class, 'item_id', 'id')
            ->where('cid', $this->cid);
    }

    public function items()
    {
        return $this->hasMany(PurchaseInvoice::class, 'item_id', 'id')
            ->where('cid', $this->cid);
    }

    public function purchases()
    {
        return $this->hasMany(PurchaseInvoice::class, 'item_id', 'id')
            ->where('cid', $this->cid);
    }

    public function production()
    {
        return $this->hasMany(ProductionDetail::class, 'item_id', 'id')
            ->where('cid', $this->cid);
    }

    public function sales()
    {
        return $this->hasMany(SaleInvoices::class, 'item_id', 'id')
            ->where('cid', $this->cid);
    }

    public function adjustments()
    {
        return $this->hasMany(StockAdjDetail::class, 'item_id', 'id')
            ->where('cid', $this->cid);
    }

    public function purchasereturn()
    {
        return $this->hasMany(PurchaseReturnInvoice::class, 'item_id', 'id')
            ->where('cid', $this->cid);
    }

    public function salereturnreturn()
    {
        return $this->hasMany(SaleReturnInvoice::class, 'item_id', 'id')
            ->where('cid', $this->cid);
    }
}
