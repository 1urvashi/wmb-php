<?php

use Illuminate\Database\Seeder;
use App\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      Role::create(['name'=>'admin', 'label' => 'Admin']);
      Role::create(['name'=>'auction_controller', 'label' => 'Auction Controller']);
      Role::create(['name'=>'quality_control', 'label' => 'Quality Control']);
      Role::create(['name'=>'document_controller', 'label' => 'Document controller']);
      Role::create(['name'=>'head_of_drm', 'label' => 'Head of DRM']);
      Role::create(['name'=>'drm_user', 'label' => 'DRM']);
    }
}
