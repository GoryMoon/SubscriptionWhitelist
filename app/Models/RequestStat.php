<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;


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
        return $this->belongsTo('App\Models\Channel');
    }

}
