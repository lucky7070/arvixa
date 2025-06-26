<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Observers\PaymentRequestObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentRequest extends Model
{
    use HasFactory;

    public static function boot()
    {
        parent::boot();
        self::observe(new PaymentRequestObserver);
    }

    protected $fillable = [
        'user_id',
        'request_number',
        'user_type',
        'title',
        'amount',
        'description',
        'attachment',
        'status',
        'reason'
    ];
}
