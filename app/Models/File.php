<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;
    protected $fillable = [
        'files',
    ];
    public function trndtl()
    {
        return $this->hasMany(TRNDTL::class, 'file_id');
    }
}
