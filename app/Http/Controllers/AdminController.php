<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\RequestStat;
use App\Models\TwitchUser;
use App\Models\Whitelist;
use App\Utils\TwitchUtils;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use DB;
use Response;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('twitch');
        $this->middleware('admin');
    }

    /**
     * @return Builder
     */
    private static function getStatBase(){
        return DB::table('whitelists')->selectRaw('COUNT(id) as num');
    }

    public function index() {
        return view('admin.index');
    }

    public function channels(Request $request) {
        $sort = $request->query('sort');
        $query = Channel::with('owner')
            ->select([
                'channels.*',
                'twitch_users.channel_id',
                'twitch_users.name',
                'twitch_users.display_name',
                'twitch_users.broadcaster_type'
            ])
            ->withCount('whitelist')
            ->join('twitch_users', 'channels.id', '=', 'twitch_users.channel_id');
        $order = 'desc';
        if ($request->query('order') == 'asc') {
            $order = 'asc';
        }
        if (!is_null($sort)) {
            if ($sort == 'id') {
                $query = $query->orderBy('id', $order);
            } else if ($sort == 'name') {
                $query = $query->orderBy('twitch_users.name', $order);
            } else if ($sort == 'dname') {
                $query = $query->orderBy('twitch_users.display_name', $order);
            } else if ($sort == 'type') {
                $query = $query->orderBy('twitch_users.broadcaster_type', $order);
            } else if ($sort == 'enabled') {
                $query = $query->orderBy('enabled', $order);
            } else if ($sort == 'whitelist') {
                $query = $query->orderBy('whitelist_count', $order);
            } else if ($sort == 'requests') {
                $query = $query->orderBy('requests', $order);
            }
        }
        $channels = $query->paginate(15);
        return view('admin.channels', ['channels' => $channels]);
    }

    public function statsChannel(Request $request, Channel $channel) {
        list($formatted) = RequestStat::parseStats($channel->stats);
        return view('admin.channel_stats', array_merge(
            ['channel' => $channel],
            BroadcasterController::getStatsArray($channel),
            ['stats' => json_encode($formatted)],
        ));
    }

    public function viewChannel(Request $request, Channel $channel) {
        $sort = $request->query('sort');
        $query = $channel->whitelist();
        $order = 'desc';
        if ($request->query('order') == 'asc') {
            $order = 'asc';
        }
        if (!is_null($sort)) {
            if ($sort == 'id') {
                $query = $query->orderBy('id', $order);
            } else if ($sort == 'name') {
                $query = $query->orderBy('username', $order);
            } else if ($sort == 'type') {
                $query = $query->orderBy('user_id', $order);
            } else if ($sort == 'valid') {
                $query = $query->orderBy('valid', $order);
            } else if ($sort == 'minecraft') {
                $query = $query->orderBy('minecraft_id', $order);
            } else if ($sort == 'steam') {
                $query = $query->orderBy('steam_id', $order);
            }
        }
        $whitelist = $query->paginate(15);

        return view('admin.channel_view', ['channel' => $channel, 'whitelists' => $whitelist]);
    }

    public function deleteWhitelist(Channel $channel, Whitelist $whitelist) {
        $whitelist->delete();
        $channel->whitelist_dirty = true;
        $channel->save();

        return back();
    }

    public function stats() {
        $channels = Channel::get();
        $data = RequestStat::get();

        $requests = $channels->map(function ($values) {
            return $values->requests;
        })->sum();

        $channels = Channel::where('enabled', true)->count();
        $total = self::getStatBase();
        $subs = self::getStatBase()->whereNotNull('user_id');
        $custom = self::getStatBase()->whereNull('user_id');
        $invalid = self::getStatBase()->where('valid', false);
        $minecraft = self::getStatBase()->whereNotNull('minecraft_id');
        $result = self::getStatBase()->whereNotNull('steam_id')
            ->unionAll($minecraft)
            ->unionAll($invalid)
            ->unionAll($custom)
            ->unionAll($subs)
            ->unionAll($total)
            ->get();

        list($formatted, $day, $twodays) = RequestStat::parseStats($data);

        return view('admin.stats', [
            'stats' => json_encode($formatted),
            'total' => $requests,
            'channels' => $channels,
            'day' => $day,
            'twodays' => $twodays,
            'whitelist' => (object)[
                'total' => $result[5]->num,
                'subscribers' => $result[4]->num,
                'custom' => $result[3]->num,
                'invalid' => $result[2]->num,
                'minecraft' => $result[1]->num,
                'steam' => $result[0]->num
            ]
        ]);
    }

}
