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
Route::post('/login','Api\UserController@login')->name('users.login');

Route::namespace('Api')->middleware(['cors','auth:api'])->group(function () {
    Route::get('/users', 'UserController@index')->name('user.index');
    Route::get('/users/{user}','UserController@show')->name('users.show');
    Route::post('/users','UserController@store')->name('users.store');
});



