<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Whitelist
 *
 * @property int $id
 * @property string $username
 * @property int $valid
 * @property int|null $user_id
 * @property int $channel_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Channel $channel
 * @property-read TwitchUser|null $user
 * @method static Builder|Whitelist newModelQuery()
 * @method static Builder|Whitelist newQuery()
 * @method static Builder|Whitelist query()
 * @method static Builder|Whitelist whereChannelId($value)
 * @method static Builder|Whitelist whereCreatedAt($value)
 * @method static Builder|Whitelist whereId($value)
 * @method static Builder|Whitelist whereUpdatedAt($value)
 * @method static Builder|Whitelist whereUserId($value)
 * @method static Builder|Whitelist whereUsername($value)
 * @method static Builder|Whitelist whereValid($value)
 * @mixin Eloquent
 * @property-read bool $is_subscriber
 * @property-read bool $is_valid
 * @property int|null $minecraft_id
 * @property-read array $status
 * @property-read \App\Models\MinecraftUser|null $minecraft
 * @method static Builder|Whitelist whereMinecraftId($value)
 */
class Whitelist extends Model
{

    protected $hidden = ['user_id', 'channel_id', 'created_at', 'updated_at', 'valid', 'minecraft'];
    protected $appends = ['is_subscriber', 'status'];

    public function user() {
        return $this->belongsTo(TwitchUser::class, 'user_id');
    }

    public function channel() {
        return $this->belongsTo(Channel::class);
    }

    public function minecraft() {
        return $this->belongsTo(MinecraftUser::class, 'minecraft_id');
    }

    /**
     * @return bool
     */
    public function getIsSubscriberAttribute() {
        return $this->user_id != null;
    }

    /**
     * @return array
     */
    public function getStatusAttribute() {
        $minecraft = $this->minecraft;
        $name = "";
        if (!is_null($minecraft)) {
            $name = $minecraft->username;
        }
        return ['valid'  => $this->valid == true, 'minecraft' => $name];
    }

    protected static function boot(){
        parent::boot();

        self::deleting(function (Whitelist $whitelist) {
            $whitelist->minecraft()->delete();
        });
    }
}
