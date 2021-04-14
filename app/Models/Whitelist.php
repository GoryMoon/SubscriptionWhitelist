<?php

namespace App\Models;

use Eloquent;
use Hashids;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Whitelist.
 *
 * @property int $id
 * @property string $username
 * @property int $valid
 * @property int|null $user_id
 * @property int $channel_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $minecraft_id
 * @property int|null $steam_id
 * @property \App\Models\Channel $channel
 * @property string $hash_id
 * @property bool $is_subscriber
 * @property array $status
 * @property \App\Models\MinecraftUser|null $minecraft
 * @property \App\Models\SteamUser|null $steam
 * @property \App\Models\TwitchUser|null $user
 *
 * @method static Builder|Whitelist newModelQuery()
 * @method static Builder|Whitelist newQuery()
 * @method static Builder|Whitelist query()
 * @method static Builder|Whitelist whereChannelId($value)
 * @method static Builder|Whitelist whereCreatedAt($value)
 * @method static Builder|Whitelist whereId($value)
 * @method static Builder|Whitelist whereMinecraftId($value)
 * @method static Builder|Whitelist whereSteamId($value)
 * @method static Builder|Whitelist whereUpdatedAt($value)
 * @method static Builder|Whitelist whereUserId($value)
 * @method static Builder|Whitelist whereUsername($value)
 * @method static Builder|Whitelist whereValid($value)
 * @mixin Eloquent
 */
class Whitelist extends Model
{
    protected $hidden = ['id', 'user_id', 'channel_id', 'created_at', 'updated_at', 'valid', 'minecraft', 'minecraft_id', 'steam', 'steam_id'];
    protected $appends = ['hash_id', 'is_subscriber', 'status'];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(TwitchUser::class, 'user_id');
    }

    /**
     * @return BelongsTo
     */
    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    /**
     * @return BelongsTo
     */
    public function minecraft(): BelongsTo
    {
        return $this->belongsTo(MinecraftUser::class, 'minecraft_id');
    }

    /**
     * @return BelongsTo
     */
    public function steam(): BelongsTo
    {
        return $this->belongsTo(SteamUser::class, 'steam_id');
    }

    /**
     * @return bool
     */
    public function getIsSubscriberAttribute(): bool
    {
        return null != $this->user_id;
    }

    /**
     * @return string
     */
    public function getHashIdAttribute(): string
    {
        return Hashids::connection('whitelist')->encode($this->id);
    }

    /**
     * @return array
     */
    public function getStatusAttribute(): array
    {
        $minecraft = $this->minecraft;
        $name = '';
        if ( ! is_null($minecraft)) {
            $name = $minecraft->username;
        }

        return ['valid' => true == $this->valid, 'minecraft' => $name, 'steam' => isset($this->steam)];
    }

    protected static function booted()
    {
        static::deleting(function (Whitelist $whitelist) {
            $whitelist->minecraft()->delete();
        });
    }
}
