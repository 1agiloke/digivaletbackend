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
});

Route::prefix('admin')->namespace('Admin')->name('admin.')->group(function () {
	// Login
	Route::match(['get', 'post'], 'login', 	'Auth\LoginController@login')->name('login');
    Route::post('logout', 'Auth\LoginController@logout')->name('logout');

    Route::group(['middleware' => ['auth:admin']], function () {
        // dashboard
        Route::get('/', 'HomeController@index')->name('home');
    });
});
