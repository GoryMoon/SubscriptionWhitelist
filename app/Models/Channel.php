<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Channel
 *
 * @property int $id
 * @property int $enabled
 * @property mixed|null $valid_plans
 * @property int $sync
 * @property string $sync_option
 * @property int $whitelist_dirty
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read TwitchUser $owner
 * @property-read Collection|Whitelist[] $whitelist
 * @method static Builder|Channel newModelQuery()
 * @method static Builder|Channel newQuery()
 * @method static Builder|Channel query()
 * @method static Builder|Channel whereCreatedAt($value)
 * @method static Builder|Channel whereEnabled($value)
 * @method static Builder|Channel whereId($value)
 * @method static Builder|Channel whereSync($value)
 * @method static Builder|Channel whereSyncOption($value)
 * @method static Builder|Channel whereUpdatedAt($value)
 * @method static Builder|Channel whereValidPlans($value)
 * @method static Builder|Channel whereWhitelistDirty($value)
 * @mixin Eloquent
 * @property int $requests
 * @property-read Collection|RequestStat[] $stats
 * @method static Builder|Channel whereRequests($value)
 */
class Channel extends Model
{

    public function owner() {
        return $this->hasOne('App\Models\TwitchUser');
    }

    public function whitelist() {
        return $this->hasMany('App\Models\Whitelist');
    }

    public function stats() {
        return $this->hasMany('App\Models\RequestStat');
    }

}
