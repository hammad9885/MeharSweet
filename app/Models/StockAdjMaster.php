<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjMaster extends Model
{
    use HasFactory;

    protected $fillable = ['v_no', 'v_date', 'prepared_by','cid'];

    public function details()
    {
        return $this->hasMany(StockAdjDetail::class, 'v_no', 'v_no');
    }
    public function preparedByUser()
    {
        return $this->belongsTo(User::class, 'prepared_by', 'cid');
    }
    
}

