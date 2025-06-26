<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductUpdateLog;

class ProductObserver
{
    public function creating(Product $product)
    {
        $product->slug = makeSlug($product->name);
    }

    public function created(Product $product)
    {
        $product->sku = orderId($product->id, 'I', 6);
        $product->saveQuietly();
    }

    public function updating(Product $category)
    {
        if ($category->isDirty('name')) {
            $category->slug = makeSlug($category->name);
        }
    }
}
