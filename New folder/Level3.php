<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level3 extends Model
{
    use HasFactory;
    protected $fillable = [
        'level2_id',
        'title'
    ];
    public function level2s()
    {
        return $this->belongsTo(level2::class, 'level2_id');
    }
    public function AccountMasters()
    {
        return $this->hasOne(AccountMaster::class, 'level3_id');
    }
}
