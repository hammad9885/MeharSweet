<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
    ];
    public function level1s()
    {
        return $this->hasMany(Level1::class, 'group_id');
    }
}
