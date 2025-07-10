<?php

namespace App\Models;

use App\Observers\DistributorObserver;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Distributor extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    public static function boot()
    {
        parent::boot();
        self::observe(new DistributorObserver);
    }

    protected $guard = 'distributor';
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
        'main_distributor_id',
        'employee_id',
        'date_of_birth',
        'gender',
        'address',
        'shop_name',
        'shop_address',
        'aadhar_no',
        'pan_no',
        'aadhar_doc',
        'pan_doc',
        'bank_proof_doc',
        'bank_name',
        'bank_account_number',
        'bank_ifsc_code',
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

    public function main_distributor()
    {
        return $this->belongsTo(MainDistributor::class, 'main_distributor_id', 'id');
    }
}
