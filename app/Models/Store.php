<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Store extends Model
{
    protected $fillable = [
        'user_id','name','slug','description','logo','banner',
        'phone','address','city','province','postal_code','origin_city_id',
        'status','bank_name','bank_account_number','bank_account_name',
        'balance','commission_rate','total_sales','rating',
    ];

    protected $casts = [
        'balance'         => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'rating'          => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(fn($s) => $s->slug = Str::slug($s->name) . '-' . Str::random(4));
    }

    public function user()        { return $this->belongsTo(User::class); }
    public function products()    { return $this->hasMany(Product::class); }
    public function orders()      { return $this->hasMany(Order::class); }
    public function withdrawals() { return $this->hasMany(Withdrawal::class); }
    public function commissions() { return $this->hasMany(Commission::class); }
    public function reviews()     { return $this->hasManyThrough(Review::class, Product::class); }

    public function isActive(): bool    { return $this->status === 'active'; }
    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isSuspended(): bool { return $this->status === 'suspended'; }

    public function statusLabel(): string {
        return match($this->status) {
            'active'    => 'Aktif',
            'pending'   => 'Menunggu Verifikasi',
            'suspended' => 'Ditangguhkan',
            default     => $this->status,
        };
    }

    public function availableProducts()
    {
        return $this->products()->where('status', 'available');
    }

    public function totalRevenue(): float
    {
        return $this->commissions()
            ->where('status', 'settled')
            ->sum('seller_amount');
    }

    public function pendingBalance(): float
    {
        return $this->commissions()
            ->where('status', 'pending')
            ->sum('seller_amount');
    }

    // Tambah saldo setelah order selesai
    public function addBalance(float $amount): void
    {
        $this->increment('balance', $amount);
    }
}