<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannerAdmin extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'status',
        'banner_for',
        'url',
        'is_special',
    ];
}
