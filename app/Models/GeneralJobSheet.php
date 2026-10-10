<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralJobSheet extends Model
{
    use HasFactory;

   
    protected $table = 'general_job_sheets';

 
    protected $fillable = [
        'v_no',
        'prepared_by',
        'account_id',
        'product_type',
        'item_name',
        'length',
        'width',
        'product_name',
        'country_name',
        'size',
        'qty',
        'rate',
        'description',
    ];
    
public function account()
{
    return $this->belongsTo(AccountMaster::class, 'account_id');
}
}


