<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_sku',
        'quantity',
        'unit_price',
        'tax_rate',
        'hsn_code',
        'total_price',
    ];


    protected $appends = ['unit_price_without_tax'];

    protected function unitPriceWithoutTax(): Attribute
    {
        return new Attribute(
            get: fn ($value, array $attr) => round(($attr['unit_price'] - $attr['unit_price'] * $attr['tax_rate'] / ($attr['tax_rate'] + 100)), 2),
        );
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id')->with(['main_image', 'brand', 'hsn_code']);
    }
}
