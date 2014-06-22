<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	//
});


App::after(function($request, $response)
{
	//
});


Route::filter('fetch_auth', function()
{
    $userid = Request::header('X-AuthID');
    $token = Request::header('X-AuthToken');
    $user = Fetch\v1\Models\User::find($userid);

    if( ! $user )
    {
        $success = FALSE;
    }
    else
    {
        $success = FALSE;
        for ($i = - 2; $i <= 2; $i ++)
        {
            if ($token == sha1($user->token . (intval(time() / 600) + $i) . '%_.^Y|F9YQH5@n1!K* L?L|tJ,6;J{>zqL6Ik8O<v]>((gR+~fFDesOjV_hW[-0g'))
            {
                $success = TRUE;
            }
        }
    }

    if( ! $success && App::environment('production'))
    {
        $api = new \Fetch\v1\Controllers\APIController;
        return $api->setStatusCode(401)->respondWithError('Failure to authenticate with your account');
    }


});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::guest('login');
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});
