<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\MinecraftUser
 *
 * @property int $id
 * @property string $uuid
 * @property string $username
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Whitelist|null $whitelist
 * @method static Builder|MinecraftUser newModelQuery()
 * @method static Builder|MinecraftUser newQuery()
 * @method static Builder|MinecraftUser query()
 * @method static Builder|MinecraftUser whereCreatedAt($value)
 * @method static Builder|MinecraftUser whereId($value)
 * @method static Builder|MinecraftUser whereUpdatedAt($value)
 * @method static Builder|MinecraftUser whereUsername($value)
 * @method static Builder|MinecraftUser whereUuid($value)
 * @mixin Eloquent
 */
class MinecraftUser extends Model
{
    protected $hidden = ['id', 'created_at', 'updated_at'];
    protected $fillable = ['uuid', 'username'];

    public function whitelist(): HasOne
    {
        return $this->hasOne(Whitelist::class);
    }

}
