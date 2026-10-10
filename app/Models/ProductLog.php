<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductLog extends Model
{
    use HasFactory;

    // Specify the table name (optional if the table is named product_logs)
    protected $table = 'product_log';

    // Allow mass assignment for these fields
    protected $fillable = ['prod_id', 'prod_name', 'old_rate', 'new_rate', 'action', 'updated_at'];

    // Disable the default timestamps as we have custom handling of updated_at
    public $timestamps = false;

    // You can also define relationships if needed
}
