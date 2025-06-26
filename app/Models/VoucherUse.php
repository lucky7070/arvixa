<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherUse extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_type',
        'voucher_id',
        'order_id',
    ];
}
