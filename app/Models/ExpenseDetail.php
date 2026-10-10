<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseDetail extends Model
{
    use HasFactory;

    // Table name (optional if following Laravel conventions)
    protected $table = 'expense_detail';

    // Mass assignable attributes
    protected $fillable = [
        'cid',
        'v_no',
        'expense_type',
        'amount',
    ];

    // Optional: If you want to cast amount to float automatically
    protected $casts = [
        'amount' => 'float',
    ];
}
