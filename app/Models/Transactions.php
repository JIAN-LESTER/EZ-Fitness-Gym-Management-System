<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * @mixin IdeHelperTransactions
 */
class Transactions extends Model
{
    use HasFactory, Notifiable;


    protected $primaryKey = 'transaction_id';
    protected $fillable = [
        'sales_id',
        'product_id',
        'type',
        'performed_by',
        'quantity'

    ];

    public function sale()
    {
        return $this->belongsTo(Sales::class, 'sales_id', 'sales_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by', 'user_id');
    }
}
