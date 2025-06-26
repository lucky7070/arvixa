<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_type',
        'product_id',
        'qty',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id')
            ->select('id', 'name', 'sku', 'slug', 'brand_id', 'category_id', 'stock', 'price', 'minimum', 'maximum', 'mrp', 'hsn_code_id', 'weight', 'length', 'width', 'height')
            ->where('status', 1)
            ->with(['main_image', 'brand', 'hsn_code'])
            ->withDefault(function ($product) {
                $product->fill([
                    'stock'     => 0,
                    'minimum'   => 0,
                    'maximum'   => 0,
                    'price'     => 0,
                    'mrp'       => 0,
                    'status'    => 0,
                ]);
            });
    }
}
