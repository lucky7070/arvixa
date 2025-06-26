<?php

namespace App\Models;

use App\Observers\OrderObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    public static function boot()
    {
        parent::boot();
        self::observe(new OrderObserver);
    }

    protected $fillable = [
        'slug',
        'voucher_no',
        'user_id',
        'user_type',
        'date',
        'customer_name_1',
        'customer_name_2',
        'customer_email',
        'customer_mobile',
        'shipping_address_1',
        'shipping_address_2',
        'shipping_city',
        'shipping_state',
        'shipping_postcode',
        'user_comment',
        'order_status_id',
        'sub_total',
        'tax',
        'delivery',
        'discount',
        'total',
        'discount_details',
        'shiprocket_order_id',
        'shiprocket_tracking_activity'
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function retailer()
    {
        return $this->belongsTo(Retailer::class, 'user_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'user_id', 'id');
    }

    public function products()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function history()
    {
        return $this->hasMany(OrderHistory::class);
    }

    protected $appends = [
        'order_status'
    ];

    protected function getOrderStatusAttribute()
    {
        return config('constant.order_status_list.' . $this->order_status_id, '');
    }

    protected function getOrderStatusClassAttribute()
    {
        return config('constant.order_status_class_list.' . $this->order_status_id, 'primary');
    }
}
