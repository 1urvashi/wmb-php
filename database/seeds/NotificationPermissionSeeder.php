<?php

use Illuminate\Database\Seeder;
use App\Permission;

class NotificationPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name'=>'Push-Notification_read', 'label'=>'Push-Notification_read']);
        Permission::create(['name'=>'Push-Notification_create', 'label'=>'Push-Notification_create']);
        Permission::create(['name'=>'traders-group_read', 'label'=>'traders-group_read']);
        Permission::create(['name'=>'traders-group_create', 'label'=>'traders-group_create']);
        Permission::create(['name'=>'traders-group_update', 'label'=>'traders-group_update']);
        Permission::create(['name'=>'traders-group_delete', 'label'=>'traders-group_delete']);
        Permission::create(['name'=>'Push-Notification-Templates_read', 'label'=>'Push-Notification-Templates_read']);
        Permission::create(['name'=>'Push-Notification-Templates_create', 'label'=>'Push-Notification-Templates_create']);
        Permission::create(['name'=>'Push-Notification-Templates_update', 'label'=>'Push-Notification-Templates_update']);
        Permission::create(['name'=>'Push-Notification-Templates_delete', 'label'=>'Push-Notification-Templates_delete']);
        Permission::create(['name'=>'auction_deduction-details-read', 'label'=>'auction_deduction-details-read']);
        Permission::create(['name'=>'auction_Export-Completed-Auction', 'label'=>'auction_Export-Completed-Auction']);
    }
}
