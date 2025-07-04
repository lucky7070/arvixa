<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicesLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_id',
        'user_type',
        'status',
        'assign_date',
        'decline_date',
        'purchase_rate',
        'sale_rate',
        'main_distributor_id',
        'distributor_id',
        'main_distributor_commission',
        'distributor_commission',
        'retailer_commission',
        'commission_slots',
    ];

    protected $casts = [
        'commission_slots' => 'array',
    ];

    public function service()
    {
        return $this->belongsTo(Services::class, 'service_id', 'id');
    }
}
