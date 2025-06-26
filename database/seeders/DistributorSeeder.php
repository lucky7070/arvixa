<?php

namespace Database\Seeders;

use App\Models\Distributor;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DistributorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Distributor::create([
            'id'                => 1,
            'slug'              => Str::uuid(),
            'name'              => 'Distributor',
            'email'             => 'distributor@admin.com',
            'mobile'            => '7568457072',
            'status'            => '1',
            'image'             => 'admin/avatar.png',
            'email_verified_at' => NULL,
            'password'          => Hash::make(123456789),
            'remember_token'    => 'CfaY4OZWO7bLxsnytPwn78B2mxdnGJcW16JNgYawHvCa6x85UMRkNLOyBxn1',
            'created_at'        => Carbon::now(),
            'updated_at'        => Carbon::now(),
        ]);
    }
}
