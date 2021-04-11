<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
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
 * @property int $requests
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read TwitchUser|null $owner
 * @property-read Collection|RequestStat[] $stats
 * @property-read int|null $stats_count
 * @property-read Collection|Whitelist[] $whitelist
 * @property-read int|null $whitelist_count
 * @method static Builder|Channel newModelQuery()
 * @method static Builder|Channel newQuery()
 * @method static Builder|Channel query()
 * @method static Builder|Channel whereCreatedAt($value)
 * @method static Builder|Channel whereEnabled($value)
 * @method static Builder|Channel whereId($value)
 * @method static Builder|Channel whereRequests($value)
 * @method static Builder|Channel whereSync($value)
 * @method static Builder|Channel whereSyncOption($value)
 * @method static Builder|Channel whereUpdatedAt($value)
 * @method static Builder|Channel whereValidPlans($value)
 * @method static Builder|Channel whereWhitelistDirty($value)
 * @mixin Eloquent
 */
class Channel extends Model
{
    use Notifiable;

    public function owner() {
        return $this->hasOne(TwitchUser::class);
    }

    public function whitelist() {
        return $this->hasMany(Whitelist::class);
    }

    public function stats() {
        return $this->hasMany(RequestStat::class);
    }

    public function receivesBroadcastNotificationsOn(){
        return 'channel.'.$this->id;
    }

    protected static function booted(){
        static::deleting(function (Channel $channel) {
            $channel->whitelist->each(function (Whitelist $whitelist) {
                $whitelist->delete();
            });
        });
    }

}
