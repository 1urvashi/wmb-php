<?php

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the controller to call when that URI is requested.
  |
 */

if ( (env('APP_ENV') == 'production') || (env('APP_ENV') == 'prod3')) {
     URL::forceSchema('https');
}
Route::get('/', function () {
    return Redirect::to('en/login');
});
Route::get('/urvi', function () {
    return   Artisan::call('auction:cron');
});


Route::get('inspector-generatePdf/{id}', 'Controller@generatePdf');

Route::get('/login', function () {
    return Redirect::to('en/login');
});

Route::get('trader/home', function () {
    return Redirect::to('en/home');
});
Route::get('/registration', function () {
    return Redirect::to('en/registration');
});

Route::get('sms', 'Controller@testSms');
// Route::get('testPush', 'Controller@sendNotificationToDevice');
Route::get('testPush/{type}/{tId}', 'Controller@sendNotificationToDevice');
//Route::get('migrateTwilio', ['as' => 'test', 'uses' => 'Api\V1\Trader\NotificationsController@test']);


Route::get('admin/login', 'Admin\Auth\AuthController@showLoginForm');
Route::post('admin/login', 'Admin\Auth\AuthController@login');
Route::get('cron', 'CronController@index');

Route::get('dealer/login', 'Dealer\Auth\AuthController@showLoginForm');
Route::post('dealer/login', 'Dealer\Auth\AuthController@login');

Route::get('trader/password/reset/{token?}', 'Trader\Auth\PasswordController@showResetForm');
Route::post('trader/password/email', 'Trader\Auth\PasswordController@sendResetLinkEmail');
Route::post('trader/password/reset', 'Trader\Auth\PasswordController@reset');

Route::get('inspector/login', 'Inspector\Auth\AuthController@showLoginForm');
Route::post('inspector/login', 'Inspector\Auth\AuthController@login');
Route::get('inspector/password/reset/{token?}', 'Inspector\Auth\PasswordController@showResetForm');
Route::post('inspector/password/email', 'Inspector\Auth\PasswordController@sendResetLinkEmail');
Route::post('inspector/password/reset', 'Inspector\Auth\PasswordController@reset');

