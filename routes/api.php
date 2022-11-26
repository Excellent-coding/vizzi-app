<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'site'], function () {
    Route::post('head', 'Website\PageController@head');
    Route::post('get', 'Website\PageController@data');

    Route::post('data', 'Website\DomainController@data');
    Route::post('domain', 'Website\DomainController@domain');
    Route::post('session-data', 'Website\DomainController@sessionData');
    Route::post('session', 'Website\DomainController@session');

    Route::post('host', 'Website\DomainController@host');
    Route::post('user', 'Website\DomainController@user');

    Route::post('chat-set', 'Website\ChatController@chatSet');
    Route::post('chat-get', 'Website\ChatController@chatGet');
    Route::post('chat-admin-get', 'Website\ChatController@chatAdminGet');
    Route::post('help-user', 'Website\ChatController@helpUser');

    Route::post('booth', 'Website\PageController@booth');
    Route::post('sponsor', 'Website\PageController@sponsor');
    Route::post('poster', 'Website\PageController@poster');
    Route::post('designers', 'Website\PageController@designers');

    Route::post('email-send', 'Website\PageController@emailSend');

    Route::group(['prefix' => 'title'], function () {
        Route::post('get', 'Website\DomainController@titleGet');
        Route::put('set', 'Website\DomainController@titleSet');
        Route::put('del', 'Website\DomainController@titleDel');
    });

    Route::group(['prefix' => 'exhibit'], function () {
        Route::post('get', 'Website\PageController@hall');
        Route::get('get', 'Website\PageController@hallData');
        Route::post('get-items', 'Website\PageController@getItems');
        Route::post('get-filter', 'Website\PageController@getFilter');
        Route::get('get-items', 'Website\PageController@getItems');
    });
});

Route::group(['prefix' => 'poll'], function () {
});

Route::post('/get/poster', 'Site\PosterController@getPreview');
Route::post('/wizard/poster/models/get', 'Site\PosterController@posterGet');

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', 'Auth\LoginController@logout');

    Route::get('/user', 'Auth\UserController@current');

    Route::patch('settings/profile', 'Settings\ProfileController@update');
    Route::patch('settings/password', 'Settings\PasswordController@update');

    Route::post('settings/avatar', 'Settings\ProfileController@avatar');

    Route::post('site/contacts', 'Site\ChatController@contacts');
});

Route::group(['middleware' => 'guest:api'], function () {
    Route::post('login', 'Auth\LoginController@login');
    Route::post('site-login', 'Auth\LoginController@siteLogin');
    Route::post('register', 'Auth\RegisterController@register');
    Route::post('code-check', 'Auth\RegisterController@codeCheck');
    Route::post('code-register', 'Auth\RegisterController@codeRegister');
    Route::post('site-register', 'Auth\RegisterController@siteRegister');
    Route::post('get', 'Auth\RegisterController@getCode');

    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\ResetPasswordController@callResetPassword');

    Route::post('email/verify/{user}', 'Auth\VerificationController@verify')->name('verification.verify');
    Route::post('email/resend', 'Auth\VerificationController@resend');

    Route::post('oauth/{driver}', 'Auth\OAuthController@redirectToProvider');
    Route::get('oauth/{driver}/callback', 'Auth\OAuthController@handleProviderCallback')->name('oauth.callback');
});

