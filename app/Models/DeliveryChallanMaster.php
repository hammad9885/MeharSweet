<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ReferenceName;

class DeliveryChallanMaster extends Model
{
    use HasFactory;
    protected $table = 'delivery_challan_master';
    protected $fillable = ['v_no', 'v_date', 'customer_id', 'prepared_by', 'ref_no', 'ref_name_id', 'bilty_no', 'transport_company', 'transport_company_id', 'qty_per_carton', 'freight', 'cid'];

    public function details()
    {
        return $this->hasMany(DeliveryChallanDetail::class, 'v_no', 'v_no')->orderBy('sort_order');
    }

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id', 'acc_id');
    }

    public function transportCompany()
    {
        return $this->belongsTo(TransportCompany::class, 'transport_company_id');
    }

    public function referenceName()
    {
        return $this->belongsTo(ReferenceName::class, 'ref_name_id');
    }
}
