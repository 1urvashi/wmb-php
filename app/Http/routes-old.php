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

Route::get('/', function () {
    return Redirect::to('en');
});

Route::get('test','Controller@test');

Route::get('admin/login','Admin\Auth\AuthController@showLoginForm');
Route::post('admin/login','Admin\Auth\AuthController@login');
Route::get('cron','CronController@index');

Route::get('dealer/login','Dealer\Auth\AuthController@showLoginForm');
Route::post('dealer/login','Dealer\Auth\AuthController@login');
    
Route::get('trader/password/reset/{token?}','Trader\Auth\PasswordController@showResetForm');
Route::post('trader/password/email','Trader\Auth\PasswordController@sendResetLinkEmail');
Route::post('trader/password/reset','Trader\Auth\PasswordController@reset');
    
Route::get('inspector/login','Inspector\Auth\AuthController@showLoginForm');
Route::post('inspector/login','Inspector\Auth\AuthController@login');
Route::get('inspector/password/reset/{token?}','Inspector\Auth\PasswordController@showResetForm');
Route::post('inspector/password/email','Inspector\Auth\PasswordController@sendResetLinkEmail');
Route::post('inspector/password/reset','Inspector\Auth\PasswordController@reset');
    
Route::group(['middleware' => ['admin']], function () { 
    Route::post('admin/register', 'Admin\Auth\AuthController@register');
    Route::get('admin/register', 'Admin\Auth\AuthController@showRegistrationForm');
    Route::get('admin/logout','Admin\Auth\AuthController@logout');
    Route::get('admin/password', 'Admin\Auth\AuthController@changePassword');
    Route::post('admin/password', 'Admin\Auth\AuthController@updatePassword');
    Route::get('admin/password/reset/{token?}','Inspector\Auth\PasswordController@showResetForm');
    Route::post('admin/password/email','Admin\Auth\PasswordController@sendResetLinkEmail');
    Route::post('admin/password/reset','Admin\Auth\PasswordController@reset');
    Route::get('admin', 'HomeController@adminIndex');
    Route::get('dealers/destroy/{id}', 'Admin\DealersController@destroy');
    Route::get('dealers/data', ['as' => 'dealer-data', 'uses' => 'Admin\DealersController@data']);
    Route::resource('dealers', 'Admin\DealersController');
    Route::get('traders/destroy/{id}', 'Admin\TradersController@destroy');
    Route::get('traders/credits/{id}', 'Admin\TradersController@creditHistory');
    Route::get('traders/data', ['as' => 'trader-data', 'uses' => 'Admin\TradersController@data']);
    Route::resource('traders', 'Admin\TradersController');
    Route::get('inspectors/destroy/{id}', 'Admin\InspectorsController@destroy');
    Route::get('inspectors/data', ['as' => 'inspect-data', 'uses' => 'Admin\InspectorsController@data']);
    Route::resource('inspectors', 'Admin\InspectorsController');
    Route::get('attribute/destroy/{id}', 'Admin\AttributeController@destroy');
    Route::get('attribute/data', ['as' => 'attribute-data', 'uses' => 'Admin\AttributeController@data']);
    Route::resource('attribute','Admin\AttributeController');
    Route::get('attributeset/destroy/{id}', 'Admin\AttributeSetController@destroy');
    Route::get('attributeset/data', ['as' => 'attributeset-data', 'uses' => 'Admin\AttributeSetController@data']);
    Route::resource('attributeset','Admin\AttributeSetController');
    Route::get('auction/destroy/{id}', 'Admin\AuctionsController@destroy');
    Route::get('auction/data', ['as' => 'auction-data', 'uses' => 'Admin\AuctionsController@data']);
    Route::resource('auction', 'Admin\AuctionsController');

    Route::get('auctions/view/{id}','Admin\AuctionsController@auctionDetails');
    Route::get('auctions/destroy/{id}', 'Admin\AuctionsController@destroy');
    Route::get('auctions/data', ['as' => 'admin-auction-data', 'uses' => 'Admin\AuctionsController@data']);
    Route::get('auctions/create','Admin\AuctionsController@create');
    Route::get('auctions/{type?}','Admin\AuctionsController@index');
    Route::resource('auctions', 'Admin\AuctionsController');
    
    Route::get('history/data', ['as' => 'history-data', 'uses' => 'Admin\HistoryController@data']);
    Route::get('history', 'Admin\HistoryController@History');
    Route::get('auctions/negotiate/{id}','Admin\AuctionsController@negotiateCreate');
    Route::post('auctions/negotiate/{id}','Admin\AuctionsController@negotiateStore');
    //Route::get('objects/destroy/{id}', 'Dealer\ObjectsController@destroy');
    //Route::get('objects/data', ['as' => 'admin-object-data', 'uses' => 'Dealer\ObjectsController@data']);
    //Route::resource('objects', 'Dealer\ObjectsController');

    Route::get('objects/destroy/{id}', 'Admin\ObjectsController@destroy');
    Route::get('objects/data', ['as' => 'admin-object-data', 'uses' => 'Admin\ObjectsController@data']);
    Route::get('objects/noData', ['as' => 'admin-no-object-data', 'uses' => 'Admin\ObjectsController@noData']);
    Route::get('objects/auction', 'Admin\ObjectsController@index');
    Route::get('objects/noauction', 'Admin\ObjectsController@noIndex');
    
    Route::get('object/detail/{id}','Admin\ObjectsController@objectDetails');
    Route::get('auction/detail/{id}','Admin\ObjectsController@objectDetails');
	Route::get('auctions/stop/{id}','Admin\AuctionsController@stopAuction');
	Route::get('auctions/cancel/{id}','Admin\AuctionsController@cancelAuction');
	
	Route::get('auctions/qualitycheck/{id}','Admin\AuctionsController@qualityAuction');
	
	Route::get('auctions/passcheck/{id}','Admin\AuctionsController@passCheck');
	Route::get('auctions/failcheck/{id}','Admin\AuctionsController@failCheck');
	Route::get('auctions/cashed/{id}','Admin\AuctionsController@cashOut');
	Route::get('auctions/readysale/{id}','Admin\AuctionsController@readySale');
});

