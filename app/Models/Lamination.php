<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lamination extends Model
{
    use HasFactory;

   
    protected $table = 'laminations';

 
    protected $fillable = [
        'v_no',
        'department_id',
        
        'glue_item',
        'glue_qty',
        
        'lamination_man',
        'lamination_man_impression',
        'lamination_man_waste',
        
        'lamination_manual_impression',
        'lamination_date_machine',
        'lamination_machine',
        'lamination_item',
        'lamination_size',
        'lamination_qty',
        
    ];
    
public function laminationItem()
{
    return $this->belongsTo(ItemMaster::class, 'lamination_item');
}

public function glueItem()
{
    return $this->belongsTo(ItemMaster::class, 'glue_item');
}
}
