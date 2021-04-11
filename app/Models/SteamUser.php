<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\SteamUser
 *
 * @property int $id
 * @property string $steam_id
 * @property string $name
 * @property string $profile_url
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read TwitchUser $user
 * @property-read Collection|Whitelist[] $whitelist
 * @property-read int|null $whitelist_count
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
        'profile_url'
    ];

    public function user() {
        return $this->belongsTo(TwitchUser::class, 'user_id');
    }

    public function whitelist() {
        return $this->hasMany(Whitelist::class, 'steam_id');
    }
}