Route::group(['prefix' => 'dealer','middleware' => ['dealer']], function () {
    Route::post('register', 'Dealer\Auth\AuthController@register');
    Route::get('register', 'Dealer\Auth\AuthController@showRegistrationForm');
    Route::get('logout','Dealer\Auth\AuthController@logout');
    Route::get('password', 'Dealer\Auth\AuthController@changePassword');
    Route::post('password', 'Dealer\Auth\AuthController@updatePassword');
    Route::get('password/reset/{token?}','Dealer\Auth\PasswordController@showResetForm');
    Route::post('password/email','Dealer\Auth\PasswordController@sendResetLinkEmail');
    Route::post('password/reset','Dealer\Auth\PasswordController@reset');
    Route::get('/', 'HomeController@dealerIndex');
    Route::get('traders/destroy/{id}', 'Dealer\TradersController@destroy');
    Route::get('traders/credits/{id}', 'Admin\TradersController@creditHistory');
    Route::get('traders/data', ['as' => 'dealer-trader-data', 'uses' => 'Dealer\TradersController@data']);
    Route::resource('traders', 'Dealer\TradersController');
    Route::get('inspectors/destroy/{id}', 'Dealer\InspectorsController@destroy');
    Route::get('inspectors/data', ['as' => 'dealer-inspect-data', 'uses' => 'Dealer\InspectorsController@data']);
    Route::resource('inspectors', 'Dealer\InspectorsController');
    Route::get('objects/destroy/{id}', 'Dealer\ObjectsController@destroy');
    Route::get('objects/data', ['as' => 'dealer-object-data', 'uses' => 'Dealer\ObjectsController@data']);
    Route::get('objects/noData', ['as' => 'dealer-no-object-data', 'uses' => 'Dealer\ObjectsController@noData']);
    Route::get('objects/auction', 'Dealer\ObjectsController@index');
    Route::get('objects/noauction', 'Dealer\ObjectsController@noIndex');
    //Route::resource('objects', 'Dealer\ObjectsController');
    Route::get('auctions/destroy/{id}', 'Dealer\AuctionsController@destroy');
	
    Route::get('auctions/view/{id}','Dealer\AuctionsController@auctionDetails');
    Route::get('auctions/data', ['as' => 'dealer-auction-data', 'uses' => 'Dealer\AuctionsController@data']);
    Route::get('auctions/create','Dealer\AuctionsController@create');
    Route::get('auctions/{type?}','Dealer\AuctionsController@index');
    Route::resource('auctions', 'Dealer\AuctionsController');
    
    Route::get('history/data', ['as' => 'deals-history-data', 'uses' => 'Dealer\HistoryController@data']);
    Route::get('history', 'Dealer\HistoryController@History');
	
	
	
	Route::get('auctions/negotiate/{id}','Dealer\AuctionsController@negotiateCreate');
	
	Route::post('auctions/negotiate/{id}','Dealer\AuctionsController@negotiateStore');
	
	
	
	
    Route::get('object/detail/{id}','Dealer\ObjectsController@objectDetails');
    Route::get('auction/detail/{id}','Dealer\ObjectsController@objectDetails');
	Route::get('auctions/stop/{id}','Dealer\AuctionsController@stopAuction');
	Route::get('auctions/cancel/{id}','Dealer\AuctionsController@cancelAuction');
	
	Route::get('auctions/qualitycheck/{id}','Dealer\AuctionsController@qualityAuction');
	
	Route::get('auctions/passcheck/{id}','Dealer\AuctionsController@passCheck');
	Route::get('auctions/failcheck/{id}','Dealer\AuctionsController@failCheck');
	Route::get('auctions/cashed/{id}','Admin\AuctionsController@cashOut');
	Route::get('auctions/readysale/{id}','Dealer\AuctionsController@readySale');
	
	
	
	
	
	
	
	
});

