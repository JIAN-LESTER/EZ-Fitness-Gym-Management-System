<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class StockOut extends Model
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'stock_out_id';
    protected $fillable = [
        'product_id',
        'quantity',
        'date',
        'reason',
        'related_sale_id'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
