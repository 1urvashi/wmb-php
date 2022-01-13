<?php

use Illuminate\Database\Seeder;
use App\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name'=>'dashboard_read', 'label'=>'dashboard_read']);
        Permission::create(['name'=>'notification_read', 'label'=>'notification_read']);
        Permission::create(['name'=>'branches_read', 'label'=>'branches_read']);
        Permission::create(['name'=>'branches_create', 'label'=>'branches_create']);
        Permission::create(['name'=>'branches_update', 'label'=>'branches_update']);
        Permission::create(['name'=>'branches_delete', 'label'=>'branches_delete']);
        Permission::create(['name'=>'branches_export', 'label'=>'branches_export']);
        Permission::create(['name'=>'branch-managers_read', 'label'=>'branch-managers_read']);
        Permission::create(['name'=>'branch-managers_create', 'label'=>'branch-managers_create']);
        Permission::create(['name'=>'branch-managers_update', 'label'=>'branch-managers_update']);
        Permission::create(['name'=>'branch-managers_delete', 'label'=>'branch-managers_delete']);
        Permission::create(['name'=>'branch-managers_export', 'label'=>'branch-managers_export']);
        Permission::create(['name'=>'traders_read', 'label'=>'traders_read']);
        Permission::create(['name'=>'traders_create', 'label'=>'traders_create']);
        Permission::create(['name'=>'traders_update', 'label'=>'traders_update']);
        Permission::create(['name'=>'traders_delete', 'label'=>'traders_delete']);
        Permission::create(['name'=>'traders_export', 'label'=>'traders_export']);
        Permission::create(['name'=>'DRM_read', 'label'=>'DRM_read']);
        Permission::create(['name'=>'DRM_create', 'label'=>'DRM_create']);
        Permission::create(['name'=>'DRM_update', 'label'=>'DRM_update']);
        Permission::create(['name'=>'DRM_delete', 'label'=>'DRM_delete']);
        Permission::create(['name'=>'DRM_export', 'label'=>'DRM_export']);
        Permission::create(['name'=>'merge_trader', 'label'=>'merge_trader']);
        Permission::create(['name'=>'inspectors_read', 'label'=>'inspectors_read']);
        Permission::create(['name'=>'inspectors_create', 'label'=>'inspectors_create']);
        Permission::create(['name'=>'inspectors_update', 'label'=>'inspectors_update']);
        Permission::create(['name'=>'inspectors_delete', 'label'=>'inspectors_delete']);
        Permission::create(['name'=>'inspectors_export', 'label'=>'inspectors_export']);
        Permission::create(['name'=>'vehicles_read', 'label'=>'vehicles_read']);
        Permission::create(['name'=>'vehicles_update', 'label'=>'vehicles_auction']);
        Permission::create(['name'=>'vehicles_delete', 'label'=>'vehicles_delete']);
        Permission::create(['name'=>'vehicles_export', 'label'=>'vehicles_export']);
        Permission::create(['name'=>'vehicles_download', 'label'=>'vehicles_download']);
        Permission::create(['name'=>'vehicles_submit-auction', 'label'=>'vehicles_auction']);
        Permission::create(['name'=>'vehicles-under-auction_read', 'label'=>'vehicles-under-auction_read']);
        Permission::create(['name'=>'vehicles-under-auction_update', 'label'=>'vehicles-under-auction_auction']);
        Permission::create(['name'=>'vehicles-under-auction_delete', 'label'=>'vehicles-under-auction_delete']);
        Permission::create(['name'=>'vehicles-under-auction_export', 'label'=>'vehicles-under-auction_export']);
        Permission::create(['name'=>'vehicles-under-auction_submit-auction', 'label'=>'vehicles-under-auction_auction']);
        Permission::create(['name'=>'auction_ongoing', 'label'=>'auction_ongoing']);
        Permission::create(['name'=>'auction_closed', 'label'=>'auction_closed']);
        Permission::create(['name'=>'auction_qualitycheck', 'label'=>'auction_qualitycheck']);
        Permission::create(['name'=>'auction_passcheck', 'label'=>'auction_passcheck']);
        Permission::create(['name'=>'auction_failcheck', 'label'=>'auction_failcheck']);
        Permission::create(['name'=>'auction_sold', 'label'=>'auction_sold']);
        Permission::create(['name'=>'auction_cash', 'label'=>'auction_cash']);
        Permission::create(['name'=>'auction_scheduled', 'label'=>'auction_scheduled']);
        Permission::create(['name'=>'auction_canceled', 'label'=>'auction_canceled']);
        Permission::create(['name'=>'auction_cancel-closed', 'label'=>'auction_cancel-closed']);
        Permission::create(['name'=>'auction-button_stop', 'label'=>'auction-button_stop']);
        Permission::create(['name'=>'auction-button_reopen', 'label'=>'auction-button_reopen']);
        Permission::create(['name'=>'auction-button_view', 'label'=>'auction-button_view']);
        Permission::create(['name'=>'auction-button_negotiate', 'label'=>'auction-button_negotiate']);
        Permission::create(['name'=>'auction-button_qualitycheck', 'label'=>'auction-button_qualitycheck']);
        Permission::create(['name'=>'auction-button_inspector-negotiate', 'label'=>'auction-button_inspector-negotiate']);
        Permission::create(['name'=>'auction-button_override-bid-amount', 'label'=>'auction-button_override-bid-amount']);
        Permission::create(['name'=>'auction-button_negotiate-with-bid-owner', 'label'=>'auction-button_Negotiate-with-bid-owner']);
        Permission::create(['name'=>'auction-button_cancel', 'label'=>'auction-button_cancel']);
        Permission::create(['name'=>'auction-button_cancel-closed', 'label'=>'auction-button_cancel-closed']);
        Permission::create(['name'=>'auction-button_send-reminder', 'label'=>'auction-button_send-reminder']);
        Permission::create(['name'=>'auction-button_pass', 'label'=>'auction-button_pass']);
        Permission::create(['name'=>'auction-button_fail', 'label'=>'auction-button_fail']);
        Permission::create(['name'=>'auction-button_ready-to-sale', 'label'=>'auction-button_ready-to-sale']);
        Permission::create(['name'=>'auction-button_download-and-print', 'label'=>'auction-button_download-and-print']);
        Permission::create(['name'=>'auction-button_cash', 'label'=>'auction-button_cash']);
        Permission::create(['name'=>'auction-column_base-price-read', 'label'=>'auction-column_base-price-read']);
        Permission::create(['name'=>'auction-column_minimum-price-read', 'label'=>'auction-column_minimum-price-read']);
        Permission::create(['name'=>'auction-column_current-price-read', 'label'=>'auction-column_current-price-read']);
        Permission::create(['name'=>'customers_read', 'label'=>'customers_read']);
        Permission::create(['name'=>'customers_create', 'label'=>'customers_create']);
        Permission::create(['name'=>'customers_update', 'label'=>'customers_update']);
        Permission::create(['name'=>'customers_delete', 'label'=>'customers_delete']);
        Permission::create(['name'=>'priceType_vat-update', 'label'=>'priceType_vat-update']);
        Permission::create(['name'=>'priceType_salestype-read', 'label'=>'priceType_salestype-read']);
        Permission::create(['name'=>'priceType_salestype-create', 'label'=>'priceType_salestype-create']);
        Permission::create(['name'=>'priceType_salestype-update', 'label'=>'priceType_salestype-update']);
        Permission::create(['name'=>'priceType_salestype-delete', 'label'=>'priceType_salestype-delete']);
        Permission::create(['name'=>'priceType_profit-Margin-read', 'label'=>'priceType_profit-Margin-read']);
        Permission::create(['name'=>'priceType_profit-Margin-create', 'label'=>'priceType_profit-Margin-create']);
        Permission::create(['name'=>'priceType_profit-Margin-update', 'label'=>'priceType_profit-Margin-update']);
        Permission::create(['name'=>'priceType_profit-Margin-delete', 'label'=>'priceType_profit-Margin-delete']);
        Permission::create(['name'=>'roles_read', 'label'=>'roles_read']);
        Permission::create(['name'=>'roles_create', 'label'=>'roles_create']);
        Permission::create(['name'=>'roles_update', 'label'=>'roles_update']);
        Permission::create(['name'=>'roles_delete', 'label'=>'roles_delete']);
        Permission::create(['name'=>'permissions_read', 'label'=>'permissions_read']);
        Permission::create(['name'=>'permissions_create', 'label'=>'permissions_create']);
        Permission::create(['name'=>'permissions_update', 'label'=>'permissions_update']);
        Permission::create(['name'=>'permissions_delete', 'label'=>'permissions_delete']);
        Permission::create(['name'=>'history_read', 'label'=>'history_read']);
        Permission::create(['name'=>'history_export', 'label'=>'history_export']);
        Permission::create(['name'=>'attributeSet_read', 'label'=>'attributeSet_read']);
        Permission::create(['name'=>'attributeSet_create', 'label'=>'attributeSet_create']);
        Permission::create(['name'=>'attributeSet_update', 'label'=>'attributeSet_update']);
        Permission::create(['name'=>'attributeSet_delete', 'label'=>'attributeSet_delete']);
        Permission::create(['name'=>'attribute_read', 'label'=>'attribute_read']);
        Permission::create(['name'=>'attribute_create', 'label'=>'attribute_create']);
        Permission::create(['name'=>'attribute_update', 'label'=>'attribute_update']);
        Permission::create(['name'=>'attribute_delete', 'label'=>'attribute_delete']);
        Permission::create(['name'=>'make_read', 'label'=>'make_read']);
        Permission::create(['name'=>'make_create', 'label'=>'make_create']);
        Permission::create(['name'=>'make_update', 'label'=>'make_update']);
        Permission::create(['name'=>'make_delete', 'label'=>'make_delete']);
        Permission::create(['name'=>'model_read', 'label'=>'model_read']);
        Permission::create(['name'=>'model_create', 'label'=>'model_create']);
        Permission::create(['name'=>'model_update', 'label'=>'model_update']);
        Permission::create(['name'=>'model_delete', 'label'=>'model_delete']);
        Permission::create(['name'=>'model_import', 'label'=>'model_import']);
        Permission::create(['name'=>'bank_read', 'label'=>'bank_read']);
        Permission::create(['name'=>'bank_create', 'label'=>'bank_create']);
        Permission::create(['name'=>'bank_update', 'label'=>'bank_update']);
        Permission::create(['name'=>'bank_delete', 'label'=>'bank_delete']);
        Permission::create(['name'=>'users_read', 'label'=>'users_read']);
        Permission::create(['name'=>'users_create', 'label'=>'users_create']);
        Permission::create(['name'=>'users_update', 'label'=>'users_update']);
        Permission::create(['name'=>'users_delete', 'label'=>'users_delete']);
        Permission::create(['name'=>'users_export', 'label'=>'users_export']);
        Permission::create(['name'=>'page_terms-read', 'label'=>'page_terms-read']);
        Permission::create(['name'=>'page_terms-update', 'label'=>'page_terms-update']);
        Permission::create(['name'=>'page_about-read', 'label'=>'page_about-read']);
        Permission::create(['name'=>'page_about-update', 'label'=>'page_about-update']);
        Permission::create(['name'=>'page_faq-read', 'label'=>'page_faq-read']);
        Permission::create(['name'=>'page_faq-update', 'label'=>'page_faq-update']);
        Permission::create(['name'=>'page_privacy-read', 'label'=>'page_privacy-read']);
        Permission::create(['name'=>'page_privacy-update', 'label'=>'page_privacy-update']);
        Permission::create(['name'=>'page_contact-read', 'label'=>'page_contact-read']);
        Permission::create(['name'=>'page_contact-update', 'label'=>'page_contact-update']);
        Permission::create(['name'=>'settings_password-change', 'label'=>'settings_password-change']);
        Permission::create(['name'=>'settings_version-control', 'label'=>'settings_version-control']);
        Permission::create(['name'=>'Bid-History_read', 'label'=>'Bid-History_read']);
        Permission::create(['name'=>'Bid-History_Owner-View', 'label'=>'Bid-History_Owner-View']);
    }
}
