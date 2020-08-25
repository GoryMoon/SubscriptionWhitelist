<?php

namespace App\Http\Controllers;

use App\Jobs\SyncMinecraftName;
use App\Models\Channel;
use App\Models\TwitchUser;
use App\Models\Whitelist;
use App\Utils\TwitchUtils;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Validation\Rule;
use Vinkla\Hashids\Facades\Hashids;

class SubscriberController extends Controller
{

    public function index() {
        return view('home');
    }

    public function subscriber() {
        $user = TwitchUtils::getDbUser();
        $whitelists = $user->whitelist()->with('channel.owner')->get();
        $return = [];
        foreach ($whitelists as $whitelist) {
            $mc = $whitelist->minecraft;
            $return[] = (object)[
                'uid' => Hashids::connection('user')->encode($user->id),
                'valid' => $whitelist->valid,
                'minecraft' => !is_null($mc) ? $mc->username: '',
                'username' => $whitelist->username,
                'display_name' => $whitelist->channel->owner->display_name,
                'name' => $whitelist->channel->owner->name,
                'steam_connected' => $user->steam()->exists() ? "true": "false",
                'steam_linked' => $whitelist->steam()->exists() ? "true": "false"
            ];
        }
        return view('subscriber.index')->with('whitelists', $return);
    }

    private function redirectError($message = ['Something went wrong'], $key = 'default') {
        $errors = new ViewErrorBag;
        return redirect()->route('subscriber')->with('errors', $errors->put($key, new MessageBag($message)));
    }

    /**
     * @param Channel $channel
     * @return bool
     */
    private function checkWhitelisted($channel) {
        return TwitchUtils::getDbUser()->whitelist()->where('channel_id', $channel->id)->count() > 0;
    }

    /**
     * @param TwitchUser $owner
     * @return bool
     */
    private function checkIfOwn($owner) {
        return $owner->id === TwitchUtils::getDbUser()->id;
    }

    public function subscriberRedirect(Request $request) {
        $channel = $request->input('channel');
        if (!is_null($channel)) {
            return redirect()->route('subscriber.add', ['channel' => $channel]);
        }

        return $this->redirectError(['add' => 'Bad input, need to enter a channel name']);
    }

    public function subscriberAdd($channelName) {
        if (!is_null($channelName)) {
            $owner = TwitchUser::whereName($channelName)->first();
            if (!is_null($owner)) {
                $channel = $owner->channel;
                if (!is_null($channel)) {
                    if (!$channel->enabled) {
                        return $this->redirectError(['add' => "This channel's whitelist isn't enabled"]);
                    }
                    if ($this->checkWhitelisted($channel)) {
                        return $this->redirectError(['add' => 'You are already whitelisted to this channel']);
                    }
                    if (!TwitchUtils::checkIfSubbed(null, $channel, $owner) && !$this->checkIfOwn($owner)) {
                        return $this->redirectError(['add' => 'You are not subscribed to this channel, you can not add to its whitelist']);
                    }
                    $db_user = TwitchUtils::getDbUser();
                    return view('subscriber.channel')->with([
                        'id' => $channelName,
                        'display_name' => $owner->display_name,
                        'name' => $owner->name,
                        'steam' => $db_user->steam
                    ]);
                }
            }
        }

        return $this->redirectError(['add' => 'Channel not found or channel don\'t have a whitelist enabled']);
    }

    public function subscriberAddSave(Request $request, $channelName) {
        if (!is_null($channelName)) {
            $owner = TwitchUser::whereName($channelName)->first();
            if (!is_null($owner)) {
                $channel = $owner->channel;
                $validData = $request->validate([
                    'username' => [
                        'required',
                        Rule::unique('whitelists', 'username')->where(function ($query) use($channel) {
                            return $query->where('channel_id', $channel->id);
                        })
                    ]
                ]);
                if (!is_null($channel)) {
                    if (!$channel->enabled) {
                        return $this->redirectError(['add' => "This channel's whitelist isn't enabled"]);
                    }
                    if ($this->checkWhitelisted($channel)) {
                        return $this->redirectError(['add' => 'You are already whitelisted to this channel']);
                    }
                    if (!TwitchUtils::checkIfSubbed(null, $channel, $owner) && !$this->checkIfOwn($owner)) {
                        return $this->redirectError(['add' => 'You are not subscribed to this channel, you can not add to its whitelist']);
                    }
                    $db_user = TwitchUtils::getDbUser();

                    $whitelist = new Whitelist;
                    $whitelist->username = $validData['username'];
                    $whitelist->user()->associate($db_user);
                    $whitelist->channel()->associate($channel);
                    $whitelist->save();
                    SyncMinecraftName::dispatch($whitelist);

                    self::dirty($channel);

                    return redirect()->route('subscriber')->with('success', "You are now whitelisted to " . $owner->display_name);
                }
            }
        }

        return $this->redirectError(['add' => 'Channel not found or channel don\'t have a whitelist enabled']);
    }

