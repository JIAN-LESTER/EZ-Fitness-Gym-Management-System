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
        'branch_id',
        'plan_id',
        'subscription_id',
        'type',
        'performed_by',
        'quantity'

    ];

    /**
     * Relationship to Sales (for sales transactions and membership purchases)
     */
    public function sale()
    {
        return $this->belongsTo(Sales::class, 'sales_id', 'sales_id');
    }
    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by', 'user_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            Sales::class,
            'sales_id', // Foreign key on sales table
            'user_id',  // Foreign key on users table
            'sales_id', // Local key on transactions table
            'user_id'   // Local key on sales table
        );
    }

    /**
     * Check if transaction is a product sale
     */
    public function isProductSale()
    {
        return $this->type === 'sales' && $this->sale && $this->sale->type === 'products';
    }

    /**
     * Check if transaction is a membership sale
     */
    public function isMembershipSale()
    {
        return $this->type === 'sales' && $this->sale && $this->sale->type === 'memberships';
    }

    /**
     * Check if transaction is a stock movement
     */
    public function isStockMovement()
    {
        return in_array($this->type, ['stock_in', 'stock_out']);
    }

    public function branch()
    {
        return $this->belongsTo(Branches::class, 'branch_id', 'branch_id');
    }
}
