<?php

namespace App\Models;

use App\Observers\LedgerObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UpiPayments extends Model
{
    use HasFactory;

    protected $fillable = [
        'voucher_no',
        'user_id',
        'user_type',
        'amount'
    ];
}