    public function subscriberAddSteam(Request $request, $channelName) {
        if (!is_null($channelName)) {
            $owner = TwitchUser::whereName($channelName)->first();
            if (!is_null($owner)) {
                $channel = $owner->channel;
                if (!is_null($channel)) {
                    if (!$channel->enabled) {
                        return $this->redirectError(['add' => "This channel's whitelist isn't enabled"]);
                    }
                    if ($this->checkWhitelisted($channel)) {
                        return $this->redirectError(['add' => 'You are already whitelisted to this channel']);
                    }
                    if (!TwitchUtils::checkIfSubbed(null, $channel, $owner) && !$this->checkIfOwn($owner)) {
                        return $this->redirectError(['add' => 'You are not subscribed to this channel, you can not add to its whitelist']);
                    }
                    $db_user = TwitchUtils::getDbUser();

                    $whitelist = new Whitelist;
                    $whitelist->username = $db_user->steam->name;
                    $whitelist->user()->associate($db_user);
                    $whitelist->steam()->associate($db_user->steam);
                    $whitelist->channel()->associate($channel);
                    $whitelist->save();
                    SyncMinecraftName::dispatch($whitelist);

                    self::dirty($channel);

                    return redirect()->route('subscriber')->with('success', "You are now whitelisted to " . $owner->display_name);
                }
            }
        }

        return $this->redirectError(['add' => 'Channel not found or channel don\'t have a whitelist enabled']);
    }

    public function subscriberSave(Request $request, $channelName) {
        if (!is_null($channelName)) {
            $owner = TwitchUser::whereName($channelName)->first();
            if (!is_null($owner)) {
                $channel = $owner->channel;
                if (!is_null($channel)) {
                    $name = 'username-' . $channelName;

                    $db_user = TwitchUtils::getDbUser();
                    $whitelist = $db_user->whitelist()->where('channel_id', $channel->id)->first();
                    if (is_null($whitelist)) {
                        return redirect()->back();
                    }
                    if ($whitelist->username === $request->input($name)) {
                        return $this->redirectError([$name  => 'Provided name is the same as the already saved one']);
                    }

                    $validData = $request->validate([
                         $name => [
                             'required',
                             Rule::unique('whitelists', 'username')->where(function ($query) use($channel) {
                                 return $query->where('channel_id', $channel->id);
                             })
                         ]
                    ]);
                    $owner = $channel->owner;
                    if ((!$whitelist->valid || !TwitchUtils::checkIfSubbed(null, $channel, $owner)) && !$this->checkIfOwn($owner)) {
                        if ($whitelist->valid) {
                            $whitelist->valid = false;
                            $whitelist->save();
                            self::dirty($channel);
                        }
                        return $this->redirectError(['edit-' . $channelName => 'You can not update your username as you are not subscribed to this channel, it\'s not used anyway']);
                    }

                    $whitelist->username = $validData[$name];
                    $whitelist->save();
                    SyncMinecraftName::dispatch($whitelist);

                    self::dirty($channel);

                    return redirect()->back()->with('success', 'Successfully updated username for ' . $owner->display_name);
                }
            }
        }

        return $this->redirectError(['edit' => 'An error occurred while trying to save username']);
    }

    public function subscriberLinkSteam(Request $request, $channelName) {
        if (!is_null($channelName)) {
            $owner = TwitchUser::whereName($channelName)->first();
            if (!is_null($owner)) {
                $channel = $owner->channel;
                if (!is_null($channel)) {
                    $db_user = TwitchUtils::getDbUser();
                    $whitelist = $db_user->whitelist()->where('channel_id', $channel->id)->first();
                    if (is_null($whitelist)) {
                        return redirect()->back();
                    }
                    if (!$db_user->steam()->exists()) {
                        return $this->redirectError(['add' => 'You have not linked steam to your account']);
                    }
                    if ($whitelist->steam()->exists()) {
                        return $this->redirectError(['add' => 'You have already linked steam to this whitelist']);
                    }
                    $whitelist->steam()->associate($db_user->steam);
                    $whitelist->save();
                    self::dirty($channel);

                    return redirect()->back()->with('success', 'Successfully linked steam to ' . $owner->display_name);
                }
            }
        }

        return $this->redirectError(['edit' => 'An error occurred while trying to add steam']);
    }

    public function subscriberUnLinkSteam(Request $request, $channelName) {
        if (!is_null($channelName)) {
            $owner = TwitchUser::whereName($channelName)->first();
            if (!is_null($owner)) {
                $channel = $owner->channel;
                if (!is_null($channel)) {
                    $db_user = TwitchUtils::getDbUser();
                    $whitelist = $db_user->whitelist()->where('channel_id', $channel->id)->first();
                    if (is_null($whitelist)) {
                        return redirect()->back();
                    }
                    if (!$whitelist->steam()->exists()) {
                        return $this->redirectError(['add' => 'You have have not linked steam to this whitelist']);
                    }
                    $whitelist->steam()->dissociate();
                    $whitelist->save();
                    self::dirty($channel);

                    return redirect()->back()->with('success', 'Successfully unlinked steam to ' . $owner->display_name);
                }
            }
        }

        return $this->redirectError(['edit' => 'An error occurred while trying to add steam']);
    }

    public function subscriberDelete($channelName) {
        if (!is_null($channelName)) {
            $owner = TwitchUser::whereName($channelName)->first();
            if (!is_null($owner)) {
                $channel = $owner->channel;
                if (!is_null($channel)) {
                    $db_user = TwitchUtils::getDbUser();
                    $whitelist = $db_user->whitelist()->where('channel_id', $channel->id)->first();
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

    /**
     * @param Channel $channel
     */
    private static function dirty(Channel $channel) {
        if (!$channel->whitelist_dirty) {
            $channel->whitelist_dirty = true;
            $channel->save();
        }
    }

}