Route::group(['middleware' => 'any'], function() {
    Route::group(['prefix' => 'dashboard'], function () {
        Route::post('info', 'Website\DomainController@getInfo');
    });

    Route::group(['prefix' => 'exhibit-hall'], function () {
        Route::post('get', 'Website\HallController@get');
    });

    Route::group(['prefix' => 'wizard'], function () {
        Route::post('getAll', 'Website\WizardController@getAll');
        Route::post('approve', 'Website\WizardController@approve');
        Route::post('reject', 'Website\WizardController@reject');
        Route::post('activate', 'Website\WizardController@activate');
        Route::post('deactivate', 'Website\WizardController@deactivate');
        Route::post('remove', 'Website\WizardController@remove');

        Route::put('setContact', 'Website\WizardController@setContact');
        Route::post('get', 'Website\WizardController@get');
        Route::post('upload', 'Website\WizardController@upload');
        Route::post('models', 'Website\WizardController@models');

        Route::get('get-models', 'Website\WizardController@getModels');

        Route::post('member', 'Website\WizardController@member');
        Route::post('confirmMember', 'Website\WizardController@confirmMember');
        Route::post('del', 'Website\WizardController@del');
        
        Route::post('setBooth', 'Website\WizardController@setBooth');
        Route::post('setTemp', 'Website\WizardController@setTemp');
        Route::post('del-temp', 'Website\WizardController@delTemp');
        Route::put('note', 'Website\DomainController@note');

        Route::post('getBoothData', 'Website\WizardController@getBoothData');
        Route::post('boothStatus', 'Website\WizardController@boothStatus');
        Route::post('userStatus', 'Website\WizardController@userStatus');
        Route::post('categoryStatus', 'Website\WizardController@categoryStatus');

        Route::post('get-setting', 'Website\WizardController@getSetting');
        Route::put('setText', 'Website\WizardController@setText');
        Route::put('note', 'Website\WizardController@note');
        Route::put('modal-set', 'Website\WizardController@modalSet');

        Route::post('poster-upload', 'Website\WizardController@posterUpload');
        Route::post('exhibit-upload', 'Website\WizardController@exhibitUpload');
        Route::post('getPosterData', 'Website\WizardController@getPosterData');
        Route::get('get-poster-data', 'Website\WizardController@getPosterUser');
        Route::post('posterStatus', 'Website\WizardController@posterStatus');
        Route::post('setBackdrop', 'Website\WizardController@setBackdrop');
        Route::post('editBackdrop', 'Website\WizardController@editBackdrop');
        Route::post('setType', 'Website\WizardController@setType');
        Route::post('delCat', 'Website\WizardController@delCat');
        Route::post('get-poster', 'Website\WizardController@getPoster');
        Route::get('get-poster', 'Website\WizardController@getPosterPart');
        Route::post('setCat', 'Website\WizardController@setCat');
        Route::post('setPosterCat', 'Website\WizardController@setPosterCat');
        Route::post('editCat', 'Website\WizardController@editCat');

        Route::post('hall-set', 'Website\WizardController@hallSet');
        Route::post('hall-item-del', 'Website\WizardController@hallItemDel');
        Route::post('hall-status', 'Website\WizardController@hallStatus');

        Route::post('award', 'Website\WizardController@award');
    });

    Route::group(['prefix' => 'domain'], function () {
        Route::post('domain', 'Website\DomainController@domain');
        Route::post('get', 'Website\DomainController@get');
        Route::post('set', 'Website\DomainController@set');
        Route::post('link', 'Website\DomainController@link');
        Route::post('csv-register', 'Website\DomainController@csvRegister');
        Route::put('status', 'Website\DomainController@status');
    });

    Route::group(['prefix' => 'menu'], function () {
        Route::post('get', 'Website\MenuController@get');
        Route::post('set', 'Website\MenuController@set');
        Route::put('order', 'Website\MenuController@order');
        Route::put('status', 'Website\MenuController@status');
        Route::post('head', 'Website\MenuController@head');
        Route::post('host', 'Website\MenuController@host');
        Route::post('title', 'Website\MenuController@title');
    });

    Route::group(['prefix' => 'page'], function () {
        Route::post('get', 'Website\PageController@get');
        Route::post('set', 'Website\PageController@set');
        Route::post('exhibit', 'Website\PageController@exhibit');
        Route::post('status', 'Website\PageController@status');
        Route::post('upload', 'Website\PageController@upload');
        Route::post('set-iframe', 'Website\PageController@setIframe');
    });
});

Route::group(['middleware' => 'should'], function () {
    Route::group(['prefix' => 'admin'], function () {
        Route::post('get', 'User\AdminController@get');
        Route::post('set', 'User\AdminController@set');
        Route::post('add', 'User\AdminController@add');
        Route::post('status', 'User\AdminController@setStatus');
    });

    Route::group(['prefix' => 'users'], function () {
        Route::get('get', 'User\UserController@get');
        Route::put('resend', 'User\UserController@resend');
        Route::post('set', 'User\UserController@set');
        Route::post('set-status', 'User\UserController@setStatus');
        Route::post('status', 'User\UserController@setStatus');
        Route::post('csv', 'User\UserController@csv');
    });
});

