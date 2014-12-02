<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', ['uses' => 'HomeController@showWelcome']);

Route::post('/login', ['uses' => 'AuthController@login']);
Route::post('/logout', ['uses' => 'AuthController@logout']);

Route::group(['prefix' => 'api/v1', 'before' => 'auth.basic'], function () {
    Route::resource('alerts', 'AlertsController');
    Route::resource('users', 'UsersController', ['only' => ['index']]);
});