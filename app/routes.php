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

Route::get('/', function()
{
	return View::make('hello');
});

Route::post('/auth/set_number', 'AuthController@store');

Route::post('/auth/verify_number', 'AuthController@verify');

Route::post('/auth/create_account', 'AuthController@create');

Route::post('/test', function(){
    Sms::send(['to'=>'+15083142814', 'text'=>'hello world']);
});