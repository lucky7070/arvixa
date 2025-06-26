<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            DistributorSeeder::class,
            MainDistributorSeeder::class,
            RetailerSeeder::class,
            RoleSeeder::class,
            PermissionModuleSeeder::class,
            RolePermissionSeeder::class,
            UserPermissionSeeder::class,
            GeneralSettingSeeder::class,
            StateSeeder::class,
            CitiesSeeder::class,
            CmsSeeder::class,
        ]);
    }
}
