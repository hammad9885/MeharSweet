<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'department_id',
        'number',
        'address',
        'blood_group',
        'salary',
        'shift_time1',
        'shift_time2',
        'registered',
    ];
    public function departments()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
