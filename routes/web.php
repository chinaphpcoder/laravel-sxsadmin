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
})->middleware('auth');

Route::get('/appcode', ['as' => 'url_appcode',function () {
    return view('appcode');
}])->middleware('auth');

Route::get('/appdebug', ['as' => 'url_appcode',function () {
    return view('appdebug');
}])->middleware('auth');

Route::any('/campaign', ['as' => 'url_campaign',function () {
    return view('campaign');
}])->middleware('auth');

Route::any('/userinfo', ['as' => 'url_userinfo',function () {
    return view('userinfo');
}])->middleware('auth');
Route::any('/newyear', 'NewyearController@index')->middleware('auth');
Route::post('/server/appdecode', 'ServerController@decode')->name('server_appdecode');
Route::post('/server/appencode', 'ServerController@encode')->name('server_appencode');
Route::post('/server/appdebug', 'ServerController@appdebug')->name('server_appdebug');
Route::post('/server/userinfo', 'UserController@userinfo')->name('server_userinfo');
Route::get('/code', 'CodeController@userCode')->name('verify_code');
Route::get('/code/find', 'CodeController@getUserCode')->name('server_usercode');



Auth::routes();

Route::get('/home', 'HomeController@index');
