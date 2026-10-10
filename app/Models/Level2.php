<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level2 extends Model
{
    use HasFactory;
    protected $fillable = [
        'level1_id',
        'title',
        'level2_code','cid'
    ];
    public function level1s()
    {
        return $this->belongsTo(Level1::class, 'level1_id');
    }
    public function groups()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
    public function AccountMasters()
    {
        return $this->hasMany(AccountMaster::class, 'level2_id');
    }
    public function erpParams()
    {
        return $this->hasMany(ErpParam::class);
    }
}
