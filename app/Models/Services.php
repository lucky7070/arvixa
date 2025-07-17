<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Services extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'slug',
        'name',
        'description',
        'status',
        'image',
        'banner',
        'purchase_rate',
        'sale_rate',
        'default_d_commission',
        'default_md_commission',
        'default_r_commission',
        'commission_slots',
        'default_assign',
        'is_feature',
        'btn_text',
        'notice',
    ];

    protected $casts = [
        'commission_slots' => 'array',
    ];
}
