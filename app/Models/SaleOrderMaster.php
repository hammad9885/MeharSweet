<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ReferenceName;

class SaleOrderMaster extends Model
{
    use HasFactory;
    protected $table = 'sale_order_master';
    protected $fillable = ['v_no', 'v_date', 'prepared_by', 'party_ref', 'ref_name_id', 'transport_company', 'transport_company_id', 'bilty_no', 'customer_id', 'cid'];

    public function details()
    {
        return $this->hasMany(SaleOrderDetail::class, 'v_no', 'v_no')->orderBy('id');
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
