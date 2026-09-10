<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model {
    protected $fillable = ['user_id','session_id','product_id','expires_at'];
    protected $casts    = ['expires_at'=>'datetime'];
    public function product() { return $this->belongsTo(Product::class); }
    public function isExpired(): bool { return $this->expires_at?->isPast() ?? false; }
}