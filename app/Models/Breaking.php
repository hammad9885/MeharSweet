<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Breaking extends Model
{
    use HasFactory;

   
    protected $table = 'breakings';

 
    protected $fillable = [
        'v_no',
        'department_id',
        
        'breaking_date_machine',
        'breaking_contractor',
        'breaking_impression',
        
        'breaking_waste',
        'breaking_total_impression',
        'breaking_total_waste',
        
    ];
    

}
