<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoxBoard extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'lenght',
        'width',
        'gsm',
        'no_of_packets',
        'party_name',
        'gate_pass_in',
    ];
}
