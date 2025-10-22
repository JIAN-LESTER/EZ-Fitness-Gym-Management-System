<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Product extends Model
{
    use HasFactory, Notifiable;


    protected $primaryKey = 'product_id';
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'status',


    ];

    public function inventory()
    {
        return $this->hasOne(Inventory::class, 'inventory_id', 'inventory_id');
    }

    public function category()
    {
        return $this->belongsTo(Categories::class, 'category_id', 'category_id');
    }


    public function stockIns()
    {
        return $this->hasMany(StockIn::class, 'stock_in_id', 'stock_in_id');
    }

    public function stockOuts()
    {
        return $this->hasMany(StockOut::class, 'stock_out_id', 'stock_out_id');
    }
}
