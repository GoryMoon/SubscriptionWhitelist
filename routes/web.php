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

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BroadcasterController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\PatreonController;
use App\Http\Controllers\SteamController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\TwitchController;

Route::get('/', [MainController::class, 'home'])->name('home');
Route::get('/login', [TwitchController::class, 'index'])->name('login');
Route::get('/login/authorize', [TwitchController::class, 'authorizeTwitch'])->name('login.authorize');
Route::get('/token', [TwitchController::class, 'token'])->name('token');

Route::get('/privacy-tos', [MainController::class, 'privacy'])->name('privacy');
Route::view('/about', 'about')->name('about');

Route::middleware('twitch')->group(function () {
    Route::get('/dashboard', [MainController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [MainController::class, 'profile'])->name('profile');
    Route::delete('/profile', [MainController::class, 'profileDelete'])->name('profile.delete');
    Route::post('/logout', [TwitchController::class, 'logout'])->name('logout');

    Route::get('/channel/', [SubscriberController::class, 'subscriberRedirect'])->name('subscriber.redirect');
    Route::get('/channel/{channel}', [SubscriberController::class, 'subscriberAdd'])->name('subscriber.add');
    Route::post('/channel/{channel}', [SubscriberController::class, 'subscriberAddSave'])->name('subscriber.add.save');
    Route::post('/channel/{channel}/steam', [SubscriberController::class, 'subscriberAddSteam'])->name('subscriber.add.steam');

    Route::get('/subscriber', [SubscriberController::class, 'subscriber'])->name('subscriber');
    Route::put('/subscriber/{channel}', [SubscriberController::class, 'subscriberSave'])->name('subscriber.save');
    Route::delete('/subscriber/{channel}', [SubscriberController::class, 'subscriberDelete'])->name('subscriber.delete');
    Route::post('/subscriber/{channel}/steam', [SubscriberController::class, 'subscriberLinkSteam'])->name('subscriber.steam.link');
    Route::delete('/subscriber/{channel}/steam', [SubscriberController::class, 'subscriberUnLinkSteam'])->name('subscriber.steam.unlink');

    Route::get('profile/steam/link', [SteamController::class, 'redirectToSteam'])->name('auth.steam.link');
    Route::get('profile/steam/unlink', [SteamController::class, 'unlink'])->name('auth.steam.unlink');
    Route::get('profile/steam/handle', [SteamController::class, 'handle'])->name('auth.steam.handle');

    Route::middleware('broadcaster')->group(function () {
        Route::get('profile/patreon/link', [PatreonController::class, 'redirectToPatreon'])->name('auth.patreon.link');
        Route::get('profile/patreon/unlink', [PatreonController::class, 'unlink'])->name('auth.patreon.unlink');
        Route::get('profile/patreon/handle', [PatreonController::class, 'handle'])->name('auth.patreon.handle');
        Route::get('profile/patreon/campaigns', [PatreonController::class, 'viewCampaigns'])->name('patreon.campaigns.view');
        Route::post('profile/patreon/campaigns', [PatreonController::class, 'setCampaign'])->name('patreon.campaigns.set');
    });

    Route::prefix('admin')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('admin');
        Route::get('/stats', [AdminController::class, 'stats'])->name('admin.stats');
        Route::get('/channel', [AdminController::class, 'channels'])->name('admin.channel');
        Route::get('/channel/stats/{channel}', [AdminController::class, 'statsChannel'])->name('admin.channel.stats');
        Route::get('/channel/{channel}', [AdminController::class, 'viewChannel'])->name('admin.channel.view');
        Route::delete('/channel/{channel}/{whitelist}', [AdminController::class, 'deleteWhitelist'])->name('admin.channel.whitelist.delete');
    });

    Route::middleware('broadcaster')->prefix('broadcaster')->group(function () {
        Route::get('/', [BroadcasterController::class, 'index'])->name('broadcaster');
        Route::post('/', [BroadcasterController::class, 'updateSettings'])->name('broadcaster.settings');
        Route::post('/contact', [BroadcasterController::class, 'contact'])->name('broadcaster.contact');

        Route::get('/links', [BroadcasterController::class, 'links'])->name('broadcaster.links');
        Route::get('/list', [BroadcasterController::class, 'userlist'])->name('broadcaster.list');
        Route::get('/list/data', [BroadcasterController::class, 'userlistData'])->name('broadcaster.data');
        Route::get('/list/stats', [BroadcasterController::class, 'listStats'])->name('broadcaster.list_stats');
        Route::post('/list/add', [BroadcasterController::class, 'addUser'])->name('broadcaster.list.add');
        Route::post('/list/sync', [BroadcasterController::class, 'sync'])->name('broadcaster.sync');
        Route::delete('/list/invalid', [BroadcasterController::class, 'removeInvalid'])->name('broadcaster.invalid');
        Route::delete('/list/all', [BroadcasterController::class, 'removeAll'])->name('broadcaster.delete');
        Route::delete('/list/{id}', [BroadcasterController::class, 'removeEntry'])->name('broadcaster.delete_entry');

        Route::get('/stats', [BroadcasterController::class, 'stats'])->name('broadcaster.stats');
    });
});
