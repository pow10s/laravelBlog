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

Route::get('/', function () {
    return view('welcome');
});

Route::auth();
Route::post('/register','AdvancedReg@register');
Route::get('register/confirm/{token}','AdvancedReg@confirm');
Route::get('/home', 'HomeController@index');

//Article routing
Route::get('/articles', 'ArticleController@index');
Route::get('/create', 'ArticleController@create');
Route::get('/edit/{id}', 'ArticleController@edit');
Route::post('/update/{id}', 'ArticleController@update');
Route::get('/delete/{id}', 'ArticleController@delete');
Route::post('/store', 'ArticleController@store');
Route::get('/show/{id}', 'ArticleController@show');
Route::post('/store', 'ArticleController@store');
