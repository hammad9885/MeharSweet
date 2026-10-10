<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasteSection extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
    protected $table = 'pastesections';  // Explicitly defining the table name (though it's already correct)
}

