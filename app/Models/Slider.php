<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;

    protected $fillable = [
        'heading',
        'sub_heading',
        'status',
        'url',
        'is_special',
        'image'
    ];
}
