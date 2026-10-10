<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralDeliveryChallen extends Model
{
    use HasFactory;

   
    protected $table = 'general_delivery_challens';

 
    protected $fillable = [
        'v_type',
            'v_no',
            'prepared_by',
            'gjs_no',
            'party_id' ,
            'party_name' ,
            'product_type' ,
            'item_name', 
            'qty' ,     
            'rate' ,
            'freight',
    ];
    

public function account()
{
    return $this->belongsTo(AccountMaster::class, 'party_id');  // Using party_id as foreign key
}
}


