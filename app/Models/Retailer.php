<?php

namespace App\Models;

use App\Observers\RetailerObserver;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Retailer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    public static function boot()
    {
        parent::boot();
        self::observe(new RetailerObserver);
    }

    protected $guard = 'retailer';
    protected $fillable = [
        'slug',
        'userId',
        'name',
        'email',
        'mobile',
        'status',
        'image',
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
        'password',
        'registor_from',
        'device_id',
        'fcm_id',
        'voucher_id',
        'distributor_id',
        'user_balance',
        'main_distributor_id',
        'default_water_board',
        'default_gas_board',
        'default_lic_board',
        'default_electricity_board'
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

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function cart()
    {
        return $this->hasMany(CartItem::class, 'user_id', 'id')->where('user_type', 4);
    }

    public function main_distributor()
    {
        return $this->belongsTo(MainDistributor::class);
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function pancards()
    {
        return $this->hasMany(PanCard::class, 'user_id', 'id');
    }

    public function notes()
    {
        return $this->hasMany(EmployeeNote::class, 'retailer_id', 'id')->orderBy('created_at', 'desc');
    }

    public function ledgers()
    {
        return $this->hasMany(Ledger::class, 'user_id', 'id')->where('user_type', 7);
    }
    public function electricity_bill()
    {
        return $this->hasMany(Bill::class, 'user_id', 'id');
    }
}
