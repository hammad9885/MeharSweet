<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionMaster extends Model
{
    use HasFactory;

    protected $table = 'production_master'; // table name is not plural

    protected $fillable = [
        'v_no',
        'v_date',
        'descr',
        'cid',
        'amount'
    ];

    // ✅ Relationship: One master has many details
    public function details()
    {
        return $this->hasMany(ProductionDetail::class, 'v_no', 'v_no');
    }

    // ✅ Optional: link to customer or company if cid refers to another table
    public function customer()
    {
        return $this->belongsTo(Customers::class, 'cid');
    }
}
