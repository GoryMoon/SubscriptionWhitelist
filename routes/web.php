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

Route::get('/privacy-tos', 'MainController@privacy')->name('privacy');

Route::middleware('twitch')->group(function () {
    Route::get('/', 'MainController@home')->name('home');
    Route::get('/profile', 'MainController@profile')->name('profile');
    Route::delete('/profile', 'MainController@profileDelete')->name('profile.delete');
    Route::post('/logout', 'TwitchController@logout')->name('logout');

    Route::get('/admin/stats', 'AdminController@stats')->name('admin.stats');

    Route::get('/channel/', 'SubscriberController@subscriberRedirect')->name('subscriber.redirect');
    Route::get('/channel/{channel}', 'SubscriberController@subscriberAdd')->name('subscriber.add');
    Route::post('/channel/{channel}', 'SubscriberController@subscriberAddSave')->name('subscriber.add.save');

    Route::get('/subscriber', 'SubscriberController@subscriber')->name('subscriber');
    Route::put('/subscriber/{channel}', 'SubscriberController@subscriberSave')->name('subscriber.save');
    Route::delete('/subscriber/{channel}', 'SubscriberController@subscriberDelete')->name('subscriber.delete');

    Route::middleware('broadcaster')->prefix('broadcaster')->group(function () {
        Route::get('/', 'BroadcasterController@index')->name('broadcaster');
        Route::post('/', 'BroadcasterController@updateSettings')->name('broadcaster.settings');
        Route::post('/contact', 'BroadcasterController@contact')->name('broadcaster.contact');

        Route::get('/links', 'BroadcasterController@links')->name('broadcaster.links');
        Route::get('/list', 'BroadcasterController@userlist')->name('broadcaster.list');
        Route::get('/list/data', 'BroadcasterController@userlistData');
        Route::get('/list/stats', 'BroadcasterController@listStats');
        Route::post('/list/add', 'BroadcasterController@addUser')->name('broadcaster.list.add');
        Route::post('/list/sync', 'BroadcasterController@sync');
        Route::delete('/list/invalid', 'BroadcasterController@removeInvalid');
        Route::delete('/list/all', 'BroadcasterController@removeAll');
        Route::delete('/list/{id}', 'BroadcasterController@removeEntry');

        Route::get('/stats', 'BroadcasterController@stats')->name('broadcaster.stats');
    });
});