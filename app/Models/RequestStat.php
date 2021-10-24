<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

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
}
