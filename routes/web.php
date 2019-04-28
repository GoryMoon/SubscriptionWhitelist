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

Route::get('/login', 'TwitchController@index')->name('login');
Route::get('/login/authorize', 'TwitchController@authorizeTwitch')->name('login.authorize');

Route::get('/token', 'TwitchController@token')->name('token');

Route::middleware('twitch')->group(function () {
    Route::get('/', 'MainController@home')->name('home');
    Route::get('/profile', 'MainController@profile')->name('profile');
    Route::delete('/profile', 'MainController@profileDelete')->name('profile.delete');
    Route::post('/logout', 'TwitchController@logout')->name('logout');

    Route::get('/channel/', 'SubscriberController@subscriberRedirect')->name('subscriber.redirect');
    Route::get('/channel/{channel}', 'SubscriberController@subscriberAdd')->name('subscriber.add');
    Route::post('/channel/{channel}', 'SubscriberController@subscriberAddSave')->name('subscriber.add.save');

    Route::get('/subscriber', 'SubscriberController@subscriber')->name('subscriber');
    Route::put('/subscriber/{channel}', 'SubscriberController@subscriberSave')->name('subscriber.save');
    Route::delete('/subscriber/{channel}', 'SubscriberController@subscriberDelete')->name('subscriber.delete');

    Route::middleware('broadcaster')->group(function () {
        Route::get('/broadcaster', 'BroadcasterController@index')->name('broadcaster');
        Route::post('/broadcaster', 'BroadcasterController@updateSettings')->name('broadcaster.settings');
        Route::post('/broadcaster/contact', 'BroadcasterController@contact')->name('broadcaster.contact');

        Route::get('/broadcaster/list', 'BroadcasterController@userlist')->name('broadcaster.list');
        Route::get('/broadcaster/list/data', 'BroadcasterController@userlistData');
        Route::get('/broadcaster/list/stats', 'BroadcasterController@stats');
        Route::post('/broadcaster/list/add', 'BroadcasterController@addUser')->name('broadcaster.list.add');
        Route::post('/broadcaster/list/sync', 'BroadcasterController@sync');
        Route::delete('/broadcaster/list/invalid', 'BroadcasterController@removeInvalid');
        Route::delete('/broadcaster/list/all', 'BroadcasterController@removeAll');
        Route::delete('/broadcaster/list/{id}', 'BroadcasterController@removeEntry');
    });
});