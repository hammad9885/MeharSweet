<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ReferenceName;

class SaleMasters extends Model
{
    use HasFactory;
    protected $table = "sale_master";
    protected $fillable = ['v_no', 'gate_pass_no','grand_total','discount','freight','cid','v_date','customer_id','dc_vno','ref_name_id','bilty_no','transport_company_id'];
     // Define relationship to invoices
    public function items()
    {
        return $this->hasMany(SaleInvoices::class, 'v_no', 'v_no');
    }
     public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id','acc_id');
    }

    public function referenceName()
    {
        return $this->belongsTo(ReferenceName::class, 'ref_name_id');
    }

    public function transportCompany()
    {
        return $this->belongsTo(TransportCompany::class, 'transport_company_id');
    }
}
