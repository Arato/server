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

Route::post('/password/remind', ['uses' => 'RemindersController@remind']);
Route::post('/password/reset', ['uses' => 'RemindersController@reset']);

Route::group(['prefix' => 'api/v1'], function () {
    Route::resource('alerts', 'AlertsController');
    Route::get('alerts/{id}/notifications', ['uses' => 'NotificationsController@index']);

    Route::resource('users', 'UsersController');
    Route::get('users/{id}/alerts', ['uses' => 'AlertsController@index']);

    Route::resource('notifications', 'NotificationsController', ['only' => 'index']);

});
