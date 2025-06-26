<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PanCard extends Model
{
    use HasFactory;

    protected $table = "service_pancards";
    protected $fillable = [
        'slug',
        'customer_id',
        'user_id',
        'user_type',
        'type',
        'name',
        'middle_name',
        'last_name',
        'email',
        'phone',
        'gender',
        'doc',
        'nsdl_formdata',
        'nsdl_txn_id',
        'nsdl_ack_no',
        'nsdl_complete',
        'is_physical_card',
        'is_refunded',
        'error_message'
    ];

    protected $casts = [
        'created_at_gmt' => 'datetime',
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
}
