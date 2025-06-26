<?php

namespace App\Models;

use App\Observers\ProductStockObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStockLog extends Model
{
    use HasFactory;

    public static function boot()
    {
        parent::boot();
        self::observe(new ProductStockObserver);
    }

    protected $fillable = [
        'voucher_no',
        'type',
        'product_id',
        'amount',
        'current_stock',
        'updated_stock',
        'price',
        'description',
        'order_id',
    ];
}
