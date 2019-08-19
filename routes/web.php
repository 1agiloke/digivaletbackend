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

    Route::match(['get', 'post'], 'parking-data', 'ParkingDataController@index')->name('parking-data.index');
});
