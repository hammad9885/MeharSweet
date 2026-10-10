<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfectioneryMaster extends Model
{
    use HasFactory;
    protected $table = 'confectionery_masters'; 
    protected $fillable = ['date', 'account_id','v_no','preparedby','file_id','confectionery_detail_id',  'status', 'v_type','sequence_no'];

    public function confectioneryDetails()
    {
        return $this->belongsTo(ConfectioneryDetail::class, 'confectionery_detail_id');
    }
    public function accounts()
    {
        return $this->belongsTo(AccountMaster::class, 'account_id');
    }
}
