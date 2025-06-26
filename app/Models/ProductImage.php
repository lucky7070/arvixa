<?php

namespace App\Models;

use App\Observers\ProductImageObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'image',
        'sort_order'
    ];

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => asset('storage/' . $value),
        );
    }
}
