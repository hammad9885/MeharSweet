<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level1 extends Model
{
    use HasFactory;
    protected $fillable = [
        'group_id',
        'title',
        'level1_code'
    ];
    public function groups()
    {
        return $this->hasOne(Group::class, 'group_id');
    }
    public function level2s()
    {
        return $this->hasMany(level2::class, 'level1_id');
    }
}
