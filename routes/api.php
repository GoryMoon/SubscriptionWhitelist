<?php

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

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
Route::group(['prefix' => '{id}'], function () {
    Route::get('csv', [ApiController::class, 'csv']);
    Route::get('nl', [ApiController::class, 'nl']);
    Route::get('json_array', [ApiController::class, 'json_array']);

    Route::get('minecraft_csv', [ApiController::class, 'minecraft_csv']);
    Route::get('minecraft_nl', [ApiController::class, 'minecraft_nl']);
    Route::get('minecraft_twitch_nl', [ApiController::class, 'minecraft_twitch_nl']);
    Route::get('minecraft_json_array', [ApiController::class, 'minecraft_json_array']);
    Route::get('minecraft_uuid_csv', [ApiController::class, 'minecraft_uuid_csv']);
    Route::get('minecraft_uuid_nl', [ApiController::class, 'minecraft_uuid_nl']);
    Route::get('minecraft_uuid_json_array', [ApiController::class, 'minecraft_uuid_json_array']);
    Route::get('minecraft_whitelist', [ApiController::class, 'minecraft_whitelist']);

    Route::get('steam_csv', [ApiController::class, 'steam_csv']);
    Route::get('steam_nl', [ApiController::class, 'steam_nl']);
    Route::get('steam_json_array', [ApiController::class, 'steam_json_array']);

    Route::get('patreon_csv', [ApiController::class, 'patreon_csv']);
    Route::get('patreon_nl', [ApiController::class, 'patreon_nl']);
    Route::get('patreon_json_array', [ApiController::class, 'patreon_json_array']);

    Route::fallback(function () {
        return response(['message' => 'Invalid endpoint'], 404);
    });
});

Route::get('/', function () {
    return response(['message' => 'Not Found'], 404);
});

Route::fallback(function () {
    return response(['message' => 'Not Found'], 404);
});

