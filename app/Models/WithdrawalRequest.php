<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Observers\WithdrawalRequestObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WithdrawalRequest extends Model
{
    use HasFactory;

    public static function boot()
    {
        parent::boot();
        self::observe(new WithdrawalRequestObserver);
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
