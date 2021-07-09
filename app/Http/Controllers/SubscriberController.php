<?php

namespace App\Http\Controllers;

use App\Jobs\SyncMinecraftName;
use App\Models\Channel;
use App\Models\TwitchUser;
use App\Models\Whitelist;
use App\Utils\TwitchUtils;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Validation\Rule;
use Vinkla\Hashids\Facades\Hashids;

class SubscriberController extends Controller
{
    // Helper functions

    /**
     * @param string[] $message
     * @param string $key
     *
     * @return RedirectResponse
     */
    private function redirectError($message = ['Something went wrong'], $key = 'default'): RedirectResponse
    {
        $errors = new ViewErrorBag();

        return redirect()->route('subscriber')->with('errors', $errors->put($key, new MessageBag($message)));
    }

    /**
     * @param TwitchUser $user
     * @param Channel $channel
     *
     * @return bool
     */
    private function checkWhitelisted(TwitchUser $user, Channel $channel): bool
    {
        return $user->whitelist()->where('channel_id', $channel->id)->count() > 0;
    }

    /**
     * @param TwitchUser $user
     * @param TwitchUser $broadcaster
     *
     * @return RedirectResponse|null
     */
    private function checkSubscriptionStatus(TwitchUser $user, TwitchUser $broadcaster): ?RedirectResponse
    {
        if ( ! $broadcaster->channel->enabled) {
            return $this->redirectError(['add' => "This channel's whitelist isn't enabled"]);
        }
        if ($this->checkWhitelisted($user, $broadcaster->channel)) {
            return $this->redirectError(['add' => 'You are already whitelisted to this channel, you can edit your entry below']);
        }
        if ( ! TwitchUtils::checkIfSubbed($user, $broadcaster)) {
            return $this->redirectError(['add' => 'You are not subscribed to this channel, you can not add to its whitelist']);
        }

        return null;
    }

    /**
     * @param Channel $channel
     */
    private static function dirty(Channel $channel)
    {
        if ( ! $channel->whitelist_dirty) {
            $channel->whitelist_dirty = true;
            $channel->save();
        }
    }

    // Route functions

    public function index()
    {
        return view('home');
    }

