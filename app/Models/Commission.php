<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $fillable = [
        'order_id','store_id','order_amount','commission_rate',
        'commission_amount','seller_amount','status','settled_at',
    ];
    protected $casts = ['settled_at' => 'datetime'];

    public function order() { return $this->belongsTo(Order::class); }
    public function store() { return $this->belongsTo(Store::class); }
}