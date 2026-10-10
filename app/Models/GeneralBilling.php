<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralBilling extends Model
{
    use HasFactory;

   
    protected $table = 'general_billings';

 
    protected $fillable = [
        'billing_no',
        'v_no',
        'date',
        'party_name',
        'gjs_no',
        'product_type',
        'item_name',
        'qty',
        'rate',
        'freight',
        'amount',
        'account_id',
        'prepared_by',
        'v_type',
        'party_type',
        'party_no',
        'pre_bal'
    ];
    
public function account()
{
    return $this->belongsTo(AccountMaster::class, 'account_id');
}
public function trnDetails()
{
    return $this->hasMany(TRNDTL::class, 'v_type', 'v_type');
}

}


