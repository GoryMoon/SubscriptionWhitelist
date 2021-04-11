<?php

namespace App\Models;

use Eloquent;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Encryption\DecryptException;
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
 * @property string $access_token
 * @property int|null $channel_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $refresh_token
 * @property-read Channel|null $channel
 * @property-read bool $admin
 * @property-read bool $broadcaster
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read SteamUser|null $steam
 * @property-read Collection|Whitelist[] $whitelist
 * @property-read int|null $whitelist_count
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
 * @method static Builder|TwitchUser whereRefreshToken($value)
 * @method static Builder|TwitchUser whereUid($value)
 * @method static Builder|TwitchUser whereUpdatedAt($value)
 * @mixin Eloquent
 */
class TwitchUser extends Model implements AuthenticatableContract
{
    use Notifiable, Authenticatable;

    protected $fillable = [
        'name',
        'uid',
        'display_name',
        'broadcaster_type',
        'access_token',
        'refresh_token'
    ];

    /**
     * Returns the channel the user has
     * @return BelongsTo
     */
    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    /**
     * Returns the whitelist the user have
     * @return HasMany
     */
    public function whitelist(): HasMany
    {
        return $this->hasMany(Whitelist::class, 'user_id');
    }

    /**
     * Returns the connected steam account if any
     * @return HasOne
     */
    public function steam(): HasOne
    {
        return $this->hasOne(SteamUser::class, 'user_id');
    }

    /**
     * Encrypts the token and sets it
     * @param string|null $value string
     */
    public function setRefreshTokenAttribute(?string $value) {
        if (is_null($value)) {
            $this->attributes['refresh_token'] = null;
        } else {
            $this->attributes['refresh_token'] = encrypt($value);
        }
    }

    /**
     * Decrypts the refresh token and returns it
     * @param string|null $value string
     * @return string|null
     */
    public function getRefreshTokenAttribute(?string $value): ?string
    {
        if (!is_null($value)) {
            try {
                return decrypt($value);
            } catch (DecryptException $e) {
                report($e);
                return null;
            }
        }
        return null;
    }

    /**
     * Encrypts the token and sets it
     * @param string|null $value string
     */
    public function setAccessTokenAttribute(?string $value) {
        if (is_null($value)) {
            $this->attributes['access_token'] = null;
        } else {
            $this->attributes['access_token'] = encrypt($value);
        }
    }

    /**
     * Decrypts the access token and returns it
     * @param string|null $value string
     * @return string
     */
    public function getAccessTokenAttribute(?string $value): ?string
    {
        if (!is_null($value)) {
            try {
                return decrypt($value);
            } catch (DecryptException $e) {
                report($e);
                return null;
            }
        }
        return null;
    }

    public function getAdminAttribute(): bool
    {
        return $this->uid === config('whitelist.admin_id');
    }

    public function getBroadcasterAttribute(): bool
    {
        return $this->broadcaster_type != '' || $this->admin;
    }

    public function receivesBroadcastNotificationsOn(): string
    {
        return 'users.'. Hashids::connection('user')->encode($this->id);
    }

    protected static function booted(){
        static::deleting(function (TwitchUser $user) {
            $user->whitelist->each(function (Whitelist $whitelist) {
                $whitelist->delete();
            });
        });
    }
}
