<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryMaster extends Model
{
    use HasFactory;
    protected $fillable = ['date', 'account_id','v_no','preparedby','file_id','delivery_detail_id','v_type' , 'status',];

    public function deliveryDetails()
    {
        return $this->belongsTo(DeliveryDetail::class, 'delivery_detail_id');
    }
    public function accounts()
    {
        return $this->belongsTo(AccountMaster::class, 'account_id');
    }
}
