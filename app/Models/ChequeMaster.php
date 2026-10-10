<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChequeMaster extends Model
{
    use HasFactory;
    protected $fillable = [
    'v_no',
    'chq_status',
    'chq_no',
    'chq_amt',
    'aid',
    'prepared_by',
    'chq_date',
    'description',
    'v_type',
    'bank',
    ];

    public function trndtls()
    {
        return $this->hasMany(TRNDTL::class, 'r_id');
    }
    public function items()
    {
        return $this->belongsTo(ItemMaster::class, 'item_code');
    }
    public function account()
    {
        // Define the relationship to AccountMaster
        return $this->belongsTo(AccountMaster::class, 'aid');
    }
    public function banks()
    {
        // Define the relationship to AccountMaster
        return $this->belongsTo(AccountMaster::class, 'bank');
    }
}
