<?php


namespace App\Utils;


use App\Models\Channel;
use App\Models\TwitchUser;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use romanzipp\Twitch\Twitch;

class TwitchUtils
{

    private static $instance;
    private $helix;
    private $kraken;
    private $authed_user;

    private $db_user;

    private $testing;

    /**
     * @return TwitchUtils
     */
    private static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new TwitchUtils;
            self::$instance->testing = config('whitelist.testing');
        }
        return self::$instance;
    }

    /**
     * @return mixed
     */
    public static function getSessionAccessToken() {
        return Session::get('access_token');
    }

    /**
     * @param null $uid
     * @return mixed
     */
    public static function getDBAccessToken($uid = null) {
        $user = self::getDbUser($uid, false, true);
        if (!is_null($user) && !is_null($user->access_token)) {
            try {
                return decrypt($user->access_token);
            } catch (DecryptException $e) {
                return null;
            }
        }
        return null;
    }

    /**
     * @param null $uid
     * @return mixed
     */
    private static function getRefreshToken($uid = null) {
        $user = self::getDbUser($uid, false, true);
        if (!is_null($user) && !is_null($user->refresh_token)) {
            try {
                return decrypt($user->refresh_token);
            } catch (DecryptException $e) {
                report($e);
                $user->refresh_token = null;
                $user->save();
                self::logout();
                abort(500, 'Error refreshing oauth token, logging out');
            }
        }
        return null;
    }


    /**
     * @return \Illuminate\Config\Repository|mixed
     */
    public static function getClientId() {
        return config('twitch-api.client_id');
    }

    /**
     * @return Twitch
     */
    private function getHelix() {
        if (is_null($this->helix)) {
            $this->helix = new Twitch();
        }
        return $this->helix;
    }

    /**
     * @return Client
     */
    private function getKraken() {
        if (is_null($this->kraken)) {
            $this->kraken = new Client(['base_uri' => 'https://api.twitch.tv/kraken/']);
        }
        return $this->kraken;
    }

    /**
     * @param string $uri Uri on the kraken api to run
     * @return mixed|null
     */
    private function executeKrakenQuery(string $uri) {
        try {
            $request = new Request('GET', $uri, $this->generateKrakenHeaders());
            $response = $this->getKraken()->send($request);
            $result = json_decode($response->getBody());
        } catch (RequestException $exception) {
            Log::error("Kraken exception", [$exception->getMessage()]);
            if ($exception->getResponse()->getStatusCode() == 404) {
                return false;
            }
            return null;
        } catch (GuzzleException $e) {
            report($e);
            Log::error("Kraken guzzle exception");
            return false;
        }
        Log::debug("Kraken response", [$result]);
        return $result;
    }

    /**
     * @param bool $auth
     * @param int $depth
     * @return mixed|null
     */
    private function getInternalRemoteUser($auth, $depth = 0) {
        if (is_null($this->authed_user)) {
            $session_user = Session::get('session_user');
            if (!is_null($session_user)) {
                $this->authed_user = $session_user;
                return $this->authed_user;
            }
            $accessToken = self::getSessionAccessToken();
            if (!$auth || is_null($accessToken)) {
                return null;
            }

            $request = $this->getHelix()->withToken($accessToken)->getAuthedUser();
            if ($request->success()) {
                $this->authed_user = $request->shift();
            } else {
                if (!self::tokenRefresh(self::getRefreshToken()) || $depth >= 2) {
                    return null;
                } else {
                    return $this->getInternalRemoteUser($auth, $depth + 1);
                }
            }
        }
        return $this->authed_user;
    }

    /**
     * @param bool $auth
     * @return mixed|null
     */
    public static function getRemoteUser($auth = true) {
        return self::instance()->getInternalRemoteUser($auth);
    }

    /**
     * @return bool
     */
    public static function hasUser() {
        return !is_null(self::getRemoteUser());
    }

    /**
     * @param $uid
     * @param bool $auth
     * @param bool $force
     * @return TwitchUser|Builder|Model|object|null
     */
    public static function getDbUser($uid = null, $auth = true, $force = false) {
        if (is_null($uid)) {
            $user = self::getRemoteUser($auth);
            if (is_null($user)) {
                return null;
            }
            $uid = $user->id;
        }
        return self::instance()->getInternalDBUser($uid, $force);
    }

    private function getInternalDBUser($uid, $force) {
        if ($force || is_null($this->db_user)) {
            $this->db_user = TwitchUser::whereUid($uid)->first();
            $this->db_user->refresh();
        }
        if (!is_null($this->db_user) && $this->db_user->uid != $uid) {
            return TwitchUser::whereUid($uid)->first();
        }
        return $this->db_user;
    }

    /**
     * @param string $channel_id
     * @param array $channel_ids
     * @return Collection|null
     */
    private function getChannelSubscribers($channel_id, $channel_ids) {
        $users = collect();
        $retried = false;
        do {
            $access_token = self::getDBAccessToken($channel_id);

            if (is_null($access_token)) {
                Log::error("Access token was null");
                return null;
            }
            $twitch = $this->getHelix()->withToken($access_token);
            $result = $twitch->getSubscriptions(['broadcaster_id' => $channel_id, 'user_id' => $channel_ids], isset($result) && !is_null($result->pagination) ? $result->next(): null);

            if ($result->success()) {
                $users = $users->concat($result->data());
            } else if ($result->status === 401 && !is_null($result->exception)){
                if (!$retried) {
                    self::tokenRefresh(self::getRefreshToken($channel_id));
                    unset($result);
                    $retried = true;
                } else {
                    Log::error("To many subcheck retries", [$result->exception->getMessage(), $channel_id, $access_token]);
                    return null;
                }
            } else {
                Log::error("Unknown error: " . $result->error(), [$result->exception->getMessage(), $channel_id, $access_token]);
                return null;
            }
        } while ($retried || (isset($result) && !is_null($result->pagination)));

        return $users->mapWithKeys(function ($item) {
            return [$item->user_id => $item->tier];
        });
    }

    /**
     * @param Channel $channel
     * @param Collection $channel_ids
     * @return Collection|null
     */
    public static function checkSubscriptions($channel, $channel_ids) {
        $plans = is_null($channel->valid_plans) ? ['Prime', '1000', '2000', '3000']: json_decode($channel->valid_plans);

        $response = self::instance()->getChannelSubscribers($channel->owner->uid, $channel_ids->toArray());
        if (!is_null($response)) {
            return $channel_ids->mapWithKeys(function ($item) use ($response, $plans){
                $data = $response->get($item);
                return [$item => in_array(is_null($data) ? '': $data, $plans)];
            });
        }
        Log::info("Sub check response was null", [$channel, $channel_ids]);
        return null;
    }


    /**
     * @param string $user_id
     * @param string $channel_id
     * @param array $valid_plans
     * @param int $depth
     * @return bool
     */
    private function internalIsSubscribed($user_id, $channel_id, $valid_plans, $depth = 0) {
        $response = $this->executeKrakenQuery("users/$user_id/subscriptions/$channel_id");
        if (is_null($response)) {
            if ($depth >= 2 || !self::tokenRefresh(self::getRefreshToken())) {
                return false;
            } else {
                return $this->internalIsSubscribed($user_id, $channel_id, $valid_plans, $depth + 1);
            }
        }
        if ($response == false) {
            return false;
        }
        if (!in_array($response->sub_plan, $valid_plans)) {
            return false;
        }
        return true;
    }

    /**
     * @param string $from_user
     * @param string $user_id
     * @param Channel $channel
     * @param string $uid
     * @return bool
     */
    public static function checkIfSubbed($user_id, $channel, $uid) {
        if (!Session::has('session_user') && !is_null($user_id)) {
            Session::put('session_user', (object)['id' => $user_id]);
        }
        if (is_null($user_id)) {
            $user_id = self::getRemoteUser()->id;
        }
        if (is_null($channel->valid_plans)) {
            return TwitchUtils::isUserSubscribedToChannel($user_id, $uid);
        } else {
            $plans = json_decode($channel->valid_plans);
            return TwitchUtils::isUserSubscribedToChannel($user_id, $uid, $plans);
        }
    }

    /**
     * @param string $from_user
     * @param string $user_id
     * @param string $channel_id
     * @param array $valid_plans
     * @return bool
     */
    public static function isUserSubscribedToChannel($user_id, $channel_id, $valid_plans = ['Prime', '1000', '2000', '3000']) {
        return self::instance()->testing ? true: self::instance()->internalIsSubscribed($user_id, $channel_id, $valid_plans);
    }

    /**
     * @return boolean
     */
    public static function hasSubscribers() {
        return self::isBroadcaster(self::getDbUser()->broadcaster_type);
    }

    /**
     * @param string $type
     * @return bool
     */
    public static function isBroadcaster($type) {
        return self::instance()->testing ? true : $type != "";
    }

    /**
     * @param $user
     * @return bool
     * @throws Exception
     */
    public static function handleDbUserLogin($user) {
        $db_user = self::getDbUser($user->id);
        $broadcaster_type = $user->broadcaster_type;
        $isBroadcaster = self::isBroadcaster($broadcaster_type);

        if (is_null($db_user)) {
            $db_user = new TwitchUser;
            $db_user->uid = $user->id;
            $db_user->name = $user->login;
            $db_user->display_name = $user->display_name;
            $db_user->broadcaster_type = $broadcaster_type;

            if ($isBroadcaster) {
                $db_channel = tap(new Channel)->save();
                $db_user->channel()->associate($db_channel);
            }
            $db_user->save();
        } else {
            if ($broadcaster_type != $db_user->broadcaster_type) {
                $db_user->broadcaster_type = $broadcaster_type;
                $db_user->save();
            }
            $db_channel = $db_user->channel;
            if ($isBroadcaster) {
                if (is_null($db_channel)) {
                    $db_channel = tap(new Channel)->save();
                    $db_user->channel()->associate($db_channel);
                    $db_user->save();
                }

                if ($db_user->display_name != $user->display_name) {
                    $db_user->display_name = $user->display_name;
                    $db_user->save();
                }
            } else {
                if (!is_null($db_channel)) {
                    $db_channel->delete();
                }
                if (!is_null($db_user->access_token)) {
                    $db_user->access_token = null;
                    $db_user->refresh_token = null;
                    $db_user->save();
                }
            }
        }
        return true;
    }

    public static function revokeToken() {
        $client = new Client(['base_uri' => "https://id.twitch.tv/oauth2/"]);
        try {
            $client->postAsync('revoke', [
                'query' => [
                    'client_id' => self::getClientId(),
                    'token' => self::getSessionAccessToken()
                ]
            ]);
        } catch (RequestException $exception) {}
    }

    public static function logout() {
        Session::forget([
            'session_user',
            'access_token'
        ]);
    }

    /**
     * @param $refresh_token
     * @return bool
     */
    public static function tokenRefresh($refresh_token) {
        if (is_null($refresh_token)) {
            Log::debug("Refresh token is null, aborting refresh");
            return false;
        }

        $client = new Client(['base_uri' => "https://id.twitch.tv/oauth2/token"]);
        try {
            $response = $client->post('', [
                'query' => [
                    'client_id' => self::getClientId(),
                    'client_secret' => config('twitch-api.client_secret'),
                    'refresh_token' => $refresh_token,
                    'grant_type' => 'refresh_token'
                ]
            ]);
        } catch (RequestException $exception) {
            report($exception);
            Log::error("Token refresh errored", [$exception->getMessage()]);
            return false;
        }

        $response = json_decode($response->getBody());
        Log::debug("Token refreshed", [$response]);

        Session::put('access_token', $response->access_token);

        $user = self::getDbUser();
        if (!is_null($user)) {
            $channel = $user->channel;
            if (!is_null($channel)) {
                if ($channel->sync) {
                    $user->access_token = $response->access_token;
                }
                $user->refresh_token = $response->refresh_token;
                $user->save();
            }
        }
        return true;
    }

    /**
     * @param $user
     */
    public static function setSessionUser($user) {
        $session_user = (object)[
            'id' => $user->id,
            'name' => $user->login,
            'display_name' => $user->display_name,
            'broadcaster_type' => $user->broadcaster_type
        ];
        Session::put('session_user', $session_user);
    }

    /**
     * @return array
     */
    public function generateKrakenHeaders(): array {
        return [
            'Accept' => 'application/vnd.twitchtv.v5+json',
            'Client-ID' => self::getClientId(),
            'Authorization' => 'OAuth ' . self::getSessionAccessToken()
        ];
    }
}
