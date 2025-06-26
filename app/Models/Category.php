<?php

namespace App\Models;

use App\Observers\CategoryObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    public static function boot()
    {
        parent::boot();
        self::observe(new CategoryObserver);
    }

    protected $fillable = ['name', 'description', 'slug', 'image', 'banner', 'sort_order', 'parent_id', 'is_feature', 'status'];

    public function subcategory()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function parentRecursive()
    {
        return $this->parent()->with('parentRecursive')->withTrashed();
    }

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => asset('storage/' . $value),
        );
    }

    protected function banner(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => asset('storage/' . $value),
        );
    }

    // public function products()
    // {
    //     return $this->belongsToMany(Product::class, 'product_to_categories');
    // }
}
