<?php

namespace App\Http\Controllers;

use App\Jobs\SyncAllMinecraftNames;
use App\Jobs\SyncChannel;
use App\Mail\Contact;
use App\Models\Channel;
use App\Models\RequestStat;
use App\Models\Whitelist;
use App\Patreon\PatreonAPI;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Vinkla\Hashids\Facades\Hashids;

class BroadcasterController extends Controller
{
    /**
     * @param Request $request
     *
     * @return View
     */
    public function index(Request $request): View
    {
        $channel = $request->user()->channel;
        $id = Hashids::encode($channel->id);
        $base_url = route('home') . "/list/$id/";
        $db_plans = json_decode($channel->valid_plans);
        if (is_null($db_plans)) {
            $plans = [
                'prime' => true,
                'tier1' => true,
                'tier2' => true,
                'tier3' => true,
            ];
        } else {
            $plans = [
                //'prime' => in_array('Prime', $db_plans),
                'tier1' => in_array('1000', $db_plans) || in_array('Prime', $db_plans),
                'tier2' => in_array('2000', $db_plans),
                'tier3' => in_array('3000', $db_plans),
            ];
        }

        return view('broadcaster.index', [
            'name' => $channel->owner->name,
            'enabled' => $channel->enabled,
            'base_url' => $base_url,
            'plans' => $plans,
            'sync' => $channel->sync,
            'sync_option' => $channel->sync_option,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return View
     */
    public function links(Request $request): View
    {
        $channel = $request->user()->channel;
        $id = Hashids::encode($channel->id);
        $base_url = route('home') . "/list/$id/";

        $patreonTiers = null;
        if ($channel->owner->patreon) {
            $patreonTiers = [];
            $api = new PatreonAPI($request->user()->patreon);
            $campaign = $api->getCampaign($request->user()->patreon->campaign_id, [
                'include' => 'tiers',
                'fields[tier]' => ['title', 'published'],
            ]);
            foreach ($campaign->included as $tier) {
                if ( ! $tier->attributes->published) {
                    continue;
                }

                $patreonTiers[] = [
                    'id' => $tier->id,
                    'title' => $tier->attributes->title,
                ];
            }
        }

        return view('broadcaster.links', [
            'name' => $channel->owner->name,
            'base_url' => $base_url,
            'patreon' => $patreonTiers,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $inputs = $request->validate([
            'list_toggle' => 'required|boolean',
            'sync_option' => [
                'required',
                Rule::in(['1day', '2day', '7day']),
            ],
            'sync_toggle' => 'required|boolean',
            'plan' => 'required|array',
            'plan.*' => 'boolean',
        ]);
        $plans = $inputs['plan'];
        $new_plans = [];
        //if ($plans['prime']) array_push($new_plans, 'Prime');
        if ($plans['tier1']) {
            array_push($new_plans, '1000', 'Prime');
        }
        if ($plans['tier2']) {
            array_push($new_plans, '2000');
        }
        if ($plans['tier3']) {
            array_push($new_plans, '3000');
        }

        $user = $request->user();
        $channel = $user->channel;
        $channel->valid_plans = json_encode($new_plans);
        $channel->enabled = $inputs['list_toggle'];
        $channel->sync = $inputs['sync_toggle'];
        $channel->sync_option = $inputs['sync_option'];
        $channel->save();

        return redirect()->route('broadcaster')->with('success', 'Successfully saved settings');
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function contact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'contact_email' => 'required|email',
            'contact_message' => 'required',
        ]);
        $to = $validated['contact_email'];
        $user = $request->user();
        $message = $validated['contact_message'];
        Mail::to('whitelist@gorymoon.se')->queue(new Contact($user->display_name, $user->name, $to, $message));

        return redirect()->route('broadcaster')->with('success', 'Message successfully sent');
    }

    /**
     * @param Request $request
     *
     * @return View
     */
    public function userlist(Request $request): View
    {
        $db_user = $request->user();

        return view('broadcaster.userlist',
            [
                'channel_id' => $db_user->channel->id,
                'name' => $db_user->name,
            ]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function userlistData(Request $request): JsonResponse
    {
        $channel = $request->user()->channel;
        $query = $channel->whitelist()->newQuery();

        if ($request->has('sort') && '' != $request->sort) {
            // handle multisort
            $sorts = explode(',', $request->sort);
            foreach ($sorts as $sort) {
                list($sortCol, $sortDir) = explode('|', $sort);
                $query = $query->orderBy($sortCol, $sortDir);
            }
        } else {
            $query = $query->orderBy('id', 'asc');
        }

        if ($request->exists('filter')) {
            $query->where(function ($q) use ($request) {
                $q->where('username', 'like', "%{$request->filter}%");
            });
        }

        $perPage = $request->has('per_page') ? (int) $request->per_page : 15;

        $pagination = $query->with(['minecraft', 'steam'])->paginate($perPage);
        $pagination->appends([
            'sort' => $request->sort,
            'filter' => $request->filter,
            'per_page' => $request->per_page,
        ]);

        return response()->json($pagination);
    }

    /**
     * @param $id
     *
     * @return Builder
     */
    private static function getStatBase($id): Builder
    {
        return DB::table('whitelists')->selectRaw('COUNT(id) as num')->where('channel_id', $id);
    }

    /**
     * @param Channel $channel
     *
     * @return Collection
     */
    private static function getStats(Channel $channel): Collection
    {
        $total = self::getStatBase($channel->id);
        $subs = self::getStatBase($channel->id)->whereNotNull('user_id');
        $custom = self::getStatBase($channel->id)->whereNull('user_id');
        $invalid = self::getStatBase($channel->id)->where('valid', false);
        $minecraft = self::getStatBase($channel->id)->whereNotNull('minecraft_id');

        return self::getStatBase($channel->id)->whereNotNull('steam_id')
            ->unionAll($minecraft)
            ->unionAll($invalid)
            ->unionAll($custom)
            ->unionAll($subs)
            ->unionAll($total)
            ->get();
    }

    /**
     * @return JsonResponse
     */
    public function listStats(): JsonResponse
    {
        $result = $this->getStats(Auth::user()->channel);

        return response()->json([
            'total' => $result[5]->num,
            'subscribers' => $result[4]->num,
            'custom' => $result[3]->num,
            'invalid' => $result[2]->num,
            'minecraft' => $result[1]->num,
            'steam' => $result[0]->num,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function addUser(Request $request): RedirectResponse
    {
        $channel = Auth::user()->channel;
        $inputs = $request->validate([
            'usernames' => 'required|array',
            'usernames.*' => Rule::unique('whitelists', 'username')->where(function ($query) use ($channel) {
                return $query->where('channel_id', $channel->id);
            }),
        ]);

        if (is_null($channel)) {
            response()->json((object) ['message' => 'Invalid user'], 403);
        }

        $whitelist = [];
        foreach (array_filter($inputs['usernames']) as $user) {
            $entry = new Whitelist();
            $entry->username = $user;
            $entry->channel()->associate($channel);
            $entry->save();
            $whitelist[] = $entry;
        }
        SyncAllMinecraftNames::dispatch($channel, $whitelist);
        $channel->whitelist_dirty = true;
        $channel->save();

        return redirect()->route('broadcaster.list')->with('success', 'Names successfully added to the whitelist');
    }

    /**
     * @return JsonResponse
     */
    public function removeAll(): JsonResponse
    {
        $channel = Auth::user()->channel;
        if (is_null($channel)) {
            response()->json((object) ['message' => 'Invalid user'], 403);
        }
        $channel->whitelist()->delete();

        return response()->json();
    }

    /**
     * @return JsonResponse
     */
    public function removeInvalid(): JsonResponse
    {
        $channel = Auth::user()->channel;
        if (is_null($channel)) {
            response()->json((object) ['message' => 'Invalid user'], 403);
        }
        $channel->whitelist()->where('valid', '0')->delete();

        return response()->json();
    }

    /**
     * @param $id
     *
     * @throws Exception
     *
     * @return JsonResponse
     */
    public function removeEntry($id): JsonResponse
    {
        $decoded_id = Hashids::connection('whitelist')->decode($id)[0];
        $channel = Auth::user()->channel;
        if (is_null($channel)) {
            response()->json((object) ['message' => 'Invalid user'], 403);
        }

        $entry = $channel->whitelist->find($decoded_id);
        if (is_null($entry)) {
            return response()->json((object) ['message' => 'User not in whitelist'], 404);
        }
        $name = $entry->username;
        $entry->delete();
        $channel->whitelist_dirty = true;
        $channel->save();

        return response()->json(['user' => $name]);
    }

    /**
     * @return JsonResponse
     */
    public function sync(): JsonResponse
    {
        $channel = Auth::user()->channel;
        if (is_null($channel)) {
            response()->json('Invalid user', 403);
        }

        SyncChannel::dispatch($channel);

        return response()->json();
    }

    /**
     * @param Channel $channel
     *
     * @return array
     */
    public static function getStatsArray(Channel $channel): array
    {
        $result = self::getStats($channel);
        $stats = $channel->stats()->whereDate('created_at', '>=', Carbon::now()->subDays(2)->subHour()->toDateTimeString())->get();
        list($formatted, $day, $two_days) = RequestStat::parseStats($stats);

        return [
            'stats' => json_encode($formatted),
            'total' => $channel->requests,
            'day' => $day,
            'twodays' => $two_days,
            'whitelist' => (object) [
                'total' => $result[5]->num,
                'subscribers' => $result[4]->num,
                'custom' => $result[3]->num,
                'invalid' => $result[2]->num,
                'minecraft' => $result[1]->num,
                'steam' => $result[0]->num,
            ],
        ];
    }

    /**
     * @return View
     */
    public function stats(): View
    {
        $channel = Auth::user()->channel;

        return view('broadcaster.stats', self::getStatsArray($channel));
    }
}
