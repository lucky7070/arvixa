<?php

namespace Database\Seeders;

use App\Models\Retailer;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RetailerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Retailer::create([
            'id'                => 1,
            'slug'              => Str::uuid(),
            'name'              => 'Retailer',
            'email'             => 'retailer@admin.com',
            'mobile'            => '7568457073',
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
