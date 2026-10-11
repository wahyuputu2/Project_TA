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
        'expires_at',
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
        'expires_at'   => 'datetime',
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
            if (!$order->expires_at && $order->status === 'pending_payment') {
                $order->expires_at = now()->addMinutes(15);
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
            'delivered'       => 'Pesanan Selesai',
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

    // ── EXPIRATION & LOCK LOGIC ──
    public function getEffectiveExpiresAtAttribute()
    {
        return $this->expires_at ?? $this->created_at?->addMinutes(15);
    }

    public function isExpired(): bool
    {
        if ($this->status !== 'pending_payment') {
            return false;
        }
        $expiry = $this->effective_expires_at;
        return $expiry ? $expiry->isPast() : false;
    }

    public static function cancelExpiredOrders(): void
    {
        $expiredOrders = static::where('status', 'pending_payment')
            ->where(function ($query) {
                $query->where('expires_at', '<=', now())
                      ->orWhere(function ($q) {
                          $q->whereNull('expires_at')
                            ->where('created_at', '<=', now()->subMinutes(15));
                      });
            })
            ->with('items.product')
            ->get();

        foreach ($expiredOrders as $order) {
            \Illuminate\Support\Facades\DB::transaction(function () use ($order) {
                $freshOrder = static::where('id', $order->id)->lockForUpdate()->first();
                if (!$freshOrder || $freshOrder->status !== 'pending_payment') {
                    return;
                }

                $freshOrder->load('items.product');
                foreach ($freshOrder->items as $item) {
                    if ($item->product_id) {
                        $product = Product::where('id', $item->product_id)->lockForUpdate()->first();
                        if ($product && $product->status === 'locked') {
                            $product->unlock();
                            \Illuminate\Support\Facades\Log::info("Stok produk [{$product->name}] dilepas karena pesanan {$freshOrder->order_number} kedaluwarsa.");
                        }
                    }
                }

                $freshOrder->update(['status' => 'cancelled']);
                \Illuminate\Support\Facades\Log::info("Pesanan {$freshOrder->order_number} otomatis dibatalkan karena melewati batas waktu 15 menit.");
            });
        }
    }
}