    public function subscriber(Request $request)
    {
        $user = $request->user()->load('steam');
        $whitelists = $user->whitelist()->with(['channel.owner', 'minecraft', 'steam'])->get();
        $return = [];
        $steam_connected = isset($user->steam);
        foreach ($whitelists as $whitelist) {
            $mc = $whitelist->minecraft;
            $return[] = (object) [
                'uid' => Hashids::connection('user')->encode($user->id),
                'valid' => $whitelist->valid,
                'minecraft' => isset($mc) ? $mc->username : '',
                'username' => $whitelist->username,
                'display_name' => $whitelist->channel->owner->display_name,
                'name' => $whitelist->channel->owner->name,
                'steam_connected' => $steam_connected ? 'true' : 'false',
                'steam_linked' => isset($whitelist->steam) ? 'true' : 'false',
            ];
        }

        return view('subscriber.index')->with('whitelists', $return);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function subscriberRedirect(Request $request): RedirectResponse
    {
        $channel = $request->input('channel');
        if ( ! is_null($channel)) {
            return redirect()->route('subscriber.add', ['channel' => $channel]);
        }

        return $this->redirectError(['add' => 'Bad input, need to enter a channel name']);
    }

    /**
     * @param Request $request
     * @param string $channel_name
     *
     * @return View|RedirectResponse
     */
    public function subscriberAdd(Request $request, string $channel_name)
    {
        if ( ! is_null($channel_name)) {
            $broadcaster = TwitchUser::whereName($channel_name)->first();
            if ( ! is_null($broadcaster)) {
                if ( ! is_null($broadcaster->channel)) {
                    $user = $request->user();
                    $status = $this->checkSubscriptionStatus($user, $broadcaster);
                    if (isset($status)) {
                        return $status;
                    }

                    return view('subscriber.channel')->with([
                        'id' => $channel_name,
                        'display_name' => $broadcaster->display_name,
                        'name' => $broadcaster->name,
                        'steam' => $user->steam,
                    ]);
                }
            }
        }

        return $this->redirectError(['add' => 'Channel not found or channel don\'t have a whitelist enabled']);
    }

    /**
     * @param Request $request
     * @param string $channel_name
     *
     * @return RedirectResponse
     */
    public function subscriberAddSave(Request $request, string $channel_name): RedirectResponse
    {
        if ( ! is_null($channel_name)) {
            $broadcaster = TwitchUser::whereName($channel_name)->first();
            if ( ! is_null($broadcaster)) {
                $channel = $broadcaster->channel;
                $validData = $request->validate([
                    'username' => [
                        'required',
                        Rule::unique('whitelists', 'username')->where(function ($query) use ($channel) {
                            return $query->where('channel_id', $channel->id);
                        }),
                    ],
                ]);
                if ( ! is_null($channel)) {
                    $user = $request->user();
                    $status = $this->checkSubscriptionStatus($user, $broadcaster);
                    if (isset($status)) {
                        return $status;
                    }

                    $whitelist = new Whitelist();
                    $whitelist->username = $validData['username'];
                    $whitelist->user()->associate($request->user());
                    $whitelist->channel()->associate($channel);
                    $whitelist->save();
                    SyncMinecraftName::dispatch($whitelist);

                    self::dirty($channel);

                    return redirect()->route('subscriber')->with('success', 'You are now whitelisted to ' . $broadcaster->display_name);
                }
            }
        }

        return $this->redirectError(['add' => 'Channel not found or channel don\'t have a whitelist enabled']);
    }

    /**
     * @param Request $request
     * @param string $channel_name
     *
     * @return RedirectResponse
     */
    public function subscriberAddSteam(Request $request, string $channel_name): RedirectResponse
    {
        if ( ! is_null($channel_name)) {
            $broadcaster = TwitchUser::whereName($channel_name)->first();
            if ( ! is_null($broadcaster)) {
                $channel = $broadcaster->channel;
                if ( ! is_null($channel)) {
                    $user = $request->user();
                    $status = $this->checkSubscriptionStatus($user, $broadcaster);
                    if (isset($status)) {
                        return $status;
                    }

                    $whitelist = new Whitelist();
                    $whitelist->username = $user->steam->name;
                    $whitelist->user()->associate($user);
                    $whitelist->steam()->associate($user->steam);
                    $whitelist->channel()->associate($channel);
                    $whitelist->save();
                    SyncMinecraftName::dispatch($whitelist);

                    self::dirty($channel);

                    return redirect()->route('subscriber')->with('success', 'You are now whitelisted to ' . $broadcaster->display_name);
                }
            }
        }

        return $this->redirectError(['add' => 'Channel not found or channel don\'t have a whitelist enabled']);
    }

    /**
     * @param Request $request
     * @param string $channel_name
     *
     * @return RedirectResponse
     */
    public function subscriberSave(Request $request, string $channel_name): RedirectResponse
    {
        if ( ! is_null($channel_name)) {
            $broadcaster = TwitchUser::whereName($channel_name)->first();
            if ( ! is_null($broadcaster)) {
                $channel = $broadcaster->channel;
                if ( ! is_null($channel)) {
                    $name = 'username-' . $channel_name;

                    $user = $request->user();
                    $whitelist = $user->whitelist()->where('channel_id', $channel->id)->first();
                    if (is_null($whitelist)) {
                        return redirect()->back();
                    }
                    if ($whitelist->username === $request->input($name)) {
                        return $this->redirectError([$name => 'Provided name is the same as the already saved one']);
                    }

                    $valid_data = $request->validate([
                        $name => [
                            'required',
                            Rule::unique('whitelists', 'username')->where(function ($query) use ($channel) {
                                return $query->where('channel_id', $channel->id);
                            }),
                        ],
                    ]);
                    $broadcaster = $channel->owner;
                    if (( ! $whitelist->valid || ! TwitchUtils::checkIfSubbed($user, $broadcaster))) {
                        if ($whitelist->valid) {
                            $whitelist->valid = false;
                            $whitelist->save();
                            self::dirty($channel);
                        }

                        return $this->redirectError(['edit-' . $channel_name => 'You can not update your username as you are not subscribed to this channel, it\'s not used anyway']);
                    }

                    $whitelist->username = $valid_data[$name];
                    $whitelist->save();
                    SyncMinecraftName::dispatch($whitelist);

                    self::dirty($channel);

                    return redirect()->back()->with('success', 'Successfully updated username for ' . $broadcaster->display_name);
                }
            }
        }

        return $this->redirectError(['edit' => 'An error occurred while trying to save username']);
    }

    /**
     * @param Request $request
     * @param string $channelName
     *
     * @return RedirectResponse
     */
    public function subscriberLinkSteam(Request $request, string $channelName): RedirectResponse
    {
        if ( ! is_null($channelName)) {
            $broadcaster = TwitchUser::whereName($channelName)->first();
            if ( ! is_null($broadcaster)) {
                $channel = $broadcaster->channel;
                if ( ! is_null($channel)) {
                    $user = $request->user();
                    $whitelist = $user->whitelist()->where('channel_id', $channel->id)->first();
                    if (is_null($whitelist)) {
                        return redirect()->back();
                    }
                    if ( ! $user->steam()->exists()) {
                        return $this->redirectError(['add' => 'You have not linked steam to your account']);
                    }
                    if ($whitelist->steam()->exists()) {
                        return $this->redirectError(['add' => 'You have already linked steam to this whitelist']);
                    }
                    $whitelist->steam()->associate($user->steam);
                    $whitelist->save();
                    self::dirty($channel);

                    return redirect()->back()->with('success', 'Successfully linked steam to ' . $broadcaster->display_name);
                }
            }
        }

        return $this->redirectError(['edit' => 'An error occurred while trying to add steam']);
    }

    /**
     * @param Request $request
     * @param string $channelName
     *
     * @return RedirectResponse
     */
    public function subscriberUnLinkSteam(Request $request, string $channelName): RedirectResponse
    {
        if ( ! is_null($channelName)) {
            $broadcaster = TwitchUser::whereName($channelName)->first();
            if ( ! is_null($broadcaster)) {
                $channel = $broadcaster->channel;
                if ( ! is_null($channel)) {
                    $whitelist = $request->user()->whitelist()->where('channel_id', $channel->id)->first();
                    if (is_null($whitelist)) {
                        return redirect()->back();
                    }
                    if ( ! $whitelist->steam()->exists()) {
                        return $this->redirectError(['add' => 'You have have not linked steam to this whitelist']);
                    }
                    $whitelist->steam()->dissociate();
                    $whitelist->save();
                    self::dirty($channel);

                    return redirect()->back()->with('success', 'Successfully unlinked steam to ' . $broadcaster->display_name);
                }
            }
        }

        return $this->redirectError(['edit' => 'An error occurred while trying to add steam']);
    }

    /**
     * @param Request $request
     * @param string $channelName
     *
     * @return RedirectResponse
     */
    public function subscriberDelete(Request $request, string $channelName): RedirectResponse
    {
        if ( ! is_null($channelName)) {
            $broadcaster = TwitchUser::whereName($channelName)->first();
            if ( ! is_null($broadcaster)) {
                $channel = $broadcaster->channel;
                if ( ! is_null($channel)) {
                    $whitelist = $request->user()->whitelist()->where('channel_id', $channel->id)->first();
                    if (is_null($whitelist)) {
                        return $this->redirectError(['edit' => 'You are not whitelisted to this channel, can\'t remove something that does not exists']);
                    }
                    try {
                        $whitelist->delete();
                    } catch (Exception $e) {
                        return $this->redirectError(['edit-' . $channelName => 'An error occurred while trying to remove username']);
                    }
                    self::dirty($channel);

                    return redirect()->back()->with('success', 'Successfully removed username for ' . $channel->owner->display_name);
                }
            }
        }

        return $this->redirectError(['edit' => 'An error occurred while trying to remove username']);
    }
}
