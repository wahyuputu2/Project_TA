<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    protected $fillable = [
        'order_id',
        'transaction_id',
        'payment_type',
        'transaction_status',
        'gross_amount',
        'raw_response',
    ];

    protected $casts = [
        'raw_response' => 'array',
        'gross_amount' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}