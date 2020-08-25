<?php

namespace App\Models;

use Eloquent;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Vinkla\Hashids\Facades\Hashids;

/**
 * App\Models\TwitchUser
 *
 * @property int $id
 * @property string $uid
 * @property string $name
 * @property string $display_name
 * @property string $broadcaster_type
 * @property string|null $access_token
 * @property string|null $refresh_token
 * @property int|null $channel_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Channel|null $channel
 * @property-read Collection|Whitelist[] $whitelist
 * @property-read int|null $notifications_count
 * @property-read int|null $whitelist_count
 * @property-read SteamUser|null $steam
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @method static Builder|TwitchUser newModelQuery()
 * @method static Builder|TwitchUser newQuery()
 * @method static Builder|TwitchUser query()
 * @method static Builder|TwitchUser whereAccessToken($value)
 * @method static Builder|TwitchUser whereBroadcasterType($value)
 * @method static Builder|TwitchUser whereChannelId($value)
 * @method static Builder|TwitchUser whereCreatedAt($value)
 * @method static Builder|TwitchUser whereDisplayName($value)
 * @method static Builder|TwitchUser whereId($value)
 * @method static Builder|TwitchUser whereName($value)
 * @method static Builder|TwitchUser whereUid($value)
 * @method static Builder|TwitchUser whereUpdatedAt($value)
 * @method static Builder|TwitchUser whereRefreshToken($value)
 * @mixin Eloquent
 */
class TwitchUser extends Model implements AuthenticatableContract
{
    use Notifiable, Authenticatable;

    /**
     * Returns the channel the user has
     * @return BelongsTo
     */
    public function channel() {
        return $this->belongsTo(Channel::class);
    }

    /**
     * Returns the whitelist the user have
     * @return HasMany
     */
    public function whitelist() {
        return $this->hasMany(Whitelist::class, 'user_id');
    }

    /**
     * Returns the connected steam account if any
     * @return HasOne
     */
    public function steam() {
        return $this->hasOne(SteamUser::class, 'user_id');
    }

    /**
     * Encrypts the token and sets it
     * @param $value string
     */
    public function setRefreshTokenAttribute($value) {
        if (is_null($value)) {
            $this->attributes['refresh_token'] = null;
        } else {
            $this->attributes['refresh_token'] = encrypt($value);
        }
    }

    /**
     * Encrypts the token and sets it
     * @param $value string
     */
    public function setAccessTokenAttribute($value) {
        if (is_null($value)) {
            $this->attributes['access_token'] = null;
        } else {
            $this->attributes['access_token'] = encrypt($value);
        }
    }

    public function receivesBroadcastNotificationsOn(){
        return 'users.'. Hashids::connection('user')->encode($this->id);
    }

}
