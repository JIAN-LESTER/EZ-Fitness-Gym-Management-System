<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    use HasFactory;

    protected $table = 'sales';

    protected $primaryKey = 'sales_id';

    protected $fillable = [
        'user_id',
        'branch_id',
        'total_amount',
        'tax',
        'discount',
        'payment_method',
        'reference_code',
        'status',
        'type',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    /**
     * FIXED: Get all items in this sale
     *
     * The issue was Laravel was trying to use 'sale_items.sales_id'
     * but concatenating the model name incorrectly.
     *
     * Explicitly specify the foreign key to avoid this.
     */
    public function items()
    {
        // Explicitly specify: table class, foreign key on sales_items, local key on sales
        return $this->hasMany(SalesItem::class, 'sales_id', 'sales_id');
    }

    /**
     * Get the user (cashier/member) who made this sale
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get transactions related to this sale
     */
    public function transactions()
    {
        return $this->hasMany(Transactions::class, 'sales_id', 'sales_id');
    }

    /**
     * Get the branch where this sale was made
     */
    public function branch()
    {
        return $this->belongsTo(Branches::class, 'branch_id', 'branch_id');
    }

    /**
     * Scope: Get sales for a specific branch
     */
    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope: Get paid sales
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope: Get pending sales
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Check if sale is paid
     */
    public function isPaid()
    {
        return $this->status === 'paid';
    }

    /**
     * Check if sale is pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if sale is cancelled
     */
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    /**
     * Get formatted total amount
     */
    public function getFormattedTotalAttribute()
    {
        return '₱'.number_format($this->total_amount, 2);
    }
}
