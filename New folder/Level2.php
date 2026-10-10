<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level2 extends Model
{
    use HasFactory;
    protected $fillable = [
        'level1_id',
        'title',
        'level2_code'
    ];
    public function level1s()
    {
        return $this->hasOne(Group::class, 'level1_id');
    }
    public function level3s()
    {
        return $this->belongsTo(level3::class, 'level3_id');
    }
}
