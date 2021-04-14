<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

/*Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});*/

Broadcast::channel('channel.{id}', function ($user, $id) {
    $channel = $user->channel;

    return ! is_null($channel) && (int) $channel->id === (int) $id;
});

Broadcast::channel('users.{id}', function ($user, $id) {
    $hash = Hashids::connection('user')->encode($user->id);

    return $hash === $id;
});