Route::group(['middleware' => 'any', 'prefix' => 'wizard', 'namespace' => 'Site'], function () {
    Route::group(['prefix' => 'booth'], function () {
        Route::post('get', 'BoothController@get');
        Route::post('getHeader', 'BoothController@getHeader');
        Route::post('setHeader', 'BoothController@setHeader');
        Route::post('getItem', 'BoothController@getItem');
        Route::post('set', 'BoothController@set');
        Route::put('head', 'BoothController@head');
        Route::put('update', 'BoothController@update');
        Route::post('order', 'BoothController@order');
        Route::post('content', 'BoothController@content');
        Route::post('status', 'BoothController@status');
        Route::put('setTitle', 'BoothController@setTitle');
        Route::post('del', 'BoothController@del');
        Route::post('publish', 'BoothController@publish');
        Route::post('addAsset', 'BoothController@addAsset');
        Route::post('mediaStatus', 'BoothController@mediaStatus');
        Route::post('delTab', 'BoothController@delTab');
    });

    Route::group(['prefix' => 'sponsor'], function () {
        Route::post('upload', 'SponsorController@upload');
        Route::post('get', 'SponsorController@get');
        Route::post('getHeader', 'SponsorController@getHeader');
        Route::post('setHeader', 'SponsorController@setHeader');
        Route::post('getItem', 'SponsorController@getItem');
        Route::post('set', 'SponsorController@set');
        Route::put('head', 'SponsorController@head');
        Route::put('update', 'SponsorController@update');
        Route::put('order', 'SponsorController@order');
        Route::put('content', 'SponsorController@content');
        Route::put('status', 'SponsorController@status');
    });

    Route::group(['prefix' => 'poster'], function () {
        Route::post('setText', 'PosterController@setText');
        Route::post('setCategory', 'PosterController@setCategory');
        Route::post('getData', 'PosterController@getData');
        Route::post('upload', 'PosterController@upload');
        Route::post('multiple-upload', 'PosterController@multipleUpload');
        Route::post('advancedUpload', 'PosterController@advancedUpload');
        Route::post('get', 'PosterController@get');
        Route::post('getHeader', 'PosterController@getHeader');
        Route::post('setHeader', 'PosterController@setHeader');
        Route::post('getItem', 'PosterController@getItem');
        Route::post('set', 'PosterController@set');
        Route::put('head', 'PosterController@head');
        Route::put('update', 'PosterController@update');
        Route::post('delTab', 'PosterController@delTab');
        Route::post('order', 'PosterController@order');
        Route::post('content', 'PosterController@content');
        Route::post('add-asset', 'PosterController@addAsset');
        Route::post('status', 'PosterController@status');
        Route::post('categoryStatus', 'PosterController@categoryStatus');
        Route::put('menuStatus', 'PosterController@menuStatus');
        Route::post('mediaStatus', 'PosterController@mediaStatus');
        Route::post('set-status', 'PosterController@setStatus');

        Route::group(['prefix' => 'models'], function () {
            Route::post('set', 'PosterController@posterSet');
            Route::post('del', 'PosterController@posterDel');
        });
    });
});

Route::group(['prefix' => 'register', 'middleware' =>  'should',  'namespace' =>  'Website'], function () {
    Route::put('set', 'RegisterController@set');
    Route::post('get', 'RegisterController@get');
});

Route::group(['prefix' => 'agenda', 'middleware' =>  'should',  'namespace' =>  'Website'], function () {
    Route::group(['prefix' => 'track'], function () {
        Route::post('set', 'TrackController@trackset');
        Route::post('get', 'TrackController@trackget');
        Route::post('del', 'TrackController@trackdel');
    });

    Route::group(['prefix' => 'session'], function () {
        Route::post('set', 'TrackController@set');
        Route::post('get', 'TrackController@get');
        Route::post('del', 'TrackController@del');
        Route::post('clone', 'TrackController@clone');
        Route::post('active', 'TrackController@active');
        Route::post('asset', 'TrackController@asset');
        Route::post('upload-session', 'TrackController@uploadSession');
        Route::post('zoom', 'TrackController@zoom');
        Route::put('del-asset', 'TrackController@delAsset');
        Route::get('get-sessions', 'TrackController@getSession');
    });

    Route::group(['prefix' => 'manager'], function () {
        Route::post('set', 'TrackController@managerset');
        Route::post('get', 'TrackController@managerget');
        Route::post('del', 'TrackController@managerdel');
    });
    
    Route::group(['prefix' => 'video-page'], function () {
        Route::post('set', 'VideoController@pageset');
        Route::post('get', 'VideoController@pageget');
        Route::post('del', 'VideoController@pagedel');
    });

    Route::group(['prefix' => 'video'], function () {
        Route::post('get', 'VideoController@get');
        Route::post('set', 'VideoController@set');
        Route::post('del', 'VideoController@del');
    });
});

Route::group(['prefix' => 'poll', 'namespace' => 'Site'], function () {
    Route::get('/available', 'PollController@getAvailable');
    Route::post('/stat', 'PollController@getStats');
    Route::get('/', 'PollController@getAll');
    Route::post('/', 'PollController@create');
    Route::get('{poll}', 'PollController@getOne');
    Route::post('{poll}/answer', 'PollController@addAnswer');
    Route::put('{poll}', 'PollController@update');
    Route::delete('{poll}', 'PollController@delete');
});