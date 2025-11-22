<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * @mixin IdeHelperStockIn
 */
class StockIn extends Model
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'stock_in_id';

    protected $fillable = [
        'product_id',
        'quantity',
        'date'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

}
