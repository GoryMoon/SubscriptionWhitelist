<?php

namespace App\Utils;

use App\Models\Channel;
use App\Models\TwitchUser;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Contracts\User;
use romanzipp\Twitch\Twitch;

class TwitchUtils
{
    private static TwitchUtils $instance;
    private Twitch $helix;

    protected function __construct()
    {
        $this->helix = new Twitch();
    }

    protected function __clone()
    {
    }

    /**
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception('Cannot unserialize a singleton.');
    }

    /**
     * @return TwitchUtils
     */
    private static function instance(): TwitchUtils
    {
        if ( ! isset(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * @return string
     */
    public static function getClientId(): string
    {
        return config('twitch-api.client_id');
    }

    /**
     * @param TwitchUser $broadcaster
     * @param array $channel_ids
     *
     * @return Collection|null
     */
    private function getChannelSubscribers(TwitchUser $broadcaster, array $channel_ids): ?Collection
    {
        $users = collect();
        $retried = false;
        $channel_id = $broadcaster->uid;
        do {
            $access_token = $broadcaster->access_token;

            if (is_null($access_token)) {
                Log::error('Access token was null');

                return null;
            }
            $twitch = $this->helix->withToken($access_token);
            $result = $twitch->getSubscriptions([
                'broadcaster_id' => $channel_id,
                'user_id' => $channel_ids,
            ],
                isset($result) && ! is_null($result->getPagination()) ? $result->next() : null
            );

            if ($result->success()) {
                $users = $users->concat($result->data());
            } elseif (401 === $result->getStatus() && ! is_null($result->getException())) {
                if ( ! $retried) {
                    self::tokenRefresh($broadcaster);
                    unset($result);
                    $retried = true;
                } else {
                    Log::error('To many subcheck retries', [$result->getException()->getMessage(), $broadcaster]);

                    return null;
                }
            } else {
                Log::error('Unknown error: ' . $result->getErrorMessage(), [$result->getException()->getMessage(), $broadcaster]);

                return null;
            }
        } while ($retried || (isset($result) && ! is_null($result->getPagination())));

        return $users->mapWithKeys(function ($item) {
            return [$item->user_id => $item->tier];
        });
    }

    /**
     * @param Channel $channel
     * @param Collection $channel_ids
     *
     * @return Collection|null
     */
    public static function checkSubscriptions(Channel $channel, Collection $channel_ids): ?Collection
    {
        $plans = is_null($channel->valid_plans) ? ['Prime', '1000', '2000', '3000'] : json_decode($channel->valid_plans);

        $response = self::instance()->getChannelSubscribers($channel->owner, $channel_ids->toArray());
        if ( ! is_null($response)) {
            return $channel_ids->mapWithKeys(function ($item) use ($response, $plans) {
                $data = $response->get($item);

                return [$item => in_array(is_null($data) ? '' : $data, $plans)];
            });
        }
        Log::info('Sub check response was null', [$channel, $channel_ids]);

        return null;
    }

    /**
     * @param TwitchUser $user
     * @param TwitchUser $broadcaster
     * @param array $valid_plans
     *
     * @return bool
     */
    private function internalIsSubscribed(TwitchUser $user, TwitchUser $broadcaster, array $valid_plans): bool
    {
        $channel_id = $broadcaster->uid;

        $retried = false;
        $response = null;

        do {
            $access_token = $user->access_token;
            if (is_null($access_token)) {
                Log::error('Sub check: Access token was null', [$user, $broadcaster]);

                return false;
            }
            $twitch = $this->helix->withToken($access_token);
            $result = $twitch->getUserSubscription([
                'broadcaster_id' => $channel_id,
                'user_id' => $user->uid,
            ]);

            if ($result->success()) {
                $response = $result->data();
            } elseif (401 === $result->getStatus() && ! is_null($result->getException())) {
                if ( ! $retried) {
                    self::tokenRefresh($broadcaster);
                    unset($result);
                    $retried = true;
                } else {
                    Log::error('To many subcheck retries', [$result->getException()->getMessage(), $broadcaster]);

                    return false;
                }
            } elseif (404 === $result->getStatus()) {
                Log::info('User ' . $user->display_name . ' is not subscribed to ' . $broadcaster->display_name);

                return false;
            } else {
                Log::error('Unknown error: ' . $result->getErrorMessage(), [$result->getException()->getMessage(), $broadcaster]);

                return false;
            }
        } while ($retried);

        if (is_null($response)) {
            return false;
        }
        $response = $response[0];
        if ( ! in_array($response->tier, $valid_plans)) {
            return false;
        }

        return true;
    }

    /**
     * @param TwitchUser $user
     * @param TwitchUser $broadcaster
     *
     * @return bool
     */
    public static function checkIfSubbed(TwitchUser $user, TwitchUser $broadcaster): bool
    {
        $plans = ['Prime', '1000', '2000', '3000'];
        if ( ! is_null($broadcaster->channel->valid_plans)) {
            $plans = json_decode($broadcaster->channel->valid_plans);
        }
        // Check if own list then check remote with twitch api if not own list
        return $broadcaster->id === $user->id || self::instance()->internalIsSubscribed($user, $broadcaster, $plans);
    }

    /**
     * @param User $sessionUser
     *
     * @return TwitchUser|null
     */
    public static function handleDbUserLogin(User $sessionUser): ?TwitchUser
    {
        if ( ! ($sessionUser instanceof \Laravel\Socialite\Two\User)) {
            return null;
        }

        $user = TwitchUser::updateOrCreate([
            'uid' => $sessionUser->getId(),
        ], [
            'name' => $sessionUser->user['login'],
            'uid' => $sessionUser->getId(),
            'display_name' => $sessionUser->getName(),
            'broadcaster_type' => $sessionUser->user['broadcaster_type'],
            'access_token' => $sessionUser->token,
            'refresh_token' => $sessionUser->refreshToken,
        ]);

        $channel = $user->channel;
        if ($user->broadcaster) {
            if (is_null($channel)) {
                $channel = tap(new Channel())->save();
                $user->channel()->associate($channel);
                $user->save();
            }
        } else {
            if ( ! is_null($channel)) {
                try {
                    $channel->delete();
                } catch (Exception $e) {
                    report($e);
                    Log::error('Channel deletion error', [$channel, $e->getMessage()]);
                }
            }
        }

        Auth::login($user);

        return $user;
    }

    public static function revokeToken(string $access_token)
    {
        $client = new Client(['base_uri' => 'https://id.twitch.tv/oauth2/']);
        try {
            $client->postAsync('revoke', [
                'query' => [
                    'client_id' => self::getClientId(),
                    'token' => $access_token,
                ],
            ]);
        } catch (RequestException $exception) {
        }
    }

    /**
     * @param TwitchUser|null $owner
     *
     * @return bool
     */
    public static function tokenRefresh(TwitchUser $owner): bool
    {
        if (is_null($owner->refresh_token)) {
            Log::debug('Refresh token is null, aborting refresh');

            return false;
        }

        $client = new Client(['base_uri' => 'https://id.twitch.tv/oauth2/token']);
        try {
            $response = $client->post('', [
                'query' => [
                    'client_id' => self::getClientId(),
                    'client_secret' => config('twitch-api.client_secret'),
                    'refresh_token' => $owner->refresh_token,
                    'grant_type' => 'refresh_token',
                ],
            ]);
        } catch (GuzzleException $exception) {
            report($exception);
            Log::error('Token refresh errored', [$owner, $exception->getMessage()]);

            return false;
        }

        $response = json_decode($response->getBody());
        Log::debug('Token refreshed', [$owner, $response]);

        $owner->access_token = $response->access_token;
        $owner->refresh_token = $response->refresh_token;
        $owner->save();

        return true;
    }
}
