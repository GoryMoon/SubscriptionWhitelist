<?php


namespace App\Utils;


use App\Models\Channel;
use App\Models\TwitchUser;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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

    private static $testing;

    /**
     * @return TwitchUtils
     */
    private static function instance() {
        if (is_null(self::$instance)) {
            self::$testing = config('whitelist.testing');
            self::$instance = new TwitchUtils;
        }
        return self::$instance;
    }

    /**
     * @return mixed
     */
    public static function getAccessToken() {
        return Session::get('access_token');
    }

    /**
     * @return mixed
     */
    private static function getRefreshToken() {
        $user = self::getDbUser(null, false);
        if (!is_null($user)) {
            return $user->refresh_token;
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
            $accessToken = self::getAccessToken();
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
     * @return TwitchUser|Builder|Model|object|null
     */
    public static function getDbUser($uid = null, $auth = true) {
        if (is_null($uid)) {
            $user = self::getRemoteUser($auth);
            if (is_null($user)) {
                return null;
            }
            $uid = $user->id;
        }
        return self::instance()->getInternalDBUser($uid);
    }

    private function getInternalDBUser($uid) {
        if (is_null($this->db_user)) {
            $this->db_user = TwitchUser::whereUid($uid)->first();
        }
        return $this->db_user;
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
            Log::info("$user_id not subbed to $channel_id", [$response]);
            return false;
        }
        if (!in_array($response->sub_plan, $valid_plans)) {
            Log::info("$user_id don't have valid sub_plan", [$response, $valid_plans]);
            return false;
        }
        Log::info("$user_id is subscribed to $channel_id", [$response]);
        return true;
    }

    /**
     * @param string $user_id
     * @param Channel $channel
     * @param string $uid
     * @return bool
     */
    public static function checkIfSubbed($user_id, $channel, $uid) {
        if (!Session::has('session_user')) {
            Session::put('session_user', (object)['id' => $uid]);
        }
        if (is_null($channel->valid_plans)) {
            return TwitchUtils::isUserSubscribedToChannel($user_id, $uid);
        } else {
            $plans = json_decode($channel->valid_plans);
            return TwitchUtils::isUserSubscribedToChannel($user_id, $uid, $plans);
        }
    }

    /**
     * @param string $user_id
     * @param string $channel_id
     * @param array $valid_plans
     * @return bool
     */
    public static function isUserSubscribedToChannel($user_id, $channel_id, $valid_plans = ['Prime', '1000', '2000', '3000']) {
        return self::$testing ? true: self::instance()->internalIsSubscribed($user_id, $channel_id, $valid_plans);
    }

    /**
     * @param string $channel_id
     * @param array $valid_plans
     * @return bool
     */
    public static function isUserSubscribed($channel_id, $valid_plans = ['Prime', '1000', '2000', '3000']) {
        return self::isUserSubscribedToChannel(self::getRemoteUser()->id, $channel_id, $valid_plans);
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
        return self::$testing ? true : $type != "";
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
                    'token' => self::getAccessToken()
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
            if (!is_null($channel) && $channel->sync) {
                $user->access_token = $response->access_token;
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
            'Authorization' => 'OAuth ' . self::getAccessToken()
        ];
    }
}