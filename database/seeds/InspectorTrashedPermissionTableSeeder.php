<?php

use Illuminate\Database\Seeder;
use App\Permission;

class InspectorTrashedPermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name'=>'inspectors_trashed-read', 'label'=>'inspectors_trashed-read']);
        Permission::create(['name'=>'inspectors_trashed-restore', 'label'=>'inspectors_trashed-restore']);
    }
}
