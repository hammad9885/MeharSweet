<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessSection extends Model
{
    
    use HasFactory;

    protected $fillable = ['name','rate'];
    protected $table = 'processsections';  // Explicitly defining the table name (though it's already correct)
    
    public function department()
{
    return $this->belongsTo(DepartmentSection::class, 'dept_id');
}

}