Route::group(['middleware' => ['admin']], function () {
    Route::post('admin/register', 'Admin\Auth\AuthController@register');
    Route::get('admin/register', 'Admin\Auth\AuthController@showRegistrationForm');
    Route::get('admin/logout', 'Admin\Auth\AuthController@logout');
    Route::get('admin/password', 'Admin\Auth\AuthController@changePassword');
    Route::post('admin/password', 'Admin\Auth\AuthController@updatePassword');
    Route::get('admin/password/reset/{token?}', 'Admin\Auth\PasswordController@showResetForm');
    Route::post('admin/password/email', 'Admin\Auth\PasswordController@sendResetLinkEmail');
    Route::post('admin/password/reset', 'Admin\Auth\PasswordController@reset');
    Route::get('admin', 'HomeController@adminIndex');

    //Price Types

    Route::get('admin/vat', ['as' => 'admin.vat', 'uses' => 'Admin\SalesTypeController@vat']);
    Route::post('admin/vat_post', ['as' => 'admin.vat_post', 'uses' => 'Admin\SalesTypeController@vatPost']);

    //Sales type
    Route::post('sales-types/{id}', ['as' => 'sales-types.publish', 'uses' => 'Admin\SalesTypeController@publish']);
    Route::get('sales-types/destroy/{id}', 'Admin\SalesTypeController@destroy');
    Route::get('sales-types/data', ['as' => 'sales-types-data', 'uses' => 'Admin\SalesTypeController@data']);
    Route::resource('sales-types', 'Admin\SalesTypeController');

    Route::get('duplicate-form/{id}', ['as' => 'duplicate-form-load', 'uses' => 'Admin\SalesTypeController@formLoad']);
    Route::post('sales-types-duplicate', ['as' => 'sales.types.duplicate', 'uses' => 'Admin\SalesTypeController@duplicate']);

    Route::get('testCheck', 'Admin\AuctionTestController@testCheck');

    Route::get('import-trader-drm', 'Admin\ImportController@import');
    Route::post('import-trader-drm-post', 'Admin\ImportController@importPost');



    Route::get('profit-management/{id}', ['as' => 'profit.management', 'uses' => 'Admin\ProfitMarginController@manageProfit']);
    Route::any('profit-management/data/{id}', ['as' => 'profit-data', 'uses' => 'Admin\ProfitMarginController@data']);
    Route::post('profit-management-create', ['as' => 'profit-management.create', 'uses' => 'Admin\ProfitMarginController@createProfit']);
    Route::post('profit-management-update/{id?}', ['as' => 'profit-management.update', 'uses' => 'Admin\ProfitMarginController@updateProfit']);
    Route::any('profit-delete/{id?}', ['as' => 'delete.profit', 'uses' => 'Admin\ProfitMarginController@destroy']);

    Route::get('form-load', ['as' => 'form-load', 'uses' => 'Admin\ProfitMarginController@formLoad']);
    Route::get('form-load-edit/{id?}', ['as' => 'form-load-edit', 'uses' => 'Admin\ProfitMarginController@formLoadEdit']);
    //********//

    Route::get('dealers/export', 'Admin\DealersController@export');
    Route::get('dealers/destroy/{id}', 'Admin\DealersController@destroy');

    Route::post('dealers-status/{id}', ['as' => 'dealers.publish', 'uses' => 'Admin\DealersController@publish']);

    Route::get('dealers/data', ['as' => 'dealer-data', 'uses' => 'Admin\DealersController@data']);
    Route::resource('dealers', 'Admin\DealersController');

    Route::get('branch-managers/export', 'Admin\BranchManagerController@export');
    Route::get('branch-managers/destroy/{id}', 'Admin\BranchManagerController@destroy');
    Route::get('branch-managers/data', ['as' => 'branch-manager-data', 'uses' => 'Admin\BranchManagerController@data']);
    Route::resource('branch-managers', 'Admin\BranchManagerController');

    Route::get('users/export', 'Admin\UsersController@export');
    Route::get('users/destroy/{id}', 'Admin\UsersController@destroy');
    Route::get('users/data', ['as' => 'user-data', 'uses' => 'Admin\UsersController@data']);
    Route::resource('users', 'Admin\UsersController');

    Route::get('admin/terms', ['as' => 'pages.terms', 'uses' => 'Admin\PagesController@termsPage']);
    Route::post('admin/terms_post', ['as' => 'pages.termspost', 'uses' => 'Admin\PagesController@setTermsPage']);

    Route::get('admin/faq', ['as' => 'pages.faq', 'uses' => 'Admin\PagesController@faqPage']);
    Route::post('admin/faq_post', ['as' => 'pages.faqPost', 'uses' => 'Admin\PagesController@setFaqPage']);

    Route::get('admin/about', ['as' => 'pages.faq', 'uses' => 'Admin\PagesController@about']);
    Route::post('admin/about_post', ['as' => 'pages.faqPost', 'uses' => 'Admin\PagesController@setAboutPage']);

    Route::get('admin/about', ['as' => 'pages.faq', 'uses' => 'Admin\PagesController@about']);
    Route::post('admin/about_post', ['as' => 'pages.faqPost', 'uses' => 'Admin\PagesController@setAboutPage']);

    Route::get('admin/privacy_policy', ['as' => 'pages.faq', 'uses' => 'Admin\PagesController@privacyPolicy']);
    Route::post('admin/privacy_policy_post', ['as' => 'pages.faqPost', 'uses' => 'Admin\PagesController@setPrivacyPolicyPage']);

    Route::get('admin/contact', ['as' => 'pages.faq', 'uses' => 'Admin\PagesController@contact']);
    Route::post('admin/contact_post', ['as' => 'pages.faqPost', 'uses' => 'Admin\PagesController@setContactPage']);

    Route::post('traders-status/{id}', ['as' => 'traders.publish', 'uses' => 'Admin\TradersController@publish']);
    Route::get('traders/destroy/{id}', 'Admin\TradersController@destroy');

    Route::get('remove-trader-data/{type}/{id}', 'Admin\TradersController@remove_trader_images');

    Route::get('traders/credits/{id}', 'Admin\TradersController@creditHistory');
    Route::get('traders/data', ['as' => 'trader-data', 'uses' => 'Admin\TradersController@data']);
    Route::get('traders/export/{dealerId?}', 'Admin\TradersController@export');

    Route::get('admin/traders-view-deleted', ['as' => 'trader-deleted-list', 'uses' => 'Admin\TradersController@viewDeleted']);
    Route::get('admin/traders-deleted-data', ['as' => 'trader-deleted-data', 'uses' => 'Admin\TradersController@deletedData']);
    Route::get('admin/traders-restore/{id}', ['as' => 'trader-restore-data', 'uses' => 'Admin\TradersController@restoreTrader']);


    Route::resource('traders', 'Admin\TradersController');

    Route::get('customers/data', ['as' => 'customer-data', 'uses' => 'Admin\CustomerController@data']);
    Route::resource('customers', 'Admin\CustomerController');

    Route::get('admin-user/destroy/{id}', 'Admin\AdminUserController@destroy');
    Route::get('admin-user/data', ['as' => 'admin-user-data', 'uses' => 'Admin\AdminUserController@data']);
    Route::resource('admin-user', 'Admin\AdminUserController');






    Route::get('vehicle/export/pdf/{inspectorId?}', 'Admin\InspectorsController@exportPdfVehicle');
    Route::get('vehicle/export/csv/{inspectorId?}', 'Admin\InspectorsController@exportCsvVehicle');
    Route::get('inspectors/vehicle-data/{id?}', 'Admin\InspectorsController@vehicleData');
    Route::get('inspectors/vehicle/{id}', 'Admin\InspectorsController@vehicle');
    Route::get('inspectors/export', 'Admin\InspectorsController@export');
    Route::get('inspectors-restrore/{id}', 'Admin\InspectorsController@restrore');

    Route::get('inspectors/trashed-data', ['as' => 'inspect-trashed-data', 'uses' => 'Admin\InspectorsController@trashedData']);
    Route::get('inspectors/trashed', 'Admin\InspectorsController@trashed')->name('inspectors.trashed');
    Route::get('inspectors/destroy/{id}', 'Admin\InspectorsController@destroy');
    Route::get('inspectors/data', ['as' => 'inspect-data', 'uses' => 'Admin\InspectorsController@data']);
    Route::resource('inspectors', 'Admin\InspectorsController');

    Route::get('inspector-activity/{id}', 'Admin\InspectorsController@activity');
    Route::get('inspector-activity/data/{id}', ['as' => 'inspect-activity-data', 'uses' => 'Admin\InspectorsController@activityData']);

    Route::get('inspector-activity-object/{id}', 'Admin\InspectorsController@objectActivity');
    Route::get('inspector-activity-object/data/{id}', ['as' => 'inspect-activity-object-data', 'uses' => 'Admin\InspectorsController@activityObjectData']);

    Route::get('attribute/destroy/{id}', 'Admin\AttributeController@destroy');
    Route::get('attribute/data', ['as' => 'attribute-data', 'uses' => 'Admin\AttributeController@data']);
    Route::resource('attribute', 'Admin\AttributeController');
    Route::post('attribute-status/{id}', ['as' => 'attribute.publish', 'uses' => 'Admin\AttributeController@publish']);
    Route::post('attribute-invisible_to_trader/{id}', ['as' => 'attribute.invisible_to_trader', 'uses' => 'Admin\AttributeController@invisibleToTrader']);
    Route::post('attribute-exportable/{id}', ['as' => 'attribute.exportable', 'uses' => 'Admin\AttributeController@exportable']);


    Route::get('attributeset/destroy/{id}', 'Admin\AttributeSetController@destroy');
    Route::get('attributeset/data', ['as' => 'attributeset-data', 'uses' => 'Admin\AttributeSetController@data']);
    Route::resource('attributeset', 'Admin\AttributeSetController');

    Route::get('auction/destroy/{id}', 'Admin\AuctionsController@destroy');
    Route::get('auction/data', ['as' => 'auction-data', 'uses' => 'Admin\AuctionsController@data']);
    Route::resource('auction', 'Admin\AuctionsController');

    Route::get('make/data', ['as' => 'make-data', 'uses' => 'Admin\MakeController@data']);
    Route::get('make/destroy/{id}', 'Admin\MakeController@destroy');
    Route::resource('make', 'Admin\MakeController');

    Route::get('model/data', ['as' => 'model-data', 'uses' => 'Admin\ModelController@data']);
    Route::get('model/destroy/{id}', 'Admin\ModelController@destroy');
    Route::resource('model', 'Admin\ModelController');
    Route::get('importModel', 'Admin\ModelController@importModel');
    Route::post('postImportModel', 'Admin\ModelController@importExcel');

    Route::get('auctions/owner_negototiate/{id}', 'Admin\AuctionsController@ownerNegotiate');
    Route::post('auctions/owner_negototiate/post/{id}', 'Admin\AuctionsController@ownerNegotiatePost');

    Route::get('auctions/override_bid_amount/{id}', 'Admin\AuctionsController@override');
    Route::post('auctions/override_bid_amount/post/{id}', 'Admin\AuctionsController@overridePost');


    Route::get('auctions/view/{id}', 'Admin\AuctionsController@auctionDetails');
    Route::get('auctions/viewAjax/{id}', 'Admin\AuctionsController@auctionDetailsAjax');
    Route::get('auctions/automaticBidAjax/{id}', ['as' => 'automaticBidAjax', 'uses' =>  'Admin\AuctionsController@auctionAutomaticAjax']);
    Route::get('auctions/destroy/{id}', 'Admin\AuctionsController@destroy');
    Route::get('auctions/data', ['as' => 'admin-auction-data', 'uses' => 'Admin\AuctionsController@data']);
    Route::get('auctions/create', 'Admin\AuctionsController@create');
    Route::get('auctions/{type?}', 'Admin\AuctionsController@index');
    Route::resource('auctions', 'Admin\AuctionsController');

    Route::get('get-sales-types/{id}', ['as' => 'get-sales-types', 'uses' => 'Admin\AuctionsController@getSalesTypes']);
    Route::post('update-sales-types/{id?}', ['as' => 'update-sales-types', 'uses' => 'Admin\AuctionsController@updateSalesTypes']);

    Route::get('trader-auction/data', ['as' => 'trader-auction-data', 'uses' => 'Admin\TraderAuctionController@data']);
    Route::get('trader-auction', 'Admin\TraderAuctionController@index');
    Route::get('admin-get-traders', ['as' => 'admin-get-traders', 'uses' => 'Admin\TraderAuctionController@getTraders']);


    Route::get('history/export', 'Admin\HistoryController@export');
    Route::get('history/data', ['as' => 'history-data', 'uses' => 'Admin\HistoryController@data']);
    Route::get('traderlist', ['as' => 'admin-trader-list', 'uses' => 'Admin\HistoryController@traderList']);
    Route::get('history', 'Admin\HistoryController@History');
    Route::get('auctions/negotiate/{id}', 'Admin\AuctionsController@negotiateCreate');
    Route::post('auctions/negotiate/{id}', 'Admin\AuctionsController@negotiateStore');

    Route::post('auctions/getDeduct/{id}', ['as' => 'auctions.getDeduct', 'uses' => 'Admin\AuctionsController@getDeduct']);

    Route::get('auctions/inspector-negotiate/{id}', 'Admin\AuctionsController@inspectorNegotiateCreate');
    Route::post('auctions/inspector-negotiate/{id}', 'Admin\AuctionsController@inspectorNegotiateStore');

    Route::get('auctions/reopen/{id}', 'Admin\ObjectsController@reopenAuction');

    Route::get('getDatas', 'Admin\TestExportController@getDatas');
    Route::get('getTraderDatas', 'Admin\TestExportController@getTraderDatas');

    Route::get('getAuctions', 'Admin\TestExportController@getAuctions');
    Route::get('download-report', 'Admin\TestExportController@downloadReport');

    //Route::get('objects/destroy/{id}', 'Dealer\ObjectsController@destroy');
    //Route::get('objects/data', ['as' => 'admin-object-data', 'uses' => 'Dealer\ObjectsController@data']);
    //Route::resource('objects', 'Dealer\ObjectsController');

    Route::get('objects/duplicate/{id}', 'Admin\ObjectsController@duplicateObject');
    Route::get('objects/destroy/{id}', 'Admin\ObjectsController@destroy');
    Route::get('objects/data', ['as' => 'admin-object-data', 'uses' => 'Admin\ObjectsController@data']);
    Route::get('objects/data', ['as' => 'admin-object-data', 'uses' => 'Admin\ObjectsController@data']);
    Route::get('objects/noData', ['as' => 'admin-no-object-data', 'uses' => 'Admin\ObjectsController@noData']);
    Route::get('objects/auction', 'Admin\ObjectsController@index');
    Route::get('objects/auction/export/{dealerId}', 'Admin\ObjectsController@auctionExport');
    Route::get('objects/noauction', 'Admin\ObjectsController@noIndex');
    Route::get('objects/noauction/export/{dealerId}/{sourceId}', 'Admin\ObjectsController@noauctionExport');

    Route::resource('objects', 'Admin\ObjectsController');



    Route::get('object/detail/{id}', 'Admin\ObjectsController@objectDetails');
    Route::get('object/download/{id}', ['as' => 'object-download-print',  'uses' => 'Admin\ObjectsController@download']);

    Route::get('object/downloadSold/{id}', ['as' => 'object-sold-download', 'uses' =>'Admin\ObjectsController@downloadSold'] );



    Route::get('auction/detail/{id}', 'Admin\ObjectsController@objectDetails');
    Route::get('object/edit/{objectId}', 'Admin\ObjectsController@objectEdit');
    Route::post('object/edit/{objectId}', 'Admin\ObjectsController@updateObject');
    Route::get('remove-watch-data/{type}/{id}', 'Admin\ObjectsController@remove_watch_images');
    Route::get('auctions/stop/{id}', 'Admin\AuctionsController@stopAuction');
    Route::get('auctions/cancel/{id}', 'Admin\AuctionsController@cancelAuction');

    Route::get('auctions/cancel-closed/{id}', 'Admin\AuctionsController@cancelClosedAuction');

    Route::get('get/models/{id}', 'Admin\ObjectsController@getModels');

    Route::get('auctions/qualitycheck/{id}', 'Admin\AuctionsController@qualityAuction');

    Route::get('auctions/passcheck/{id}', 'Admin\AuctionsController@passCheck');
    Route::get('auctions/failcheck/{id}', 'Admin\AuctionsController@failCheck');
    Route::get('auctions/cashed/{id}', 'Admin\AuctionsController@cashOut');
    Route::get('auctions/readysale/{id}', 'Admin\AuctionsController@readySale');


    Route::get('auctions/sendreminder/{id}', 'Admin\AuctionsController@sendreminder');

    Route::get('admin/version/', ['as' => 'version.index', 'uses' => 'Admin\VersionController@ViewVersion']);
    Route::post('admin/version/update', ['as' => 'version.index', 'uses' => 'Admin\VersionController@VersionUpdate']);

    Route::get('getNewVehicle', ['as' => 'getNewVehicle', 'uses' => 'Admin\AdminController@getNewVehicle']);
    Route::get('notification/status/{id?}', ['as' => 'notificationStatus', 'uses' => 'Admin\AdminController@notificationStatus']);

    Route::get('getOtherNewVehicle', ['as' => 'getOtherNewVehicle', 'uses' => 'Admin\AdminController@getOtherNewVehicle']);
    Route::get('notification-other/status/{id?}', ['as' => 'notificationOtherStatus', 'uses' => 'Admin\AdminController@notificationOtherStatus']);

    Route::get('bank/destroy/{id}', 'Admin\BanksController@destroy');
    Route::get('bank/data', ['as' => 'bank-data', 'uses' => 'Admin\BanksController@data']);
    Route::resource('bank', 'Admin\BanksController');

    Route::get('drmusers/export', 'Admin\DRMController@export');
    Route::post('drmusers-status/{id}', ['as' => 'drmusers.publish', 'uses' => 'Admin\DRMController@publish']);
    Route::get('drmusers/destroy/{id}', 'Admin\DRMController@destroy');
    Route::get('drmusers/data', ['as' => 'drm-data', 'uses' => 'Admin\DRMController@data']);
    Route::resource('drmusers', 'Admin\DRMController');


    Route::get('onboarder-users/export', 'Admin\OnboarderController@export');
    Route::post('onboarder-users-status/{id}', ['as' => 'onboarder.publish', 'uses' => 'Admin\OnboarderController@publish']);
    Route::get('onboarder-users/destroy/{id}', 'Admin\OnboarderController@destroy');
    Route::get('onboarder-users/data', ['as' => 'onboarder-data', 'uses' => 'Admin\OnboarderController@data']);
    Route::resource('onboarder-users', 'Admin\OnboarderController');

    Route::get('merge-onboarder-traders/data', ['as' => 'merge-onboarder-trader-data', 'uses' => 'Admin\MergeOnboarderTraderController@data']);
    Route::get('merge-onboarder-traders/index', ['as' => 'merge-onboarder-trader-index', 'uses' => 'Admin\MergeOnboarderTraderController@index']);
    Route::post('merge-onboarder-traders/post', ['as' => 'merge-onboarder-trader-post', 'uses' => 'Admin\MergeOnboarderTraderController@post']);

    Route::get('merge-traders/data', ['as' => 'merge-trader-data', 'uses' => 'Admin\MergeDrmTraderController@data']);
    Route::get('merge-traders/index', ['as' => 'merge-trader-index', 'uses' => 'Admin\MergeDrmTraderController@index']);
    Route::post('merge-traders/post', ['as' => 'merge-trader-post', 'uses' => 'Admin\MergeDrmTraderController@post']);

    //Roles
    Route::get('roles/destroy/{id}', 'Admin\RoleController@destroy');
    Route::get('roles/data', ['as' => 'roles.datatable', 'uses' => 'Admin\RoleController@datatable']);
    Route::resource('roles', 'Admin\RoleController');

    Route::get('permissions/data', ['as' => 'permissions.datatable', 'uses' => 'Admin\PermissionController@datatable']);
    Route::resource('permissions', 'Admin\PermissionController');

    Route::get('notifications/data', ['as' => 'notifications-data', 'uses' => 'Admin\NotificationController@data']);
    Route::resource('notifications', 'Admin\NotificationController');

    Route::post('notification-create', ['as' => 'notification.create', 'uses' => 'Admin\NotificationController@create']);

    Route::get('notifications-dismiss', ['as' => 'notifications-dismiss', 'uses' => 'Admin\NotificationController@dismiss']);

    Route::get('notifications-traders-data/{id}', ['as' => 'notifications-traders-history-data', 'uses' => 'Admin\NotificationHistoryController@traderData']);
    Route::get('notification-history/data', ['as' => 'notifications-history-data', 'uses' => 'Admin\NotificationHistoryController@data']);
    Route::resource('notification-history', 'Admin\NotificationHistoryController');

    Route::get('notification-resend/{id}', ['as' => 'notifications-traders-confirm', 'uses' => 'Admin\NotificationHistoryController@reSend']);
    Route::post('notification-resend-post/{id}', ['as' => 'notifications-traders-confirm', 'uses' => 'Admin\NotificationHistoryController@reSendPost']);


    Route::get('admin/traders-group/destroy/{id}', 'Admin\TraderGroupController@destroy');
    Route::get('admin/traders-list/data', ['as' => 'trader-list-data', 'uses' => 'Admin\TraderGroupController@createData']);
    Route::get('admin/traders-list-edit/data/{id}', ['as' => 'trader-list-edit-data', 'uses' => 'Admin\TraderGroupController@editData']);
    Route::get('admin/traders-group/data', ['as' => 'trader-group-data', 'uses' => 'Admin\TraderGroupController@data']);
    Route::resource('admin/traders-group', 'Admin\TraderGroupController');

    Route::get('template-cancel', ['as' => 'notifications-templates-cancel', 'uses' => 'Admin\NotificationTemplateController@cancel']);
    Route::get('template-send/{id}', ['as' => 'notifications-templates-send', 'uses' => 'Admin\NotificationTemplateController@send']);
    Route::post('template-send-post/{id}', ['as' => 'notifications-templates-send-post', 'uses' => 'Admin\NotificationTemplateController@sendPost']);

    Route::get('notification-templates/data', ['as' => 'notifications-templates-data', 'uses' => 'Admin\NotificationTemplateController@data']);
    Route::resource('notification-templates', 'Admin\NotificationTemplateController');
    Route::resource('audit-report', 'Admin\AuditReportController');
    Route::get('auditReport/data', ['as' => 'audit.datatable', 'uses' => 'Admin\AuditReportController@data']);
    Route::get('auditUserReport/data', ['as' => 'audit.user.datatable', 'uses' => 'Admin\AuditReportController@showData']);

});

