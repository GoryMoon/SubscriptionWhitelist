<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\TwitchUser
 *
 * @property int $id
 * @property string $uid
 * @property string $name
 * @property string $display_name
 * @property string $broadcaster_type
 * @property string|null $access_token
 * @property int|null $channel_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Channel|null $channel
 * @property-read Collection|Whitelist[] $whitelist
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
 * @mixin Eloquent
 */
class TwitchUser extends Model
{

    /**
     * Returns the channel the user has
     * @return BelongsTo
     */
    public function channel() {
        return $this->belongsTo('App\Models\Channel');
    }

    /**
     * Returns the whitelist the user have
     * @return HasMany
     */
    public function whitelist() {
        return $this->hasMany('App\Models\Whitelist', 'user_id');
    }

}
