<?php

namespace App\Models;

use App\Observers\MainDistributorObserver;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class MainDistributor extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    public static function boot()
    {
        parent::boot();
        self::observe(new MainDistributorObserver);
    }

    protected $guard = 'main_distributor';
    protected $fillable = [
        'slug',
        'userId',
        'name',
        'email',
        'mobile',
        'status',
        'image',
        'password',
        'device_id',
        'fcm_id',
        'user_balance',
        'employee_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
