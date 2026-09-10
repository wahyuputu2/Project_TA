<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'store_id','name','slug','description','condition_notes','price','category_id','brand_id',
        'size','condition','length_cm','width_cm','weight_gram','status',
        'locked_by_session','locked_at','gender',
    ];

    protected $casts = ['price' => 'decimal:2', 'locked_at' => 'datetime'];

    protected static function booted(): void
    {
        static::creating(function ($p) {
            $p->slug = Str::slug($p->name) . '-' . Str::random(6);
        });
    }

    // ── RELATIONS ──
    public function category()   { return $this->belongsTo(Category::class); }
    public function brand()      { return $this->belongsTo(Brand::class); }
    public function images()     { return $this->hasMany(ProductImage::class)->orderBy('sort_order'); }
    public function reviews()    { return $this->hasMany(Review::class); }
    public function orderItems() { return $this->hasMany(OrderItem::class); }

    // ── HELPERS ──
    public function mainImage(): ?ProductImage {
        return $this->images()->where('angle', 'front')->first() ?? $this->images()->first();
    }

    public function averageRating(): float {
        return $this->reviews()->where('is_approved', true)->avg('rating') ?? 0;
    }

    // ── LABELS ──
    public function conditionLabel(): string {
        return match($this->condition) {
            'like_new' => 'Like New',
            default    => 'Kondisi ' . $this->condition,
        };
    }

    public function statusLabel(): string {
        return match($this->status) {
            'available' => 'Tersedia',
            'locked'    => 'Dipesan',
            'sold'      => 'Terjual',
            default     => $this->status,
        };
    }

    public function statusColor(): string {
        return match($this->status) {
            'available' => 'success',
            'locked'    => 'warning',
            'sold'      => 'danger',
            default     => 'secondary',
        };
    }

    // ── STATUS CHECKS ──
    public function isAvailable(): bool { return $this->status === 'available'; }
    public function isLocked(): bool    { return $this->status === 'locked'; }
    public function isSold(): bool      { return $this->status === 'sold'; }

    // ── STOCK LOCK ──
    public function lockForSession(string $sessionId): void {
        $this->update([
            'status'             => 'locked',
            'locked_by_session'  => $sessionId,
            'locked_at'          => now(),
        ]);
    }

    public function unlock(): void {
        $this->update([
            'status'            => 'available',
            'locked_by_session' => null,
            'locked_at'         => null,
        ]);
    }

    public function markSold(): void {
        $this->update([
            'status'            => 'sold',
            'locked_by_session' => null,
            'locked_at'         => null,
        ]);
    }

    public static function releaseExpiredLocks(): void {
        static::where('status', 'locked')
            ->where('locked_at', '<', now()->subMinutes(15))
            ->update([
                'status'            => 'available',
                'locked_by_session' => null,
                'locked_at'         => null,
            ]);
    }

    // ── SCOPES ──
    public function scopeAvailable($q) { return $q->where('status', 'available'); }

    public function scopeFilter($q, array $filters) {
        if (!empty($filters['category']))  $q->whereHas('category', fn($x) => $x->where('slug', $filters['category']));
        if (!empty($filters['brand']))     $q->whereHas('brand',    fn($x) => $x->where('slug', $filters['brand']));
        if (!empty($filters['size']))      $q->where('size',      $filters['size']);
        if (!empty($filters['condition'])) $q->where('condition', $filters['condition']);
        if (!empty($filters['gender']))    $q->where('gender',    $filters['gender']);
        if (!empty($filters['min_price'])) $q->where('price', '>=', $filters['min_price']);
        if (!empty($filters['max_price'])) $q->where('price', '<=', $filters['max_price']);
        if (!empty($filters['search']))    $q->where(fn($x) =>
            $x->where('name', 'like', '%'.$filters['search'].'%')
              ->orWhereHas('brand', fn($b) => $b->where('name', 'like', '%'.$filters['search'].'%'))
        );
        return $q;
    }

    // Tambahkan di app/Models/Product.php

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

}