<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobDetail extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'job_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'v_no',
        'prepared_by',
        'job_type',
        'aid',
        'product_id',
        'packets',
        'delivery_date',
        'department_name',
        'designation_sup',
        'employee_sup',
        'department_Process',
        'length',
        'width',
        'no_of_cut',
        'batch_no',
        'batch_qty',
        'custom_descr',
        'job_status',
        'box_item',
        'box_width',
        'box_length',
        'box_qty',
        'box_employee',
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
        'box_machine',
        'breaking_waste',
        'breaking_impression',
        'box_date_boxboard',
        'job_sheet_status',
        'box_status',
        
    ];
    


public function accountMaster()
{
    return $this->belongsTo(AccountMaster::class, 'aid');
}

// In app/Models/JobDetail.php

public function productMaster()
{
    return $this->belongsTo(ProductMaster::class, 'product_id');
}

// In app/Models/JobDetail.php

public function processSection()
{
    return $this->belongsTo(ProcessSection::class, 'process_id');
}

// In app/Models/JobDetail.php

public function shipper()
{
    return $this->belongsTo(ItemMaster::class, 'shipper_id');
}

public function departments()
{
    return $this->hasMany(JobDepartment::class);
}

public function batchDetails()
{
    return $this->hasMany(JobBatchDetail::class);
}

public function processes()
{
    return $this->hasMany(JobProcess::class);
}


}
