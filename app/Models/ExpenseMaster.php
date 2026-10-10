<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseMaster extends Model
{
    use HasFactory;

    // Table name (optional if following Laravel conventions)
    protected $table = 'expense_masters';

    // Mass assignable attributes
    protected $fillable = [
        'cid',
        'v_no',
        'total_amount',
    ];

    // Optional: cast total_amount to float
    protected $casts = [
        'total_amount' => 'float',
    ];

    /**
     * Relationship: One ExpenseMaster has many ExpenseDetail
     */
    public function details()
    {
        return $this->hasMany(ExpenseDetail::class, 'v_no', 'v_no');
        // 'v_no' is the foreign key in expense_detail table
        // 'v_no' is the local key in expense_masters table
    }
}
