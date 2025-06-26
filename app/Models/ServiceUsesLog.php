<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

class ServiceUsesLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_type',
        'user_id',
        'customer_id',
        'service_id',
        'request_id',
        'used_in',
        'purchase_rate',
        'sale_rate',
        'main_distributor_id',
        'distributor_id',
        'main_distributor_commission',
        'distributor_commission',
        'is_refunded',
        'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function retailer()
    {
        return $this->belongsTo(Retailer::class, 'user_id', 'id');
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class, 'user_id', 'id');
    }

    public function main_distributor()
    {
        return $this->belongsTo(MainDistributor::class, 'user_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function service()
    {
        return $this->belongsTo(Services::class, 'service_id', 'id');
    }
}
