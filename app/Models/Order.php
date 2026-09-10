<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'store_id',           // ← wajib ada
        'order_number',
        'subtotal',
        'shipping_cost',
        'total',
        'status',
        'notes',
        'shipping_name',
        'shipping_phone',
        'shipping_address',
        'shipping_city',
        'shipping_province',
        'shipping_postal_code',
        'courier',
        'courier_service',
        'tracking_number',
        'payment_method',
        'payment_channel',
        'payment_token',
        'payment_url',
        'paid_at',
        'shipped_at',
        'delivered_at',
    ];

    protected $casts = [
        'paid_at'      => 'datetime',
        'shipped_at'   => 'datetime',
        'delivered_at' => 'datetime',
        'subtotal'     => 'decimal:2',
        'shipping_cost'=> 'decimal:2',
        'total'        => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function ($order) {
            if (!$order->order_number) {
                $order->order_number = 'TF-' . strtoupper(Str::random(8));
            }
        });
    }

    // ── RELATIONS ──
    public function user()       { return $this->belongsTo(User::class); }
    public function store()      { return $this->belongsTo(Store::class); }
    public function items()      { return $this->hasMany(OrderItem::class); }
    public function reviews()    { return $this->hasMany(Review::class); }
    public function paymentLogs(){ return $this->hasMany(PaymentLog::class); }
    public function commissions(){ return $this->hasMany(Commission::class); }

    // ── STATUS ──
    public function statusLabel(): string {
        return match($this->status) {
            'pending_payment' => 'Menunggu Pembayaran',
            'paid'            => 'Pembayaran Diterima',
            'processing'      => 'Sedang Dikemas',
            'shipped'         => 'Dikirim',
            'delivered'       => 'Selesai',
            'cancelled'       => 'Dibatalkan',
            'refunded'        => 'Dikembalikan',
            default           => ucfirst($this->status),
        };
    }

    public function statusColor(): string {
        return match($this->status) {
            'pending_payment' => 'warning',
            'paid'            => 'info',
            'processing'      => 'primary',
            'shipped'         => 'purple',
            'delivered'       => 'success',
            'cancelled'       => 'danger',
            default           => 'secondary',
        };
    }
}