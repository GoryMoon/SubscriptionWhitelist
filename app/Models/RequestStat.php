<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Carbon;

/**
 * App\Models\RequestStat.
 *
 * @property int $id
 * @property int $channel_id
 * @property string|null $agent
 * @property string|null $ip
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property \App\Models\Channel $channel
 *
 * @method static Builder|RequestStat newModelQuery()
 * @method static Builder|RequestStat newQuery()
 * @method static Builder|RequestStat query()
 * @method static Builder|RequestStat whereAgent($value)
 * @method static Builder|RequestStat whereChannelId($value)
 * @method static Builder|RequestStat whereCreatedAt($value)
 * @method static Builder|RequestStat whereId($value)
 * @method static Builder|RequestStat whereIp($value)
 * @method static Builder|RequestStat whereUpdatedAt($value)
 * @mixin Eloquent
 */
class RequestStat extends Model
{
    use HasFactory;

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    /**
     * @param $base Builder|Relation
     * @param $hours int|null
     * @return array
     */
    public static function parseStats($base, ?int $hours): array
    {
        $hours = max(1, min(48, is_null($hours) ? 48 : $hours));

        $time = \Carbon\Carbon::now()->subHours($hours - 1)->minute(0)->second(0)->millisecond(0);
        $timestamp = $time->toDateTimeString();
        $stats = $base
            ->where('created_at', '>=', $timestamp)
            ->selectRaw('DATE_FORMAT(created_at, \'%Y-%m-%d %H:00:00\') as time, count(*) as number')
            ->groupBy('time')
            ->orderBy('time')->get();
        $stats = $stats->map(function ($data) {
            return (object) [
                'y' => $data->number,
                'x' => Carbon::createFromFormat('Y-m-d H:i:s', $data->time)->getTimestampMs(),
            ];
        });

        $statIndex = 0;
        $data = [];
        for ($i = 0; $i < $hours; ++$i) {
            $point = $time->getTimestampMs();
            $stat = $stats->get($statIndex);
            if (is_null($stat) || $stat->x != $point) {
                $data[] = (object) [
                    'y' => 0,
                    'x' => $point,
                ];
                if (is_null($stat)) {
                    ++$statIndex;
                }
            } else {
                $data[] = $stat;
                ++$statIndex;
            }
            $time->addRealHour();
        }
        return $data;
    }
}
