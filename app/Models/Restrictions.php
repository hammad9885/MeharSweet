<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restrictions extends Model
{
    use HasFactory;
    protected $table = "restrictions";

    // Fillable fields
    protected $fillable = ['permission', 'app','cid','value'];

    // Define relationship to Purchase Return

}