Route::group(['prefix' => 'dealer', 'middleware' => ['dealer']], function () {
    Route::post('register', 'Dealer\Auth\AuthController@register');
    Route::get('get/models/{id}', 'Admin\ObjectsController@getModels');
    Route::get('register', 'Dealer\Auth\AuthController@showRegistrationForm');
    Route::get('logout', 'Dealer\Auth\AuthController@logout');
    Route::get('password', 'Dealer\Auth\AuthController@changePassword');
    Route::post('password', 'Dealer\Auth\AuthController@updatePassword');
    Route::get('password/reset/{token?}', 'Dealer\Auth\PasswordController@showResetForm');
    Route::post('password/email', 'Dealer\Auth\PasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Dealer\Auth\PasswordController@reset');
    Route::get('get-dealer-profile','Dealer\Auth\AuthController@getProfile');
    Route::post('update-dealer-profile','Dealer\Auth\AuthController@updateProfile');
    Route::get('/', 'HomeController@dealerIndex');
    Route::get('traders/export', 'Dealer\TradersController@export');
    Route::get('traders/destroy/{id}', 'Dealer\TradersController@destroy');
    Route::get('traders/credits/{id}', 'Admin\TradersController@creditHistory');
    Route::get('traders/data', ['as' => 'dealer-trader-data', 'uses' => 'Dealer\TradersController@data']);
    Route::post('traders-status/{id}', ['as' => 'traders.publish', 'uses' => 'Dealer\TradersController@publish']);
    Route::resource('traders', 'Dealer\TradersController');

    Route::get('inspectors/vehicle-data/{id?}', 'Dealer\InspectorsController@vehicleData');
    Route::get('inspectors/vehicle/{id}', 'Dealer\InspectorsController@vehicle');
    Route::get('inspectors/destroy/{id}', 'Dealer\InspectorsController@destroy');
    Route::get('inspectors/data', ['as' => 'dealer-inspect-data', 'uses' => 'Dealer\InspectorsController@data']);
    Route::resource('inspectors', 'Dealer\InspectorsController');

    Route::get('objects/destroy/{id}', 'Dealer\ObjectsController@destroy');
    Route::get('objects/data', ['as' => 'dealer-object-data', 'uses' => 'Dealer\ObjectsController@data']);
    Route::get('objects/noData', ['as' => 'dealer-no-object-data', 'uses' => 'Dealer\ObjectsController@noData']);
    Route::get('objects/auction', 'Dealer\ObjectsController@index');
    Route::get('objects/noauction', 'Dealer\ObjectsController@noIndex');
    Route::resource('objects', 'Dealer\ObjectsController');

    Route::get('dealer/get/models/{id}', 'Dealer\ObjectsController@getModels');

    Route::get('auctions/destroy/{id}', 'Dealer\AuctionsController@destroy');

    Route::get('branch-managers/export', 'Dealer\BranchManagerController@export');
    Route::get('branch-managers/destroy/{id}', 'Dealer\BranchManagerController@destroy');
    Route::get('branch-managers/data', ['as' => 'dealer-branch-manager-data', 'uses' => 'Dealer\BranchManagerController@data']);
    Route::resource('branch-managers', 'Dealer\BranchManagerController');

    Route::get('auctions/view/{id}', 'Dealer\AuctionsController@auctionDetails');
    Route::get('auctions/viewAjax/{id}', 'Dealer\AuctionsController@auctionDetailsAjax');
    Route::get('auctions/data', ['as' => 'dealer-auction-data', 'uses' => 'Dealer\AuctionsController@data']);
    Route::get('auctions/create', 'Dealer\AuctionsController@create');
    Route::get('auctions/{type?}', 'Dealer\AuctionsController@index');
    Route::resource('auctions', 'Dealer\AuctionsController');

    Route::post('dealer-status/{id}', ['as' => 'dealers.accept', 'uses' => 'Dealer\AuctionsController@accept']);


    Route::get('history/data', ['as' => 'deals-history-data', 'uses' => 'Dealer\HistoryController@data']);
    Route::get('history', 'Dealer\HistoryController@History');



    Route::get('auctions/negotiate/{id}', 'Dealer\AuctionsController@negotiateCreate');

    Route::post('auctions/negotiate/{id}', 'Dealer\AuctionsController@negotiateStore');

    Route::get('auctions/inspector-negotiate/{id}', 'Dealer\AuctionsController@inspectorNegotiateCreate');
    Route::post('auctions/inspector-negotiate/{id}', 'Dealer\AuctionsController@inspectorNegotiateStore');

    Route::get('auctions/override_bid_amount/{id}', 'Dealer\AuctionsController@override');
    Route::post('auctions/override_bid_amount/post/{id}', 'Dealer\AuctionsController@overridePost');

    Route::get('auctions/owner_negototiate/{id}', 'Dealer\AuctionsController@ownerNegotiate');
    Route::post('auctions/owner_negototiate/post/{id}', 'Dealer\AuctionsController@ownerNegotiatePost');

    Route::get('auctions/reopen/{id}', 'Dealer\ObjectsController@reopenAuction');

    Route::get('objects/duplicate/{id}', 'Dealer\ObjectsController@duplicateObject');

    Route::get('object/detail/{id}', 'Dealer\ObjectsController@objectDetails');
    Route::get('object/download/{id}', 'Dealer\ObjectsController@download');

    Route::get('auction/detail/{id}', 'Dealer\ObjectsController@objectDetails');
    Route::get('auctions/stop/{id}', 'Dealer\AuctionsController@stopAuction');
    Route::get('auctions/cancel/{id}', 'Dealer\AuctionsController@cancelAuction');
    Route::get('auctions/cancel-closed/{id}', 'Dealer\AuctionsController@cancelClosedAuction');

    Route::get('object/edit/{objectId}', 'Dealer\ObjectsController@objectEdit');
    Route::post('object/edit/{objectId}', 'Dealer\ObjectsController@updateObject');
    Route::get('remove-watch-data/{type}/{id}', 'Dealer\ObjectsController@remove_watch_images');

    Route::get('auctions/qualitycheck/{id}', 'Dealer\AuctionsController@qualityAuction');

    Route::get('auctions/passcheck/{id}', 'Dealer\AuctionsController@passCheck');
    Route::get('auctions/failcheck/{id}', 'Dealer\AuctionsController@failCheck');
    Route::get('auctions/cashed/{id}', 'Dealer\AuctionsController@cashOut');
    Route::get('auctions/readysale/{id}', 'Dealer\AuctionsController@readySale');

    Route::get('auctions/sendreminder/{id}', 'Admin\AuctionsController@sendreminder');
});
Route::get('trader/verify/{token}', 'Trader\Auth\AuthController@verifyUser');

