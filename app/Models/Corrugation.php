<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Corrugation extends Model
{
    use HasFactory;

   
    protected $table = 'corrugations';

 
    protected $fillable = [
        'v_no',
        'department_id',
        'corrugation_date_machine',
        'corrugation_box',
        'corrugation_packing',
        'corrugation_item_type',
        'finish_product_qty',
        'po_order_qty',
        'corrugation_total_boxes',
        'shipper_item',
        'shipper_qty',
    ];
    

}
