<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    use HasFactory;

    protected $table = 'user_permission';

    protected $fillable = [
        'userid', 'module_id', 'cid', 'read', 'write', 'update', 'delete'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userid');
    }

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id');
    }
}
