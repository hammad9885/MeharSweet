<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseMaster extends Model
{
    use HasFactory;
    protected $table = "purchase_master";
    protected $fillable = ['v_no','gate_pass_no','ref_no','remarks', 'grand_total','discount','freight','net_amount','cid','v_date','supplier_id','contractor_id'];

    public function items()
    {
        return $this->hasMany(PurchaseInvoice::class, 'v_no', 'v_no');
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id','acc_id');
    }
    public function contractor()
    {
        return $this->belongsTo(AccountMaster::class, 'contractor_id');
    }
}
