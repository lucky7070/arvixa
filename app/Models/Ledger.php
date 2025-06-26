<?php

namespace App\Models;

use App\Observers\LedgerObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ledger extends Model
{
    use HasFactory;

    public static function boot()
    {
        parent::boot();
        self::observe(new LedgerObserver);
    }

    protected $fillable = [
        'voucher_no',
        'user_id',
        'user_type',
        'amount',
        'current_balance',
        'updated_balance',
        'payment_type',
        'payment_method',
        'trans_details_json',
        'service_id',
        'request_id',
        'particulars',
        'date',
        'paid_by',
    ];

}