Route::group(['prefix' => '{lang?}', 'middleware' => 'language'], function () {
    Route::get('/', 'Trader\Auth\AuthController@showLoginForm');
    Route::get('login','Trader\Auth\AuthController@showLoginForm');
    Route::post('login','Trader\Auth\AuthController@login');
    Route::group(['middleware' => ['trader']], function () {
        Route::get('home', 'HomeController@traderIndex');
        Route::post('trader/register', 'Trader\Auth\AuthController@register');
        Route::get('trader/register', 'Trader\Auth\AuthController@showRegistrationForm');
        Route::get('logout','Trader\Auth\AuthController@logout');
        Route::get('profile','Trader\Auth\AuthController@logout');
        Route::get('auction/detail/{id}','Trader\IndexController@auctionDetails');
        Route::get('profile','Trader\IndexController@getProfile');
        Route::get('notifications','Trader\IndexController@getNotification');
        Route::get('preference','Trader\IndexController@getNotificationPreference');
        Route::post('preference','Trader\IndexController@setNotificationPreference');
        Route::get('history','Trader\IndexController@getHistory');
        Route::get('trader/password', 'Trader\Auth\AuthController@changePassword');
        Route::post('trader/password', 'Trader\Auth\AuthController@updatePassword');
	
	Route::get('contact','Trader\PageController@contact');
	Route::get('about','Trader\PageController@about');
	Route::get('faq','Trader\PageController@faq');
	Route::get('terms_service','Trader\PageController@termsService');
	Route::get('privacy_policy','Trader\PageController@privacyPolicy');
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
    Route::get('inspector/logout','Inspector\Auth\AuthController@logout');
    Route::get('inspector/password', 'Inspector\Auth\AuthController@changePassword');
    Route::post('inspector/password', 'Inspector\Auth\AuthController@updatePassword');
    Route::get('inspector', 'HomeController@inspectorIndex');
});


Route::group(['prefix' => 'api'], function () {
    Route::group(['middleware' => ['web_api']], function () {
        Route::post('inspector/login', 'ApiController@inspectorLogin');
        Route::post('trader/login', 'ApiController@traderLogin');
        Route::post('trader/reset', 'Trader\Auth\PasswordController@sendResetLinkEmailApi'); 
        Route::post('inspector/reset', 'Inspector\Auth\PasswordController@sendResetLinkEmailApi');
        Route::post('attributeset', 'ApiController@getAttributeSet');
		
		
    });
    Route::group(['middleware' => ['api_validation']], function () {
        Route::group(['prefix' => 'inspector','middleware' => ['auth:api','throttle:30,1']], function () {
           Route::post('getProfile', 'Api\V1\Inspector\UserController@getProfile');
           Route::post('updatePassword', 'Api\V1\Inspector\UserController@updatePassword');
           Route::post('object/save', 'Api\V1\Inspector\UserController@saveObject');
		   
		   Route::post('object/saveImage', 'Api\V1\Inspector\UserController@saveImage');
		   Route::post('attributes', 'Api\V1\Inspector\UserController@getAttributes');
		   
		   
        });
        Route::group(['prefix' => 'trader','middleware' => ['auth:trader_api']], function () {
           Route::post('getProfile', 'Api\V1\Trader\UserController@getProfile');
		   
		   Route::post('updateToken', 'Api\V1\Trader\UserController@updateToken');
		   
           Route::post('updatePassword', 'Api\V1\Trader\UserController@updatePassword');
		   
		   Route::post('addBid', 'Api\V1\Trader\BidController@addBid');
		   
		   Route::post('setAutomaticBid', 'Api\V1\Trader\BidController@setAutomaticBidAmount');
		   
		   Route::post('getObjectDetail', 'Api\V1\Trader\BidController@getObjectDetail');
		   
		   Route::post('buyBidNow', 'Api\V1\Trader\BidController@buyNow');
		   
		   Route::post('bidTimeUpdate', 'Api\V1\Trader\BidController@bidTimeUpdate');
		   
		   Route::post('auctionHistory', 'Api\V1\Trader\BidController@auctionHistory');
		   
		   Route::post('getNotifications', 'Api\V1\Trader\NotificationsController@getNotifications');
		   
		   Route::post('getPreferenceOptions', 'Api\V1\Trader\NotificationsController@getPreferenceOptions');
		   
		   Route::post('setPreferenceOptions', 'Api\V1\Trader\NotificationsController@setPreferenceOptions');
		   
		   
        });
    });
});