<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mold extends Model
{
    use HasFactory;
    protected $fillable = [
    'mold_id',
    'mold_name',
    'weight',
    'wages',
    'expense',
    'total',
    'remarks',
    'cid'
];
}
