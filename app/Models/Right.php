<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Right extends Model
{
    use HasFactory;
    protected $table = 'rights'; // Make sure the table name is correct
    protected $fillable = ['user_id', 'app_name', 'add', 'edit', 'del']; 
   
}