Route::group(['prefix' => '{lang?}', 'middleware' => 'language'], function () {
    Route::get('/', 'Trader\Auth\AuthController@showLoginForm');
    Route::get('login', 'Trader\Auth\AuthController@showLoginForm');
    Route::post('login', 'Trader\Auth\AuthController@login');

    Route::get('/register', 'Trader\Auth\AuthController@showRegistrationForm');
    Route::post('trader/register', 'Trader\Auth\AuthController@register');
    // Route::get('trader/verify/{token}', 'Trader\Auth\AuthController@verifyUser');

    Route::get('webviews/terms', ['as' => 'web.terms', 'uses' => 'CmsController@getTerms']);
    Route::get('webviews/faq', ['as' => 'web.faq', 'uses' => 'CmsController@getFaq']);

    Route::group(['middleware' => ['trader']], function () {
        Route::get('home', 'HomeController@traderIndex');
        // Route::post('trader/register', 'Trader\Auth\AuthController@register');
        // Route::get('trader/register', 'Trader\Auth\AuthController@showRegistrationForm');
        Route::get('logout', 'Trader\Auth\AuthController@logout');
        // Route::get('profile', 'Trader\Auth\AuthController@logout');
        Route::get('auction/detail/{id}', 'Trader\IndexController@auctionDetails');
        Route::get('profile', 'Trader\IndexController@getProfile');
        Route::get('notifications', 'Trader\IndexController@getNotification');
        Route::get('preference', 'Trader\IndexController@getNotificationPreference');
        Route::post('preference', 'Trader\IndexController@setNotificationPreference');
        Route::get('history', 'Trader\IndexController@getHistory');
        Route::get('trader/password', 'Trader\Auth\AuthController@changePassword');
        Route::post('trader/password', 'Trader\Auth\AuthController@updatePassword');

        Route::get('contact', 'Trader\PageController@contact');
        Route::get('about', 'Trader\PageController@about');
        Route::get('faq', 'Trader\PageController@faq');
        Route::get('terms_service', 'Trader\PageController@termsService');
        Route::get('privacy_policy', 'Trader\PageController@privacyPolicy');

        Route::post('upload-trader-image', 'Trader\ProfileController@upload')->name('uploadTraderImage');
        Route::post('delete-trader-image', 'Trader\ProfileController@remove')->name('deleteTraderImage');
    });
});

