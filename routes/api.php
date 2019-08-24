<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::middleware('jwt.auth')->get('users', function(Request $request) {
    return auth()->user();
});
Route::group([
    'middleware' => 'api',
    'namespace'     => 'Api',
], function () {
    /**
     * Authentication
     */
    Route::prefix('auth')->group(function () {
        // Route::get('phonenumberverify/{phone}', 'AuthController@phoneNumberVerify');
        Route::post('login',                    'AuthController@login');
        Route::post('register',                    'AuthController@register');
        // Route::post('forgotpassword',           'AuthController@forgotPassword');
        // Route::post('resetpassword',            'AuthController@resetPassword');
        // Route::post('changepassword',           'AuthController@changePassword');
        Route::post('refresh',                  'AuthController@refresh');
        Route::get('me',                        'AuthController@me');
        Route::post('logout',                   'AuthController@logout');
    });

    Route::prefix('parking')->group(function(){
        Route::get('list', 'ParkingController@list');
        Route::post('enter', 'ParkingController@enter');
        Route::post('exit', 'ParkingController@exit');
        Route::post('confirm_enter', 'ParkingController@confirm_enter');
    });

    // Route::get('customer', 'CustomerController@index')->name('customer');
    // Route::get('category/all', 'CategoryController@all');
    // Route::get('brand/all', 'BrandController@all');
    // Route::get('good', 'GoodController@index')->name('brand');
});
