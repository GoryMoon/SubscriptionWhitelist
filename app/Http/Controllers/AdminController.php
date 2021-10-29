<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\RequestStat;
use App\Models\Whitelist;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
    private static function getStatBase(): Builder
    {
        return DB::table('whitelists')->selectRaw('COUNT(id) as num');
    }

    /**
     * @return View
     */
    public function index(): View
    {
        return view('admin.index');
    }

    /**
     * @param Request $request
     *
     * @return View
     */
    public function channels(Request $request): View
    {
        $sort = $request->query('sort');
        $query = Channel::with('owner')
            ->select([
                'channels.*',
                'twitch_users.channel_id',
                'twitch_users.name',
                'twitch_users.display_name',
                'twitch_users.broadcaster_type',
            ])
            ->withCount('whitelist')
            ->join('twitch_users', 'channels.id', '=', 'twitch_users.channel_id');
        $order = 'desc';
        if ('asc' == $request->query('order')) {
            $order = 'asc';
        }
        if ( ! is_null($sort)) {
            if ('id' == $sort) {
                $query = $query->orderBy('id', $order);
            } elseif ('name' == $sort) {
                $query = $query->orderBy('twitch_users.name', $order);
            } elseif ('dname' == $sort) {
                $query = $query->orderBy('twitch_users.display_name', $order);
            } elseif ('type' == $sort) {
                $query = $query->orderBy('twitch_users.broadcaster_type', $order);
            } elseif ('enabled' == $sort) {
                $query = $query->orderBy('enabled', $order);
            } elseif ('whitelist' == $sort) {
                $query = $query->orderBy('whitelist_count', $order);
            } elseif ('requests' == $sort) {
                $query = $query->orderBy('requests', $order);
            }
        }
        $channels = $query->paginate(15);

        return view('admin.channels', ['channels' => $channels]);
    }

    /**
     * @param Channel $channel
     *
     * @return View
     */
    public function statsChannel(Channel $channel): View
    {
        return view('admin.channel_stats', array_merge(
            ['channel' => $channel],
            BroadcasterController::getStatsArray($channel),
        ));
    }

    /**
     * @param Request $request
     * @param Channel $channel
     *
     * @return View
     */
    public function viewChannel(Request $request, Channel $channel): View
    {
        $sort = $request->query('sort');
        $query = $channel->whitelist()->with('user:id', 'minecraft:id', 'steam:id');
        $order = 'desc';
        if ('asc' == $request->query('order')) {
            $order = 'asc';
        }
        if ( ! is_null($sort)) {
            if ('id' == $sort) {
                $query = $query->orderBy('id', $order);
            } elseif ('name' == $sort) {
                $query = $query->orderBy('username', $order);
            } elseif ('type' == $sort) {
                $query = $query->orderBy('user_id', $order);
            } elseif ('valid' == $sort) {
                $query = $query->orderBy('valid', $order);
            } elseif ('minecraft' == $sort) {
                $query = $query->orderBy('minecraft_id', $order);
            } elseif ('steam' == $sort) {
                $query = $query->orderBy('steam_id', $order);
            }
        }
        $whitelist = $query->paginate(15);

        return view('admin.channel_view', ['channel' => $channel, 'whitelists' => $whitelist]);
    }

    /**
     * @param Channel $channel
     * @param Whitelist $whitelist
     *
     * @throws Exception
     *
     * @return RedirectResponse
     */
    public function deleteWhitelist(Channel $channel, Whitelist $whitelist): RedirectResponse
    {
        $whitelist->delete();
        $channel->whitelist_dirty = true;
        $channel->save();

        return back();
    }

    /**
     * @return View
     */
    public function stats(): View
    {
        $channels = Channel::get();

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

        $timeBase = Carbon::now()->minute(0)->second(0);
        $countStats = RequestStat::selectRaw('COUNT(id) as num')->where('created_at', '>=', $timeBase->subDays()->toDateTimeString())
            ->unionAll(RequestStat::selectRaw('COUNT(id) as num')->where('created_at', '>=', $timeBase->subDays(2)->toDateTimeString()))
            ->get();

        $timestamp = Carbon::now()->subDays(2)->minute(0)->second(0)->toDateTimeString();
        $stats = RequestStat::where('created_at', '>=', $timestamp)
            ->selectRaw('DATE_FORMAT(created_at, \'%Y-%m-%d %H:00:00\') as hour, count(*) as number')
            ->groupBy('hour')
            ->orderBy('hour')->get();

        return view('admin.stats', [
            'stats' => json_encode($stats),
            'total' => $requests,
            'channels' => $channels,
            'day' => $countStats[0]->num,
            'twodays' => $countStats[1]->num,
            'whitelist' => (object) [
                'total' => $result[5]->num,
                'subscribers' => $result[4]->num,
                'custom' => $result[3]->num,
                'invalid' => $result[2]->num,
                'minecraft' => $result[1]->num,
                'steam' => $result[0]->num,
            ],
        ]);
    }
}
