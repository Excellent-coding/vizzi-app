<?php

use Illuminate\Support\Facades\Route;

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

Auth::routes();

Route::get('/login', function () {
     return redirect('/auth/login');
 });
//Route::get('/password/reset', ['uses' => 'Auth\ForgotPasswordController@getEmail']);
//Route::post('/password/reset', ['uses' => 'Auth\ForgotPasswordController@postEmail'])
//    ->name('password.update');
//Route::get('forgot-password', ['uses' => 'Auth\ForgotPasswordController@getEmail']);
//Route::post('forgot-password', ['uses' => 'Auth\ForgotPasswordController@postEmail']);

Route::get('/auth/login', 'SpaController')->where('path', '/auth/login')
    ->name('login');

Route::get('/join/{token}', 'SpaController')->where('token', '(.*)')
    ->name('join');


Route::get('/presentlaunch/{token}', function () {
    
     return view('presenter')->with('token', '(.*)');
 })
    ->name('presentlaunch');


Route::get('/video-test', function () {
     return view('test-player');
 })
    ->name('video-test');

Route::get('/register/{refcode}', 'Auth\RegisterController@registerEvent');
Route::get('/register-check-email', 'Auth\RegisterController@checkEmail');


Route::get('/register-event', function () {
     return view('register-success');
 })
    ->name('register-success');

Route::get('/register-verified', function () {
     return view('register-verified');
 })
    ->name('register-verified');

Route::get('/email/verify/{user}', 'Auth\VerificationController@verifyAvatar')
    ->name('register-verified');


Route::post('/register-settings', 'Auth\VerificationController@updateSettings')
    ->name('register-settings');    

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/timezones', 'SpaController@timeZones')->name('timezones');


Route::get('/phpinfo', function() {
    return phpinfo();
});

Route::get('/zoomevent', function() {
    die();
});
Route::post('/zoomevent', function() {
		die();
});

Route::get('/check-version', function () {
    return env('BUILD_NUMBER');
});
