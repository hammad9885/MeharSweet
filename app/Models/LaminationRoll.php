<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaminationRoll extends Model
{
    use HasFactory;
    protected $fillable = [
        'size',
        'rate',
        'no_of_roles',
        'party_name',
        'gate_pass_in',
    ];
}
