<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'doc_type',
        'doc_number',
        'doc_img_front',
        'doc_img_back',
    ];
}
