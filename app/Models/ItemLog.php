<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'type_id',
        'item_code',
        'new_purchase',
        'old_purchase',
        'cid'
    ];
    
    public function itemtypes()
    {
        return $this->belongsTo(ItemType::class, 'type_id');
    }
    public function deliverydetails()
    {
        return $this->hasMany(DeliveryDetail::class, 'item_code');
    }
    public function purchasereturns()
    {
        return $this->hasMany(PurchaseReturn::class, 'item_code');
    }
    public function purchasedetails()
    {
        return $this->hasMany(PurchaseDetail::class, 'item_code');
    }
    public function purchaseplates()
    {
        return $this->hasMany(PurchasePlate::class, 'item_code');
    }
}
