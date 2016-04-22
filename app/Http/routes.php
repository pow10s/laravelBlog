<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/



Route::group(array('before' => 'auth|nohttps'), function()
{
    Route::get('/', function () {
        return view('welcome');
    });
    Route::auth();
    Route::get('/home', 'HomeController@index');

});

Route::filter('nohttps', function() {
    if (Request::secure()) {return Redirect::to('http://' . Request::url());}
});