<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountMaster extends Model
{
    use HasFactory;
    protected $fillable = [
        'level3_id',
        'title',
        'opening_date',
    ];
    public function level3s()
    {
        return $this->belongsTo(Level3::class, 'level3_id');
    }
}
