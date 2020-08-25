<?php

namespace App\Http\Controllers;

use App\Jobs\SyncChannel;
use App\Jobs\SyncUser;
use App\Utils\TwitchUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\Support\ViewErrorBag;
use romanzipp\Twitch\Enums\Scope;
use romanzipp\Twitch\Facades\Twitch;

class TwitchController extends Controller
{

    private static $scope = "channel:read:subscriptions+user_subscriptions";

    public function index() {
        if (TwitchUtils::hasUser()) {
            return redirect()->route('home');
        }
        return view('login');
    }

    public function authorizeTwitch(Request $request) {
        $state = tap(Str::random(32), function ($str) use ($request){
            $request->session()->put('state', $str);
        });
        Twitch::setRedirectUri(route('token'));
        $url = Twitch::getOAuthAuthorizeUrl('code', [Scope::CHANNEL_READ_SUBSCRIPTIONS, Scope::V5_USER_SUBSCRIPTIONS], $state);
        return redirect()->away($url);
    }

    public function token(Request $request) {
        $state = $request->session()->get('state');
        if (is_null($state)) {
            return "Invalid session";
        }

        $error = $request->get('error');
        if (!is_null($error)) {
            if ($error == "access_denied") {
                return $this->redirectError(['Authorization canceled']);
            } else {
                return $this->redirectError([$request->get('error_description')]);
            }
        }

        if (self::$scope != str_replace(' ', '+', $request->get('scope'))) {
            return "Changed/Invalid scope";
        }

        Twitch::setRedirectUri(route('token'));
        $result = Twitch::getOAuthToken($request->get('code'));
        if (!$result->success()) {
            return $this->redirectError(['Unknown error', json_decode($result->exception->getResponse()->getBody())->message]);
        }
        $request->session()->remove('state');
        $response = $result->data();
        Session::put('access_token', $response->access_token);

        $user = TwitchUtils::getRemoteUser();
        if (is_null($user)) {
            return $this->redirectError();
        }

        if (!isset($user->id) || !isset($user->login) || !isset($user->display_name) || !isset($user->broadcaster_type)) {
            return $this->redirectError();
        }

        TwitchUtils::setSessionUser($user);

        if (!TwitchUtils::handleDbUserLogin($user)) {
            return $this->redirectError();
        }

        $db_user = TwitchUtils::getDbUser();
        $db_user->refresh_token = $response->refresh_token;
        $db_user->save();
        SyncUser::dispatch($db_user, TwitchUtils::getSessionAccessToken());
        Auth::guard()->login($db_user);

        $channel = $db_user->channel;
        if (!is_null($channel) && isset($channel) && $channel->enabled && $channel->sync) {
            SyncChannel::dispatch($channel);
        }

        $redirect = Session::get('redirect');
        if (!is_null($redirect)) {
            Session::remove('redirect');
            return redirect($redirect);
        } else {
            return redirect()->route('dashboard');
        }
    }

    private function redirectError($message = ['Something went wrong']) {
        Auth::logout();
        TwitchUtils::logout();
        $errors = new ViewErrorBag;
        return redirect()->route('login')->with('errors', $errors->put('default', new MessageBag($message)));
    }

    public function logout() {
        Auth::logout();
        TwitchUtils::logout();
        return redirect()->route('login');
    }

}
