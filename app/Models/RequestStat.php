<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;


/**
 * App\Models\RequestStat
 *
 * @property int $id
 * @property int $channel_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|RequestStat newModelQuery()
 * @method static Builder|RequestStat newQuery()
 * @method static Builder|RequestStat query()
 * @method static Builder|RequestStat whereChannelId($value)
 * @method static Builder|RequestStat whereCreatedAt($value)
 * @method static Builder|RequestStat whereId($value)
 * @method static Builder|RequestStat whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read Channel $channel
 */
class RequestStat extends Model
{

    public function channel() {
        return $this->belongsTo(Channel::class);
    }

    /**
     * Returns formatted time stats and one and two days usage
     * @param  RequestStat[]|Collection $data
     * @return int[]
     */
    public static function parseStats($data) {
        $stats = $data->countBy(function ($time) {
            return $time->created_at->minute(0)->second(0)->toDateTimeString();
        });

        $time = \Carbon\Carbon::now()->minute(0)->second(0);
        $formatted = array();
        $day = 0;
        $twodays = 0;
        for ($i = 0; $i < 48; $i++) {
            $stat = $stats->get($time->format("Y-m-d H:i:s"), 0);
            $formatted[] = [
                'time' => Carbon::make($time)->format('Y-m-d\TH:i:sP'),
                'requests' => $stat
            ];
            $time->subHour();
            if ($i > 24) {
                $twodays += $stat;
            } else {
                $day += $stat;
                $twodays += $stat;
            }
        }
        return array( $formatted, $day, $twodays );
    }

}
