<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Review extends Model {
    protected $fillable = ['user_id','product_id','order_id','rating','comment','photos','is_approved'];
    protected $casts    = ['photos'=>'array','is_approved'=>'boolean'];
    public function user()    { return $this->belongsTo(User::class); }
    public function product() { return $this->belongsTo(Product::class); }
    public function order()   { return $this->belongsTo(Order::class); }
}