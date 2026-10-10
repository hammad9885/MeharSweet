<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalVoucher extends Model
{
    protected $fillable = ['account_title', 'debit_entries', 'credit_entries','total_amount','total_debit','total_credit','date','v_type'];
    use HasFactory;
}
