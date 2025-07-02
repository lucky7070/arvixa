<?php

namespace App\Models;


use Carbon\Carbon;
use App\Observers\CustomerObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
    use HasFactory, SoftDeletes, Notifiable;

    protected $fillable = [
        'slug',
        'name',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'mobile',
        'status',
        'image',
        'role_id',
        'password',
        'dob',
        'state_id',
        'city_id',
        'gender',
        'userId',
        'device_id',
        'fcm_id',
        'user_balance',
        'registor_from',
        'voucher_id',
    ];

    protected $appends  = ['name'];
    protected $hidden   = ['password'];

    public static function boot()
    {
        parent::boot();
        self::observe(new CustomerObserver);
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn($value, $row) => trim($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']),
            set: function ($value, $row) {
                $value              = explode(' ', $value);
                return [
                    'first_name'    => optional($value)[0],
                    'middle_name'   => optional($value)[1],
                    'last_name'     => optional($value)[2],
                ];
            },

        );
    }

    protected function father_name(): Attribute
    {
        return Attribute::make(
            get: fn($value, $row) => trim($row['father_first_name'] . ' ' . $row['father_middle_name'] . ' ' . $row['father_last_name']),
            set: function ($value, $row) {
                $value              = explode(' ', $value);
                return [
                    'father_first_name'    => optional($value)[0],
                    'father_middle_name'   => optional($value)[1],
                    'father_last_name'     => optional($value)[2],
                ];
            },

        );
    }

    public function bank()
    {
        return $this->hasOne(CustomerBank::class)->select('*')
            ->withDefault(function ($image, $product) {
                $image->fill([
                    'account_bank'      => "",
                    'account_name'      => "",
                    'account_number'    => "",
                    'account_ifsc'      => "",
                ]);
            });
    }

    protected function dateOfBirth(): Attribute
    {
        return Attribute::make(get: fn($value, $row) => Carbon::parse($row['dob']));
    }

    public function documents()
    {
        return $this->hasMany(CustomerDocument::class);
    }

    public function service_used()
    {
        return $this->hasMany(ServiceUsesLog::class)->with(['service:id,name']);
    }

    public function cart()
    {
        return $this->hasMany(CartItem::class, 'user_id', 'id')->where('user_type', 7);
    }
}
