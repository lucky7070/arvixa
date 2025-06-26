<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Observers\EmployeeObserver;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Employee extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    public static function boot()
    {
        parent::boot();
        self::observe(new EmployeeObserver);
    }

    protected $guard = 'employee';
    protected $fillable = [
        'slug',
        'userId',
        'designation_id',
        'name',
        'email',
        'mobile',
        'status',
        'image',
        'city_id',
        'state_id',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    public function retailer()
    {
        return $this->hasMany(Retailer::class);
    }
}
