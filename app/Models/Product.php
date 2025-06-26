<?php

namespace App\Models;

use App\Observers\ProductObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{

    public static function boot()
    {
        parent::boot();
        self::observe(new ProductObserver);
    }

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'slug',
        'sku',
        'brand_id',
        'category_id',
        'name',
        'sort_description',
        'description',
        'stock',
        'price',
        'mrp',
        'weight',
        'length',
        'width',
        'height',
        'hsn_code_id',
        'status',
        'minimum',
        'maximum',
        'sort_order',
        'meta_title',
        'meta_keyword',
        'meta_description',
        'is_feature'
    ];

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }

    public function main_image()
    {
        return $this->hasOne(ProductImage::class)->select('image', 'product_id',)->orderBy('sort_order', 'asc')
            ->withDefault(function ($image, $product) {
                $image->fill([
                    'product_id'    => $product->id,
                    'image'         => 'product/default.jpg',
                    'sort_order'    => 0
                ]);
            });
    }

    public function category()
    {
        return $this->belongsTo(Category::class)->with('parentRecursive')->withTrashed();
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class)->select('name', 'id')->withTrashed();
    }

    public function hsn_code()
    {
        return $this->belongsTo(HsnCode::class)->select('code', 'id', 'tax_rate')->withTrashed();
    }
}