//Route::group(['middleware' => ['adminordealer']], function () {
//    Route::get('traders/destroy/{id}', 'Admin\TradersController@destroy');
//    Route::get('traders/data', ['as' => 'trader-data', 'uses' => 'Admin\TradersController@data']);
//    Route::resource('traders', 'Admin\TradersController');
//});

Route::group(['middleware' => ['inspector']], function () {
    Route::post('inspector/register', 'Inspector\Auth\AuthController@register');
    Route::get('inspector/register', 'Inspector\Auth\AuthController@showRegistrationForm');
    Route::get('inspector/logout', 'Inspector\Auth\AuthController@logout');
    Route::get('inspector/password', 'Inspector\Auth\AuthController@changePassword');
    Route::post('inspector/password', 'Inspector\Auth\AuthController@updatePassword');
    Route::get('inspector', 'HomeController@inspectorIndex');
});


Route::group(['prefix' => 'api'], function () {
    Route::group(['middleware' => ['web_api']], function () {
        Route::post('inspector/login', 'ApiController@inspectorLogin');
        Route::post('trader/login', 'ApiController@traderLogin');
        Route::post('trader/createTrader', 'Api\V1\Trader\UserController@createTrader');

        Route::post('trader/saveTraderImage', 'Api\V1\Trader\UserController@saveTraderImage');
        Route::post('trader/uploadDocuments', 'Api\V1\Trader\UserController@uploadDocuments');

        Route::post('trader/reset', 'Trader\Auth\PasswordController@sendResetLinkEmailApi');
        Route::post('inspector/reset', 'Inspector\Auth\PasswordController@sendResetLinkEmailApi');
        Route::post('attributeset', 'ApiController@getAttributeSet');


		Route::post('trader/versionCheck', 'ApiController@versionCheck');
          Route::post('inspector/versionCheck', 'ApiController@versionCheck');


		Route::post('trader/obj', 'ApiController@getObj');


    });
    Route::group(['middleware' => ['api_validation']], function () {
        Route::group(['prefix' => 'inspector','middleware' => ['auth:api','throttle:30,1']], function () {
           Route::post('getProfile', 'Api\V1\Inspector\UserController@getProfile');
           Route::post('updatePassword', 'Api\V1\Inspector\UserController@updatePassword');
           Route::post('object/save', 'Api\V1\Inspector\UserController@saveObject');

		   Route::post('object/saveImage', 'Api\V1\Inspector\UserController@saveImage');
             Route::post('object/saveImageNames', 'Api\V1\Inspector\UserController@saveImageNames');
             Route::post('object/saveImageNamesV2', 'Api\V1\Inspector\UserController@saveImageNamesV2');
		   Route::post('attributes', 'Api\V1\Inspector\UserController@getAttributes');

		   Route::post('makes', 'Api\V1\Inspector\UserController@getMakes');
		   Route::post('banks', 'Api\V1\Inspector\UserController@getBanks');

             Route::post('object', 'Api\V1\Inspector\UserController@getObject');
             Route::post('getObjectDetail', 'Api\V1\Inspector\UserController@getObjectDetail');
             Route::post('getNegotiate', 'Api\V1\Inspector\UserController@getNegotiate');


             Route::post('getClosedAuctions', 'Api\V1\Inspector\UserController@getClosedAuctions');

             Route::post('twilioRegister', 'Api\V1\Inspector\UserController@twilioRegister');
             Route::post('updateToken', 'Api\V1\Inspector\UserController@updateToken');

             Route::post('saveCustomerNegotiateAmount', 'Api\V1\Inspector\UserController@saveCustomerNegotiateAmount');

             Route::post('saveNegotiate', 'Api\V1\Inspector\UserController@negotiateSave');

             Route::post('saveTracker', 'Api\V1\Inspector\UserController@saveTracker');
             Route::post('pdfGenerate', 'Api\V1\Inspector\UserController@pdfGenerate');




        });

        Route::post('/trader/getObjectDetail', 'Api\V1\Trader\BidController@getObjectDetail');

        Route::group(['prefix' => 'trader', 'middleware' => ['auth:trader_api']], function () {

            Route::post('logout', 'Api\V1\Trader\UserController@traderLogout');

            Route::post('getProfile', 'Api\V1\Trader\UserController@getProfile');



            Route::post('updateToken', 'Api\V1\Trader\UserController@updateToken');

            Route::post('updatePassword', 'Api\V1\Trader\UserController@updatePassword');

            Route::post('addBid', 'Api\V1\Trader\BidController@addBid');


            Route::post('refreshBid', 'Api\V1\Trader\BidController@refreshBid');


            Route::post('setAutomaticBid', 'Api\V1\Trader\BidController@setAutomaticBidAmount');

           

            Route::post('buyBidNow', 'Api\V1\Trader\BidController@buyNow');

            Route::post('settleNow', 'Api\V1\Trader\BidController@settleNow');

            Route::post('bidTimeUpdate', 'Api\V1\Trader\BidController@bidTimeUpdate');

            Route::post('auctionHistory', 'Api\V1\Trader\BidController@auctionHistory');

            Route::post('auctionHistoryv2', 'Api\V1\Trader\BidController@auctionHistoryV2');

            Route::post('getNotifications', 'Api\V1\Trader\NotificationsController@getNotifications');

            Route::post('getPreferenceOptions', 'Api\V1\Trader\NotificationsController@getPreferenceOptions');

            Route::post('setPreferenceOptions', 'Api\V1\Trader\NotificationsController@setPreferenceOptions');

            Route::post('twilioRegister', 'Api\V1\Trader\NotificationsController@twilioRegister');
        });
    });
});
