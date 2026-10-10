<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockIssueDetail extends Model
{
    use HasFactory;

    protected $table = 'stock_issue_details';

    protected $fillable = ['cid', 'inv_no', 'product_id', 'qty'];

    public function product()
    {
        return $this->belongsTo(ItemMaster::class, 'product_id', 'id');
    }

    public function master()
    {
        return $this->belongsTo(StockIssueMaster::class, 'inv_no', 'inv_no');
    }
}
