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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/appcode', ['as' => 'url_appcode',function () {
    return view('appcode');
}]);
Route::get('/appdebug', ['as' => 'url_appcode',function () {
    return view('appdebug');
}]);
Route::any('/campaign', ['as' => 'url_campaign',function () {
    return view('campaign');
}]);
Route::any('/userinfo', ['as' => 'url_userinfo',function () {
    return view('userinfo');
}]);
Route::post('/server/appdecode', 'ServerController@decode')->name('server_appdecode');
Route::post('/server/appencode', 'ServerController@encode')->name('server_appencode');
Route::post('/server/appdebug', 'ServerController@appdebug')->name('server_appdebug');
Route::post('/server/userinfo', 'UserController@userinfo')->name('server_userinfo');



Auth::routes();

Route::get('/home', 'HomeController@index');

Auth::routes();

Route::get('/home', 'HomeController@index');

Auth::routes();

Route::get('/home', 'HomeController@index');
