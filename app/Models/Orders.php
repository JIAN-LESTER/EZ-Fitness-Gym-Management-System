<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Orders extends Model
{
 use HasFactory, Notifiable;


    protected $primaryKey = 'order_id';
    protected $fillable = [
        'user_id',
        'total_amount',
        'payment_method',
        'status',


    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function orderItem()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }



}
