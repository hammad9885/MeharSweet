<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employees extends Model
{
    use HasFactory;
    protected $fillable = [
    'fname', 'lname', 'phone_no', 'blood_group', 
    'address', 'employee', 'cnic_no', 
    'cnic_front_path', 'cnic_back_path', 'extra_time', 'rate', 'cad', 'joining_date', 'bonus_title', 'bonus_rate' , 'bonus_id'
];
    
    public function employeeType()
{
    return $this->hasOne(EmployeeType::class, 'cnic_no', 'id');
}
// Employee.php model
public function extraTime()
{
    return $this->belongsTo(ExtraTime::class, 'extra_time'); // Adjust the foreign key if needed
}
public function attendances()
{
    return $this->hasMany(AttendenceForm::class, 'employee_id');
}

}
