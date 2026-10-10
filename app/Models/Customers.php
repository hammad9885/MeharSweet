<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    use HasFactory;

    protected $table = 'customers';

    protected $fillable = [
        'city',
        'address',
        'cnic',
        'mobile',
        'email',
        'ntn',
        'acc_id',
        'cid',
        'name',
        'b_type',
        'registration_tax',
        'strn',
        'file_no',
        'company_title',
        'urdu_title',
        'customer_category_id',
        'seq_number',
        'disc'
    ];

    /**
     * Relationship with AccountMaster
     */
    public function accountMaster()
    {
        return $this->belongsTo(AccountMaster::class, 'acc_id');
    }
}
