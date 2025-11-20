<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * @mixin IdeHelperOrderItem
 */
class OrderItem extends Model
{
   use HasFactory, Notifiable;


  protected $primaryKey = 'order_item_id';
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'sub_total',



    ];

    public function order() {
    return $this->belongsTo(Orders::class, 'order_id', 'order_id');
}

public function product() {
    return $this->belongsTo(Product::class, 'product_id', 'product_id');
}

}