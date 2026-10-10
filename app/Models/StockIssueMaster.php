<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockIssueMaster extends Model
{
    use HasFactory;

    protected $table = 'stock_issue_masters';

    protected $fillable = ['cid', 'inv_no', 'date', 'product_id', 'contractor_id', 'lot_no'];

    public function details()
    {
        return $this->hasMany(StockIssueDetail::class, 'inv_no', 'inv_no');
    }

    public function product()
    {
        return $this->belongsTo(ProductMaster::class, 'product_id', 'id');
    }

    public function contractor()
    {
        return $this->belongsTo(AccountMaster::class, 'contractor_id', 'id');
    }
}
