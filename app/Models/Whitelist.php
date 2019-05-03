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
 */
class Whitelist extends Model
{

    protected $hidden = ['user_id', 'channel_id', 'created_at', 'updated_at', 'valid'];
    protected $appends = ['is_subscriber', 'is_valid'];

    public function user() {
        return $this->belongsTo('App\Models\TwitchUser', 'user_id');
    }

    public function channel() {
        return $this->belongsTo('App\Models\Channel');
    }

    /**
     * @return bool
     */
    public function getIsSubscriberAttribute() {
        return $this->user_id != null;
    }

    /**
     * @return bool
     */
    public function getIsValidAttribute() {
        return $this->valid == true;
    }

}
