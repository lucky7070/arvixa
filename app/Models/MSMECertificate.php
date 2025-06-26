<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MSMECertificate extends Model
{
    use HasFactory;

    protected $table = "service_msme_certificates";
    protected $fillable = [
        'useFrom',
        'txn_id',
        'customer_id',
        'user_id',
        'user_type',
        'name',
        'email',
        'phone',
        'aadharcard',
        'aadhar_file',
        'pancard_has',
        'pancard_type',
        'pancard',
        'pancard_file',
        'category',
        'gender',
        'special_abled',
        'name_enterprise',
        'name_plant',
        'flat_plant',
        'building_plant',
        'block_plant',
        'street_plant',
        'village_plant',
        'city',
        'state',
        'country',
        'pincode',
        'official_flat',
        'official_building',
        'official_block',
        'official_street',
        'official_village',
        'official_city',
        'official_state',
        'official_country',
        'official_pincode',
        'uam_register',
        'uam_number',
        'enterprise_registration',
        'enterprise_production',
        'enterprise_date',
        'bank_name',
        'bank_ifsc',
        'bank_account',
        'unit_type',
        'nic_description',
        'nic_type',
        'nic_2',
        'nic_4',
        'nic_5',
        'emp_male',
        'emp_female',
        'emp_other',
        'emp_total',
        'inv_wdv_a',
        'inv_wdv_b',
        'inv_wdv_total',
        'turnover_a',
        'turnover_b',
        'turnover_total',
        'get_register',
        'treds_rgister',
        'district_center',
        'certificate',
        'comment',
        'is_refunded',
        'error_message',
        'status',
    ];

    protected $casts = [
        'enterprise_registration'   => 'date',
        'enterprise_date'           => 'date',
    ];

    public function retailer()
    {
        return $this->belongsTo(Retailer::class, 'user_id', 'id');
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class, 'user_id', 'id');
    }

    public function main_distributor()
    {
        return $this->belongsTo(MainDistributor::class, 'user_id', 'id');
    }
}
