<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::middleware(['auth:web'])->group(function () {
	// dashboard
	Route::get('/', 'HomeController@index')->name('home');

    // Parking Data
    Route::match(['get', 'post'], 'parking-data', 'ParkingDataController@index')->name('parking-data.index');

    // Parking
    Route::match(['get', 'post'], 'parking',    'ParkingController@index')->name('parking.index');
    Route::post('parking/configuration',        'ParkingController@configuration')->name('parking.configuration');
    Route::resource('parking',                  'ParkingController', ['only' => [
        'show'
    ]]);

    // profile
    Route::get('/profile',                          'ProfileController@index')->name('profile.index');
    Route::post('/profile/change-password/{id}',    'ProfileController@changePassword')->name('profile.change-password');
    Route::post('/profile/change-setting/{id}',     'ProfileController@changeSetting')->name('profile.change-setting');
});

Route::prefix('admin')->namespace('Admin')->name('admin.')->group(function () {
	// Login
	Route::match(['get', 'post'], 'login', 	'Auth\LoginController@login')->name('login');
    Route::post('logout', 'Auth\LoginController@logout')->name('logout');

    Route::group(['middleware' => ['auth:admin']], function () {
        // dashboard
        Route::get('/', 'HomeController@index')->name('home');

        // Parking Location
        Route::match(['get', 'post'], 'parking-location',       'ParkingLocationController@index')->name('parking-location.index');
        Route::match(['get', 'post'], 'parking-location/add',   'ParkingLocationController@store')->name('parking-location.store');

        // Merchant
        Route::match(['get', 'post'], 'merchant',   'MerchantController@index')->name('merchant.index');
        Route::post('merchant/add',                 'MerchantController@store')->name('merchant.store');

        // Customer
        Route::match(['get', 'post'], 'customer',   'CustomerController@index')->name('customer.index');
    });
});
