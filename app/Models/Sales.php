<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * @mixin IdeHelperSales
 */
class Sales extends Model
{
    use HasFactory, Notifiable;


    protected $primaryKey = 'sales_id';
    protected $fillable = [
        'user_id',
        'total_amount',
        'tax',
        'discount',
        'payment_method',
        'reference_code',
        'status',
        'type',
    ];

    public function items()
    {
        return $this->hasMany(SalesItem::class, 'sales_id', 'sales_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function transaction()
    {
        return $this->hasOne(Transactions::class, 'transaction_id', 'transaction_id');
    }
}
