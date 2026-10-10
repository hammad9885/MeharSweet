<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraTime extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'rate'];
    protected $table = 'extra_times';
}

