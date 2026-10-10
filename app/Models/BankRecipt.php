<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankRecipt extends Model
{
    protected $fillable = ['date', 'bank', 'account', 'description', 'amount','invoice_number','v_type','total_amount'];
    use HasFactory;
}
