<?php

namespace App\Http\Controllers;

use App\Jobs\SyncChannel;
use App\Jobs\SyncDispatcher;
use App\Jobs\SyncUser;
use App\Utils\TwitchUtils;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\Support\ViewErrorBag;

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
        $url = route('token');
        $id = config('twitch-api.client_id');
        $url = "https://id.twitch.tv/oauth2/authorize?client_id=$id&redirect_uri=$url&response_type=code&scope=" . self::$scope . "&state=$state";
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

        try {
            $client = new Client(['base_uri' => "https://id.twitch.tv/oauth2/token"]);
            $response = $client->post('', [
                'query' => [
                    'client_id' => config('twitch-api.client_id'),
                    'client_secret' => config('twitch-api.client_secret'),
                    'code' => $request->get('code'),
                    'grant_type' => 'authorization_code',
                    'redirect_uri' => route('token')
                ]
            ]);
        } catch (RequestException $exception) {
            return $this->redirectError(['Unknown error', json_decode($exception->getResponse()->getBody())->message]);
        }

        $request->session()->remove('state');
        $response = json_decode($response->getBody());
        $request->session()->put([
            'access_token' => $response->access_token,
            'refresh_token' => $response->refresh_token
        ]);

        $user = TwitchUtils::getRemoteUser();
        if (is_null($user)) {
            return $this->redirectError();
        }

        if (!isset($user->id) || !isset($user->login) || !isset($user->display_name) || !isset($user->broadcaster_type)) {
            return $this->redirectError();
        }

        $session_user = (object)[
            'id' => $user->id,
            'name' => $user->login,
            'display_name' => $user->display_name,
            'broadcaster_type' => $user->broadcaster_type
        ];
        Session::put('session_user', $session_user);

        if (!TwitchUtils::handleDbUserLogin($user)) {
            return $this->redirectError();
        }

        $db_user = TwitchUtils::getDbUser();
        SyncUser::dispatch($db_user);
        $channel = $db_user->channel;
        if (!is_null($channel) && $channel->enabled && $channel->sync) {
            SyncChannel::dispatch($channel);
        }

        $redirect = Session::get('redirect');
        if (!is_null($redirect)) {
            Session::remove('redirect');
            return redirect($redirect);
        } else {
            return redirect()->route('home');
        }
    }

    private function redirectError($message = ['Something went wrong']) {
        TwitchUtils::logout();
        $errors = new ViewErrorBag;
        return redirect()->route('login')->with('errors', $errors->put('default', new MessageBag($message)));
    }

    public function logout() {
        TwitchUtils::logout();
        return redirect()->route('login');
    }

}
