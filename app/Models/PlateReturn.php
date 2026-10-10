<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlateReturn extends Model
{
    use HasFactory;
    
      protected $table = 'plate_returns'; // Explicitly set the table name
      
    protected $fillable = [
        'item_code',
        'product_name',
        'description',
        'grammage',
        'total_wt',
        'qty',
        'rate',
        'amount',
        'vorcher_no',
        'country',
    ];
    public function trndtls()
    {
        return $this->belongsTo(TRNDTL::class, 'r_id');
    }
    public function items()
    {
        return $this->belongsTo(ItemMaster::class, 'item_code');
    }
    public function products()
    {
        return $this->belongsTo(ProductMaster::class, 'product_name');
    }
    public function countries()
    {
          return $this->belongsTo(Country::class, 'country', 'id');
    }
}
