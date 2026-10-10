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
    public function groups()
    {
        return $this->belongsTo(Level1::class, 'group_id');
    }
}
