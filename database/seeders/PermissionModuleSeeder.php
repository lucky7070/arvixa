<?php

namespace Database\Seeders;

use App\Models\PermissionModule;
use Illuminate\Database\Seeder;

class PermissionModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        //  Permission Array 
        $permissions = [
            [
                'module_id'     => '101',
                'name'          => 'App Setting',
                'can_add'       => 0,
                'can_edit'      => 0,
                'can_delete'    => 0,
                'can_view'      => 0,
                'allow_all'     => 0
            ],
            [
                'module_id'     => '102',
                'name'          => 'Roles',
                'can_add'       => 0,
                'can_edit'      => 0,
                'can_delete'    => 0,
                'can_view'      => 0,
                'allow_all'     => 0
            ],
            [
                'module_id'     => '103',
                'name'          => 'Sub Admin',
                'can_add'       => 0,
                'can_edit'      => 0,
                'can_delete'    => 0,
                'can_view'      => 0,
                'allow_all'     => 0
            ],
            [
                'module_id'     => '104',
                'name'          => 'Main Distributor',
                'can_add'       => 0,
                'can_edit'      => 0,
                'can_delete'    => 0,
                'can_view'      => 0,
                'allow_all'     => 0
            ],
            [
                'module_id'     => '105',
                'name'          => 'Distributor',
                'can_add'       => 0,
                'can_edit'      => 0,
                'can_delete'    => 0,
                'can_view'      => 0,
                'allow_all'     => 0
            ],
            [
                'module_id'     => '106',
                'name'          => 'Retailer',
                'can_add'       => 0,
                'can_edit'      => 0,
                'can_delete'    => 0,
                'can_view'      => 0,
                'allow_all'     => 0
            ],
            [
                'module_id'     => '107',
                'name'          => 'Service',
                'can_add'       => 0,
                'can_edit'      => 0,
                'can_delete'    => 0,
                'can_view'      => 0,
                'allow_all'     => 0
            ],
            [
                'module_id'     => '108',
                'name'          => 'CMS',
                'can_add'       => 0,
                'can_edit'      => 0,
                'can_delete'    => 0,
                'can_view'      => 0,
                'allow_all'     => 0
            ],
            [
                'module_id'     => '109',
                'name'          => 'Payment Request',
                'can_add'       => 0,
                'can_edit'      => 0,
                'can_delete'    => 0,
                'can_view'      => 0,
                'allow_all'     => 0
            ],
            [
                'module_id'     => '110',
                'name'          => 'Location - State',
                'can_add'       => 0,
                'can_edit'      => 0,
                'can_delete'    => 0,
                'can_view'      => 0,
                'allow_all'     => 0
            ],
            [
                'module_id'     => '111',
                'name'          => 'Location - City',
                'can_add'       => 0,
                'can_edit'      => 0,
                'can_delete'    => 0,
                'can_view'      => 0,
                'allow_all'     => 0
            ],
            [
                'module_id'     => '112',
                'name'          => 'Customers',
                'can_add'       => 0,
                'can_edit'      => 0,
                'can_delete'    => 0,
                'can_view'      => 0,
                'allow_all'     => 0
            ],
            [
                'module_id'     => '113',
                'name'          => 'Admin Banners',
                'can_add'       => 0,
                'can_edit'      => 0,
                'can_delete'    => 0,
                'can_view'      => 0,
                'allow_all'     => 0
            ],
            [
                'module_id'     => '114',
                'name'          => 'PanCard Report',
                'can_add'       => 0,
                'can_edit'      => 0,
                'can_delete'    => 0,
                'can_view'      => 0,
                'allow_all'     => 0
            ],
            [
                'module_id'     => '115',
                'name'          => 'Send Email',
                'can_add'       => 0,
                'can_edit'      => 0,
                'can_delete'    => 0,
                'can_view'      => 0,
                'allow_all'     => 0
            ],
            [
                'module_id'     => '116',
                'name'          => 'Employees',
                'can_add'       => 0,
                'can_edit'      => 0,
                'can_delete'    => 0,
                'can_view'      => 0,
                'allow_all'     => 0
            ],
            [
                'module_id'     => '117',
                'name'          => 'Send Notification',
                'can_add'       => 0,
                'can_edit'      => 0,
                'can_delete'    => 0,
                'can_view'      => 0,
                'allow_all'     => 0
            ],

            [
                'module_id'     => '118',
                'name'          => 'Front Web Sliders',
                'can_add'       => 0,
                'can_edit'      => 0,
                'can_delete'    => 0,
                'can_view'      => 0,
                'allow_all'     => 0
            ],
            [
                'module_id'     => '119',
                'name'          => 'Testimonials',
                'can_add'       => 0,
                'can_edit'      => 0,
                'can_delete'    => 0,
                'can_view'      => 0,
                'allow_all'     => 0
            ],
            [
                'module_id'     => '120',
                'name'          => 'FAQ',
                'can_add'       => 0,
                'can_edit'      => 0,
                'can_delete'    => 0,
                'can_view'      => 0,
                'allow_all'     => 0
            ],
            [
                'module_id'     => '121',
                'name'          => 'Enquiries',
                'can_add'       => 0,
                'can_edit'      => 0,
                'can_delete'    => 0,
                'can_view'      => 0,
                'allow_all'     => 0
            ],
        ];

        PermissionModule::insert($permissions);
    }
}
