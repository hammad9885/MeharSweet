<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DyeJob extends Model
{
    use HasFactory;

   
    protected $table = 'dye_jobs';

 
    protected $fillable = [
        'v_no',
        'department_id',
        'dye_man',
        'dye_man_impression',
        'dye_man_waste',
        'dye_helper',
        'dye_helper_impression',
        'dye_helper_waste',
        'total_manual_impression',
        'total_helper_impression',
        'dye_machine',
        'dye_machine_helper',
        'dye_date_machine',
        'dye_date_helper',
        
    ];
    

}
