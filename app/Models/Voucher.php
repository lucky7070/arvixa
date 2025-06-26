<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'slug',
        'code',
        'name',
        'description',
        'max_uses',
        'max_uses_per_user',
        'starts_at',
        'expires_at',
        'type',
        'is_fixed',
        'discount_amount',
        'max_discount',
        'min_cart_value',
        'is_free_shipping',
        'status',
        'is_public',
    ];

    protected $casts = [
        'starts_at'     => 'datetime',
        'expires_at'    => 'datetime',
    ];

    public function voucher_use()
    {
        return $this->hasMany(VoucherUse::class, 'voucher_id');
    }
}
