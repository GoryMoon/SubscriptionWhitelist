<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\PatreonUser.
 *
 * @property int $id
 * @property string $patreon_id
 * @property string|null $vanity
 * @property string|null $url
 * @property string|null $campaign_id
 * @property string|null $access_token
 * @property string|null $refresh_token
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property \App\Models\TwitchUser $user
 *
 * @method static Builder|PatreonUser newModelQuery()
 * @method static Builder|PatreonUser newQuery()
 * @method static Builder|PatreonUser query()
 * @method static Builder|PatreonUser whereAccessToken($value)
 * @method static Builder|PatreonUser whereCampaignId($value)
 * @method static Builder|PatreonUser whereCreatedAt($value)
 * @method static Builder|PatreonUser whereId($value)
 * @method static Builder|PatreonUser wherePatreonId($value)
 * @method static Builder|PatreonUser whereRefreshToken($value)
 * @method static Builder|PatreonUser whereUpdatedAt($value)
 * @method static Builder|PatreonUser whereUrl($value)
 * @method static Builder|PatreonUser whereUserId($value)
 * @method static Builder|PatreonUser whereVanity($value)
 * @mixin Eloquent
 */
class PatreonUser extends TokenModel
{
    protected $fillable = [
        'patreon_id',
        'vanity',
        'url',
        'campaign_id',
        'access_token',
        'refresh_token',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(TwitchUser::class, 'user_id');
    }
}
