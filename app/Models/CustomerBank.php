<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerBank extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'account_bank',
        'account_name',
        'account_number',
        'account_ifsc',
    ];
}
