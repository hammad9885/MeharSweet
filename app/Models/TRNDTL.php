<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TRNDTL extends Model
{
    // In app/Models/TRNDTL.php
protected $table = 'trn_dtl';

    protected $fillable = ['date', 'bank', 'account_id', 'description', 'credit','cash_id','v_no','v_type','debit','preparedby','file_id','r_id','status','pre_bal','cid'];
    use HasFactory;
    protected $casts = [
    'v_date' => 'date',
    'duedate' => 'date',
];
    public function accounts()
    {
        return $this->belongsTo(AccountMaster::class, 'account_id');
    }
        public function customer()
    {
        return $this->belongsTo(Customers::class, 'account_id','acc_id');
    }
        public function user()
    {
        return $this->belongsTo(User::class, 'preparedby','name');
    }
    public function files()
    {
        return $this->belongsTo(File::class, 'file_id');
    }
    public function cashes()
    {
        return $this->belongsTo(AccountMaster::class, 'cash_id');
    }
    public function acc_codes()
    {
        return $this->belongsTo(AccountMaster::class, 'account_id');
    }
    public function purchasedetails()
    {
        return $this->belongsTo(PurchaseDetail::class, 'r_id');
    }
   
    public function wastagesales()
{
    return $this->belongsTo(WastageSale::class, 'r_id');
}
public function saleinvoice()
{
    return $this->belongsTo(SaleInvoice::class, 'r_id');
}
public function gatePassIn()
{
    return $this->belongsTo(GatePassIn::class, 'r_id');
}

public function gatePassOut()
{
    return $this->belongsTo(GatePassOut::class, 'r_id');
}

    public function purchasereturns()
    {
        return $this->belongsTo(PurchaseReturn::class, 'r_id');
    }
    
    
      public function platereturns()
    {
        return $this->belongsTo(PlateReturn::class, 'r_id');
    }
    
    
    public function purchaseplates()
    {
        return $this->belongsTo(PurchasePlate::class, 'r_id');
    }
    
    public function gluepurchases()
    {
        return $this->belongsTo(GluePurchase::class, 'r_id');
    }
    
    public function gluereturns()
    {
        return $this->belongsTo(GlueReturn::class, 'r_id');
    }
    
    public function inkpurchases()
    {
        return $this->belongsTo(InkPurchase::class, 'r_id');
    }
    
    public function inkreturns()
    {
        return $this->belongsTo(InkReturn::class, 'r_id');
    }
    
    
    public function shipperpurchases()
    {
        return $this->belongsTo(ShipperPurchases::class, 'r_id');
    }
    
     public function dyepurchases()
    {
        return $this->belongsTo(DyePurchase::class, 'r_id');
    }
    
    
     public function shipperreturns()
    {
        return $this->belongsTo(ShipperReturn::class, 'r_id');
    }
    
     public function dyereturns()
    {
        return $this->belongsTo(DyeReturn::class, 'r_id');
    }
    
    
    
    public function leminationpurchases()
    {
        return $this->belongsTo(LaminationPurchase::class, 'r_id');
    }
    
    public function laminationreturns()
    {
        return $this->belongsTo(LaminationReturn::class, 'r_id');
    }
    
    
    public function corrugationPurchases()
    {
        return $this->belongsTo(CorrugationPurchase::class, 'r_id');
    }
    
    public function corrugationreturns()
    {
        return $this->belongsTo(CorrugationReturn::class, 'r_id');
    }
    
    
    public function group() {
        return $this->belongsTo(Group::class, 'group_id');
    }
    public function level1() {
        return $this->belongsTo(Level1::class, 'level1_id');
    }
}
