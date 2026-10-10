<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeType extends Model
{
    use HasFactory;

    protected $table = 'employee_types'; // Explicit table name (optional but recommended)
   
    protected $fillable = [
        'cnic_no',
        'department_id',
        'designation_id',
        'salary_type',
        'salary_cal',
        
        // Salary type fields
        'salary_amount',
        'shift_from',
        'shift_to',
        'break_from',
        'break_to',
        
        // Waje type fields (Solna departments)
        'basic_salary',
        'fix_impression_day',
        'lam_working_days',
        'over_time',
        
        // Waje type fields (Box department)
        'process_name',
        'process_rate',
        
        // Department 21 field
        'per_sheet_rate',
        
        // Timestamps are automatically included
    ];

    protected $casts = [
        'shift_from' => 'datetime:H:i',
        'shift_to' => 'datetime:H:i',
        'break_from' => 'datetime:H:i',
        'break_to' => 'datetime:H:i',
        'salary_amount' => 'decimal:2',
        'basic_salary' => 'decimal:2',
       
        'per_sheet_rate' => 'decimal:2',
          
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'cnic_no', 'id');
    }

    public function department()
    {
        return $this->belongsTo(DepartmentSection::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    // Scopes for easier querying
    public function scopeSalaryType($query, $type)
    {
        return $query->where('salary_type', $type);
    }

    public function scopeDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }
}