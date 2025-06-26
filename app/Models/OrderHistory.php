<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderHistory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'order_status_id',
        'admin_id',
        'comment',
        'date',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    protected $appends = [
        'order_status',
        'order_status_class',
    ];

    protected function getOrderStatusAttribute()
    {
        return config('constant.order_status_list.' . $this->order_status_id, '');
    }

    protected function getOrderStatusClassAttribute()
    {
        return config('constant.order_status_class_list.' . $this->order_status_id, 'primary');
    }

    protected function date(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => date('d F, Y h:i a', strtotime($value)),
        );
    }
}
