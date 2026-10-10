<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Custom extends Model
{
    use HasFactory;

    protected $fillable = ['custom_name', 'rate'];
    protected $table = 'customs';  // Explicitly defining the table name (though it's already correct)
}

