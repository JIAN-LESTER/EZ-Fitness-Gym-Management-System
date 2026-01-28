<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * @mixin IdeHelperProduct
 */
class Product extends Model
{
    use HasFactory, Notifiable;


    protected $primaryKey = 'product_id';
    protected $fillable = [
        'category_id',
        'branch_id',
        'name',
        'description',
        'price',
        'status',
        'image',


    ];

    public function inventory()
    {
        return $this->hasOne(Inventory::class, 'product_id', 'product_id');
    }

    public function category()
    {
        return $this->belongsTo(Categories::class, 'category_id', 'category_id');
    }

    public function transaction()
    {
        return $this->hasMany(Transactions::class, 'product_id', 'product_id');
    }


    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'product_id', 'product_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branches::class, 'branch_id', 'branch_id');
    }
}
