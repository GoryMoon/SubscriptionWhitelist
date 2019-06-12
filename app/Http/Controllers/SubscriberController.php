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
            $return[] = (object)[
                'id' => Hashids::encode($whitelist->channel->id),
                'valid' => $whitelist->valid,
                'username' => $whitelist->username,
                'display_name' => $whitelist->channel->owner->display_name,
                'name' => $whitelist->channel->owner->name
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

    /**
     * @param Channel $channel
     * @param string $uid
     * @return bool
     */
    private function checkIfSubbed($channel, $uid) {
        if (is_null($channel->valid_plans)) {
            return TwitchUtils::isUserSubscribed($uid);
        } else {
            $plans = json_decode($channel->valid_plans);
            return TwitchUtils::isUserSubscribed($uid, $plans);
        }
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
                    if ($this->checkIfOwn($owner)) {
                        $url = route('broadcaster.list');
                        return $this->redirectError(['add' => "You can not add yourself to your own whitelist here, go to <a href='$url'>broadcast userlist</a> to do that"]);
                    }
                    if (!$this->checkIfSubbed($channel, $owner->uid)) {
                        return $this->redirectError(['add' => 'You are not subscribed to this channel, you can not add to its whitelist']);
                    }
                    return view('subscriber.channel')->with([
                        'id' => $channelName,
                        'display_name' => $owner->display_name,
                        'name' => $owner->name
                    ]);
                }
            }
        }

        return $this->redirectError(['add' => 'Channel not found or channel don\'t have a whitelist enabled']);
    }

    public function subscriberAddSave(Request $request, $channelName) {
        $validData = $request->validate([
            'username' => 'required|unique:whitelists,username'
        ]);

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
                    if ($this->checkIfOwn($owner)) {
                        $url = route('broadcaster.list');
                        return $this->redirectError(['add' => "You can not add yourself to your own whitelist here, go to <a href='$url'>broadcast userlist</a> to do that"]);
                    }
                    if (!$this->checkIfSubbed($channel, $owner->uid)) {
                        return $this->redirectError(['add' => 'You are not subscribed to this channel, you can not add to its whitelist']);
                    }
                    $db_user = TwitchUtils::getDbUser();

                    $whitelist = new Whitelist;
                    $whitelist->username = $validData['username'];
                    $whitelist->user()->associate($db_user);
                    $whitelist->channel()->associate($channel);
                    $whitelist->save();

                    self::dirty($channel);

                    return redirect()->route('subscriber')->with('success', "You are now whitelisted to " . $owner->display_name);
                }
            }
        }

        return $this->redirectError(['add' => 'Channel not found or channel don\'t have a whitelist enabled']);
    }

    public function subscriberSave(Request $request, $channelHash) {
        if (!is_null($channelHash)) {
            $ids = Hashids::decode($channelHash);
            if (count($ids) > 0) {
                $channel = Channel::whereId($ids[0])->first();
                if (!is_null($channel)) {
                    $name = 'username-' . $channelHash;
                    $validData = $request->validate([
                         $name => 'required|unique:whitelists,username'
                    ]);
                    $owner = $channel->owner;
                    $db_user = TwitchUtils::getDbUser();
                    $whitelist = $db_user->whitelist()->where('channel_id', $channel->id)->first();
                    if (is_null($whitelist)) {
                        return $this->redirectError(['edit' => 'You are not whitelisted to this channel, you need to add before able to update']);
                    }
                    if ($this->checkIfOwn($owner)) {
                        $url = route('broadcaster.list');
                        return $this->redirectError(['add' => "You can not add yourself to your own whitelist here, go to <a href='$url'>broadcast userlist</a> to do that"]);
                    }
                    if (!$whitelist->valid || !$this->checkIfSubbed($channel, $owner->uid)) {
                        if ($whitelist->valid) {
                            $whitelist->valid = false;
                            $whitelist->save();
                            self::dirty($channel);
                        }
                        return $this->redirectError(['edit-' . $channelHash => 'You can not update your username as you are not subscribed to this channel, it\'s not used anyway']);
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

    public function subscriberDelete($channelHash) {
            if (!is_null($channelHash)) {
            $ids = Hashids::decode($channelHash);
            if (count($ids) > 0) {
                $channel = Channel::whereId($ids[0])->first();
                if (!is_null($channel)) {
                    $db_user = TwitchUtils::getDbUser();
                    $whitelist = $db_user->whitelist()->where('channel_id', $channel->id)->first();
                    if (is_null($whitelist)) {
                        return $this->redirectError(['edit' => 'You are not whitelisted to this channel, can\'t remove something that does not exists']);
                    }
                    try {
                        $whitelist->delete();
                    } catch (Exception $e) {
                        return $this->redirectError(['edit-' . $channelHash => 'An error occurred while trying to remove username']);
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
