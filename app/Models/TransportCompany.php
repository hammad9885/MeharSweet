<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportCompany extends Model
{
    use HasFactory;
    protected $table = 'transport_companies';
    protected $fillable = ['name', 'cid'];
}
