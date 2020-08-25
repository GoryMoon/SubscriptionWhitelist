<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\SteamUser
 *
 * @property int $id
 * @property string $steam_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read TwitchUser $user
 * @method static Builder|SteamUser newModelQuery()
 * @method static Builder|SteamUser newQuery()
 * @method static Builder|SteamUser query()
 * @method static Builder|SteamUser whereCreatedAt($value)
 * @method static Builder|SteamUser whereId($value)
 * @method static Builder|SteamUser whereSteamId($value)
 * @method static Builder|SteamUser whereUpdatedAt($value)
 * @method static Builder|SteamUser whereUserId($value)
 * @mixin Eloquent
 */
class SteamUser extends Model
{
    protected $fillable = [
        'steam_id',
        'name',
        'profile_url'
    ];

    public function user() {
        return $this->belongsTo(TwitchUser::class, 'user_id');
    }

    public function whitelist() {
        return $this->hasMany(Whitelist::class, 'steam_id');
    }
}
