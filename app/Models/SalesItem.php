<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * @mixin IdeHelperSalesItem
 */
class SalesItem extends Model
{
    use HasFactory, Notifiable;


    protected $primaryKey = 'sales_item_id';
    protected $fillable = [
        'sales_id',
        'product_id',
        'plan_id',
        'quantity',
        'price',
        'sub_total'
    ];

    public function sale()
    {
        return $this->belongsTo(Sales::class, 'sales_id', 'sales_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

        public function plan()
    {
        return $this->belongsTo(MembershipPlan::class, 'plan_id', 'plan_id');
    }
}
