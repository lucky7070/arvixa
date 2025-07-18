<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HsnCode extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'tax_rate',
        'description',
        'status',
    ];
}
