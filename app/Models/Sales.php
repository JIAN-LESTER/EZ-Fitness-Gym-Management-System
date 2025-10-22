<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Sales extends Model
{
    use HasFactory, Notifiable;


    protected $primaryKey = 'sales_id';
    protected $fillable = [
        'user_id',
        'total_amount',
        'payment_method',
        'status',
        'date'
    ];

    public function items()
    {
        return $this->hasMany(SalesItem::class, 'sales_item_id', 'sales_item_id');
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
