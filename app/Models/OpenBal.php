<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpenBal extends Model
{
    use HasFactory;

    protected $table = 'opening_bakance';
    protected $fillable = ['aid', 'op_date', 'debit', 'credit', 'cid'];

    public function account()
    {
        return $this->belongsTo(AccountMaster::class, 'aid');
    }
}
