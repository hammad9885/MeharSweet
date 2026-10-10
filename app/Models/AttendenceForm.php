<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendenceForm extends Model
{
    protected $fillable = ['employee_id', 'fname', 'cnic', 'employee_time', 'employee_time_out', 'employee_date'];
    use HasFactory;
    
      public function employee()
    {
        return $this->belongsTo(Employees::class, 'employee_id');
    }
    
    public function employeeType()
{
    return $this->hasOne(EmployeeType::class, 'cnic_no', 'employee_id');
}
}
