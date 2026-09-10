<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = ['product_id', 'image_path', 'angle', 'sort_order'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function angleLabel(): string
    {
        return match($this->angle) {
            'front'        => 'Depan',
            'back'         => 'Belakang',
            'brand_detail' => 'Detail Merek',
            'minus_detail' => 'Detail Minus',
            default        => 'Lainnya',
        };
    }
}