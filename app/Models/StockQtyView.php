<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
 class StockQtyView extends Model
{
    use HasFactory;

    protected $table = 'stock_qty_view';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'item_id',
        'v_date',
        'opn_qty',
        'opn_amt',
        'pur_qty',
        'pur_amt',
        'pret_qty',
        'pret_amt',
        'sal_qty',
        'sal_amt',
        'sret_qty',
        'sret_amt',
        'con_qty',
        'con_amt',
        'cid'
    ];

    protected $casts = [
        'v_date' => 'date',
        'opn_qty' => 'decimal:2',
        'opn_amt' => 'decimal:2',
        'pur_qty' => 'decimal:2',
        'pur_amt' => 'decimal:2',
        'pret_qty' => 'decimal:2',
        'pret_amt' => 'decimal:2',
        'sal_qty' => 'decimal:2',
        'sal_amt' => 'decimal:2',
        'sret_qty' => 'decimal:2',
        'sret_amt' => 'decimal:2',
        'con_qty' => 'decimal:2',
        'con_amt' => 'decimal:2',
    ];

    /**
     * Relationship with Item model
     */
    public function item()
    {
        return $this->belongsTo(ItemMaster::class, 'item_id');
    }

    /**
     * Relationship with Company model
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'cid');
    }

    /**
     * Scope for filtering by item ID
     */
    public function scopeWhereItemId($query, $itemId)
    {
        return $query->where('item_id', $itemId);
    }

    /**
     * Scope for filtering by multiple item IDs
     */
    public function scopeWhereItemIds($query, array $itemIds)
    {
        return $query->whereIn('item_id', $itemIds);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeWhereDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('v_date', [$startDate, $endDate]);
    }

    /**
     * Scope for filtering by company
     */
    public function scopeWhereCompany($query, $companyId)
    {
        return $query->where('cid', $companyId);
    }

    /**
     * Scope for getting summarized stock data
     */
    public function scopeSummarized($query)
    {
        return $query->select([
            'item_id',
            'cid',
            \DB::raw('SUM(opn_qty) as total_opn_qty'),
            \DB::raw('SUM(opn_amt) as total_opn_amt'),
            \DB::raw('SUM(pur_qty) as total_pur_qty'),
            \DB::raw('SUM(pur_amt) as total_pur_amt'),
            \DB::raw('SUM(pret_qty) as total_pret_qty'),
            \DB::raw('SUM(pret_amt) as total_pret_amt'),
            \DB::raw('SUM(sal_qty) as total_sal_qty'),
            \DB::raw('SUM(sal_amt) as total_sal_amt'),
            \DB::raw('SUM(sret_qty) as total_sret_qty'),
            \DB::raw('SUM(sret_amt) as total_sret_amt'),
            \DB::raw('SUM(con_qty) as total_con_qty'),
            \DB::raw('SUM(con_amt) as total_con_amt')
        ])->groupBy('item_id', 'cid');
    }
}