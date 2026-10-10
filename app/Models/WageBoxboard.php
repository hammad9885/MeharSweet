<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WageBoxboard extends Model
{
    use HasFactory;

    protected $fillable = [
        'v_no',
        'employee_name',
        'process_name',
        'process_rate',
        'packets',
        'boxboard_wage',
        'account_id',
        'date',
        'prepared_by',
        'v_type',
        'total_amount',
        'b_no',
    ];
}
