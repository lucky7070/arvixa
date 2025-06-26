<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cms extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'image'
    ];
}
