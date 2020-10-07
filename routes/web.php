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

Route::get('/', 'MainController@home')->name('home');
Route::get('/login', 'TwitchController@index')->name('login');
Route::get('/login/authorize', 'TwitchController@authorizeTwitch')->name('login.authorize');
Route::get('/token', 'TwitchController@token')->name('token');

Route::get('/privacy-tos', 'MainController@privacy')->name('privacy');
Route::get('/about', 'MainController@about')->name('about');

Route::middleware('twitch')->group(function () {
    Route::get('/dashboard', 'MainController@dashboard')->name('dashboard');
    Route::get('/profile', 'MainController@profile')->name('profile');
    Route::delete('/profile', 'MainController@profileDelete')->name('profile.delete');
    Route::post('/logout', 'TwitchController@logout')->name('logout');

    Route::get('/channel/', 'SubscriberController@subscriberRedirect')->name('subscriber.redirect');
    Route::get('/channel/{channel}', 'SubscriberController@subscriberAdd')->name('subscriber.add');
    Route::post('/channel/{channel}', 'SubscriberController@subscriberAddSave')->name('subscriber.add.save');
    Route::post('/channel/{channel}/steam', 'SubscriberController@subscriberAddSteam')->name('subscriber.add.steam');

    Route::get('/subscriber', 'SubscriberController@subscriber')->name('subscriber');
    Route::put('/subscriber/{channel}', 'SubscriberController@subscriberSave')->name('subscriber.save');
    Route::delete('/subscriber/{channel}', 'SubscriberController@subscriberDelete')->name('subscriber.delete');
    Route::post('/subscriber/{channel}/steam', 'SubscriberController@subscriberLinkSteam')->name('subscriber.steam.link');
    Route::delete('/subscriber/{channel}/steam', 'SubscriberController@subscriberUnLinkSteam')->name('subscriber.steam.unlink');

    Route::get('profile/steam/link', 'SteamController@redirectToSteam')->name('auth.steam.link');
    Route::get('profile/steam/unlink', 'SteamController@unlink')->name('auth.steam.unlink');
    Route::get('profile/steam/handle', 'SteamController@handle')->name('auth.steam.handle');

    Route::prefix('admin')->group(function () {
        Route::get('/', 'AdminController@index')->name('admin');
        Route::get('/stats', 'AdminController@stats')->name('admin.stats');
        Route::get('/channel', 'AdminController@channels')->name('admin.channel');
        Route::get('/channel/stats/{channel}', 'AdminController@statsChannel')->name('admin.channel.stats');
        Route::get('/channel/{channel}', 'AdminController@viewChannel')->name('admin.channel.view');
        Route::delete('/channel/{channel}/{whitelist}', 'AdminController@deleteWhitelist')->name('admin.channel.whitelist.delete');
    });

    Route::middleware('broadcaster')->prefix('broadcaster')->group(function () {
        Route::get('/', 'BroadcasterController@index')->name('broadcaster');
        Route::post('/', 'BroadcasterController@updateSettings')->name('broadcaster.settings');
        Route::post('/contact', 'BroadcasterController@contact')->name('broadcaster.contact');

        Route::get('/links', 'BroadcasterController@links')->name('broadcaster.links');
        Route::get('/list', 'BroadcasterController@userlist')->name('broadcaster.list');
        Route::get('/list/data', 'BroadcasterController@userlistData')->name('broadcaster.data');
        Route::get('/list/stats', 'BroadcasterController@listStats')->name('broadcaster.list_stats');
        Route::post('/list/add', 'BroadcasterController@addUser')->name('broadcaster.list.add');
        Route::post('/list/sync', 'BroadcasterController@sync')->name('broadcaster.sync');
        Route::delete('/list/invalid', 'BroadcasterController@removeInvalid')->name('broadcaster.invalid');
        Route::delete('/list/all', 'BroadcasterController@removeAll')->name('broadcaster.delete');
        Route::delete('/list/{id}', 'BroadcasterController@removeEntry')->name('broadcaster.delete_entry');

        Route::get('/stats', 'BroadcasterController@stats')->name('broadcaster.stats');
    });
});
