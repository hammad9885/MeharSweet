<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dye extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'rate',
        'party_name',
        'gate_pass_in',
    ];
}
