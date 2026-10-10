<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';
    use HasFactory;

    protected $fillable = [
        'name',
        'city',
        'address',
        'Remark',
        'cid',
        'customer_category_id',
        'seq_number'
    ];
}