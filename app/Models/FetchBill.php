<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FetchBill extends Model
{
    protected $table = 'fetch_bills';

    protected $fillable = [
        'transaction_id',
        'service_id',
        'user_id',
        'board_id',
        'consumer_no',
        'consumer_name',
        'bill_no',
        'bill_amount',
        'due_date',
    ];
}
