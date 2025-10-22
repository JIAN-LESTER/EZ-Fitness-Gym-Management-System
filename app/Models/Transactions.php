<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Transactions extends Model
{
    use HasFactory, Notifiable;


    protected $primaryKey = 'transaction_id';
    protected $fillable = [
        'sales_id',
        'amount_paid',
        'payment_method',
        'qr_code',
        'transaction_date'
    ];

    public function sale()
    {
        return $this->belongsTo(Sales::class, 'sales_id', 'sales_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

}
