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
 * App\Models\SteamUser.
 *
 * @property int $id
 * @property string $steam_id
 * @property string $name
 * @property string $profile_url
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property \App\Models\TwitchUser $user
 * @property Collection|\App\Models\Whitelist[] $whitelist
 * @property int|null $whitelist_count
 *
 * @method static Builder|SteamUser newModelQuery()
 * @method static Builder|SteamUser newQuery()
 * @method static Builder|SteamUser query()
 * @method static Builder|SteamUser whereCreatedAt($value)
 * @method static Builder|SteamUser whereId($value)
 * @method static Builder|SteamUser whereName($value)
 * @method static Builder|SteamUser whereProfileUrl($value)
 * @method static Builder|SteamUser whereSteamId($value)
 * @method static Builder|SteamUser whereUpdatedAt($value)
 * @method static Builder|SteamUser whereUserId($value)
 * @mixin Eloquent
 */
class SteamUser extends Model
{
    protected $hidden = ['id', 'user_id', 'created_at', 'updated_at'];
    protected $fillable = [
        'steam_id',
        'name',
        'profile_url',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(TwitchUser::class, 'user_id');
    }

    public function whitelist(): HasMany
    {
        return $this->hasMany(Whitelist::class, 'steam_id');
    }
}
