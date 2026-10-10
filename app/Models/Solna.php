<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solna extends Model
{
    use HasFactory;

   
    protected $table = 'solnas';

 
    protected $fillable = [
        'v_no',
        'department_id',
        'ink_item',
        'ink_qty',
        
        'solna_man',
        'solna_man_impression',
        'solna_man_waste',
        'solna_helper',
        'solna_helper_impression',
        'solna_helper_waste',
        'manual_impression',
        'helper_impression',
        'solna_machine_helper',
        'solna_machine',
        'solna_supervisor_impression',
        'solna_date_machine',
        'solna_date_helper',
        'solna_total_job_sheet_impression',
        
    ];
    

}
