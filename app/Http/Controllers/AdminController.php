<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\RequestStat;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('twitch');
        $this->middleware('admin');
    }

    /**
     * @param int $id
     * @return \Illuminate\Database\Query\Builder
     */
    private static function getStatBase(){
        return DB::table('whitelists')->selectRaw('COUNT(id) as num');
    }

    public function stats() {
        $channels = Channel::get();
        $data = RequestStat::get();

        $requests = $channels->map(function ($values) {
            return $values->requests;
        })->sum();

        $stats = $data->countBy(function ($time) {
            return $time->created_at->minute(0)->second(0)->toDateTimeString();
        });

        $formatted = array();
        $time = Carbon::now()->minute(0)->second(0);
        for ($i = 0; $i < 48; $i++) {
            $formatted[] = [
                'time' => Carbon::make($time)->format('Y-m-d\TH:i:sP'),
                'requests' => $stats->get($time->format("Y-m-d H:i:s"), 0)
            ];
            $time->subHour();
        }

        $channels = Channel::where('enabled', true)->count();
        $total = self::getStatBase();
        $subs = self::getStatBase()->whereNotNull('user_id');
        $custom = self::getStatBase()->whereNull('user_id');
        $result = self::getStatBase()->where('valid', false)->unionAll($custom)->unionAll($subs)->unionAll($total)->get();

        return view('admin.stats', [
            'stats' => json_encode($formatted),
            'total' => $requests,
            'channels' => $channels,
            'whitelist' => (object)[
                'total' => $result[3]->num,
                'subscribers' => $result[2]->num,
                'custom' => $result[1]->num,
                'invalid' => $result[0]->num
            ]
        ]);


    }

}
