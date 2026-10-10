<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdMaster extends Model
{
    use HasFactory;
    protected $fillable = [
        'date',
        'prepared_by',
        'v_no',
    ];
   
   public function consumedItems()
    {
        return $this->hasMany(ProdCon::class, 'stock_report_id');
    }

    public function producedItems()
    {
        return $this->hasMany(ProdPro::class, 'stock_report_id');
    }
    
    // In ProdMaster model
public function prodCons() {
    return $this->hasMany(ProdCon::class, 'stock_report_id');
}

public function prodPros() {
    return $this->hasMany(ProdPro::class, 'stock_report_id');
}

}
