<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\RequestStat;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('twitch');
        $this->middleware('admin');
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

        return view('admin.stats', [
            'stats' => json_encode($formatted),
            'total' => $requests
        ]);
    }

}
