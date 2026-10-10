<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ink extends Model
{
    use HasFactory;
    protected $fillable = [
        'party_name',
        'gate_pass_in',
    ];
}
