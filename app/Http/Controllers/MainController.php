<?php

namespace App\Http\Controllers;

use App\Utils\TwitchUtils;
use Exception;

class MainController extends Controller
{
    public function home() {
        return view('home', [
            'isBroadcaster' => TwitchUtils::hasSubscribers()
        ]);
    }

    public function profile() {
        $user = TwitchUtils::getDbUser();
        return view('profile', [
            'display_name' => $user->display_name,
            'name' => $user->name
        ]);
    }

    public function profileDelete() {
        dd(request());
        try {
            $user = TwitchUtils::getDbUser();
            if (!is_null($user)) {
                $channel = $user->channel();
                if (!is_null($channel)) {
                    $channel->delete();
                }
                $user->delete();
            }
        } catch (Exception $e) {}
        TwitchUtils::revokeToken();
        TwitchUtils::logout();

        return redirect()->route('login')->with('success', 'Successfully removed account');
    }
}
