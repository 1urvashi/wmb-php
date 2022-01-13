<?php

use Illuminate\Database\Seeder;
use App\Permission;

class OnboarderRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name'=>'Onboarder_read', 'label'=>'Onboarder_read']);
        Permission::create(['name'=>'Onboarder_create', 'label'=>'Onboarder_create']);
        Permission::create(['name'=>'Onboarder_update', 'label'=>'Onboarder_update']);
        Permission::create(['name'=>'Onboarder_delete', 'label'=>'Onboarder_delete']);
        Permission::create(['name'=>'Onboarder_export', 'label'=>'Onboarder_export']);
        Permission::create(['name'=>'Merge_Onboarder-Trader', 'label'=>'Merge_Onboarder-Trader']);
    }
}
