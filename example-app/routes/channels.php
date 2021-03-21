<?php

use Illuminate\Support\Facades\Broadcast;

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

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/**
 * IMPORTANT!
 * Despite that in the Form Data, Laravel sends: channel_name=private-dominik-channel
 * You can't use 'private-' prefix here, just use the same name of channel here as in the following
 *  window.Echo.private(`dominik-channel`) <--- here
 */
Broadcast::channel('dominik-channel', function(){
    return true;
}, ['guards' => ['dummy-guard']] );
