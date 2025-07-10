<?php

namespace App\Models;

use App\Observers\UserObserver;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    public static function boot()
    {
        parent::boot();
        self::observe(new UserObserver);
    }

    protected $fillable = [
        'slug',
        'role_id',
        'name',
        'email',
        'mobile',
        'status',
        'image',
        'password',
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

    public function permission()
    {
        return $this->hasMany(UserPermission::class)->with('permission_name:id,name');
    }

    // protected function image(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn ($value) => asset('storage/' . $value),
    //     );
    // }
}
