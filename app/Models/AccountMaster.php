<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountMaster extends Model
{
    use HasFactory;
    protected $fillable = [
        'level2_id',
        'title',
        'opening_date',
        'account_code','cid'
    ];
    public function erpParams_cash()
    {
        return $this->hasMany(ErpParam::class, 'cash_level');
    }
    public function erpParams_bank()
    {
        return $this->hasOne(ErpParam::class, 'bank_level');
    }
    public function erps()
    {
        return $this->belongsTo(ErpParam::class, 'purchase_account');
    }
    public function purchaseDebitAccount()
    {
        return $this->belongsTo(ErpParam::class, 'purchase_debit_account');
    }
    public function level2s()
    {
        return $this->belongsTo(Level2::class, 'level2_id');
    }
    public function cashTrndtls()
    {
        return $this->hasMany(Trndtl::class, 'cash_id');
    }

    public function accountTrndtls()
    {
        return $this->hasMany(Trndtl::class, 'account_id','id');
    }
    public function deliverymasters()
    {
        return $this->hasMany(DeliveryMaster::class, 'account_id');
    }
    public function level2()
{
    return $this->belongsTo(Level2::class, 'level2_id');
}

// Dynamic accessor for Level1
public function getLevel1Attribute()
{
    return $this->level2 ? $this->level2->level1s : null;
}

// Dynamic accessor for Group
public function getGroupAttribute()
{
    return $this->level2 && $this->level2->level1s
        ? $this->level2->level1s->groups
        : null;
}

    public function getFullCodeAttribute()
    {
        $groupCode  = $this->group?->group_code ?? '00';
        $level1Code = $this->level1?->level1_code ?? '00';
        $level2Code = $this->level2?->level2_code ?? '00';
        $accCode    = $this->account_code ?? '0000';

        return "{$groupCode}-{$level1Code}-{$level2Code}-{$accCode}";
    }



}
