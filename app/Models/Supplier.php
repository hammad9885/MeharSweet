<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Supplier extends Model
{
    use HasFactory;
    protected $table = 'supplier';
    protected $fillable = [
        'city',
        'address',
        'cnic',
        'mobile',
        'urdu_title',
        'email',
        'ntn',
        'acc_id',
        'cid',
        'name',
        'seq_number',
        'order_number'
    ];

    public static function normalizeOrderNumber($value)
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (preg_match('/-?\d+/', $value, $matches)) {
            return (string) (int) $matches[0];
        }

        return null;
    }

    public function getOrderNumberAttribute($value)
    {
        return self::normalizeOrderNumber($value);
    }

    public function setOrderNumberAttribute($value)
    {
        $this->attributes['order_number'] = self::normalizeOrderNumber($value);
    }

    public static function sortByOrderNumber($suppliers)
    {
        return $suppliers->sortBy(function ($supplier) {
            $value = trim((string) ($supplier->order_number ?? ''));

            if ($value === '') {
                return PHP_INT_MAX;
            }

            preg_match('/-?\d+/', $value, $matches);
            $number = $matches[0] ?? '';

            return $number === '' ? PHP_INT_MAX : (int) $number;
        }, SORT_REGULAR)->values();
    }

    public function accountMaster()
    {
        return $this->belongsTo(AccountMaster::class, 'acc_id');
    }
}