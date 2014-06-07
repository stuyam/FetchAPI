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

Route::group(['namespace' => 'Fetch\v1\Controllers', 'prefix' => 'v1'], function()
{
    Route::controller('auth', 'AuthController');

    Route::get('test', function(){
       return Response::make('Chod Whomper');
    });
//    Route::group(['before' => 'fetch_auth'], function()
//    {
//
//    });
});


/////////////// 404 ///////////////
App::missing(function($exception)
{
    return Response::json(['404'=> 'Page Not Found'], 404);
});
