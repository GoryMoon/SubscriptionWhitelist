<?php

namespace App\Jobs;

use App\Models\MinecraftUser;
use App\Notifications\MCSyncDone;
use App\Utils\MinecraftUtils;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncMinecraftUuids implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var MinecraftUser[]|Collection|null
     */
    private ?array $users;

    /**
     * Create a new job instance.
     *
     * @param MinecraftUser[]|Collection|null $users
     */
    public function __construct(array $users = null)
    {
        $this->users = $users;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $requests = 0;
        if (is_null($this->users)) {
            $this->users = MinecraftUser::get();
        }

        $channel = null;
        for ($i = 0; $i < count($this->users); ++$i) {
            $user = $this->users[$i];
            if ($requests >= 550) {
                SyncMinecraftUuids::dispatch($this->users->slice($i))->delay(now()->addMinutes(11));
                break;
            }

            $name = MinecraftUtils::instance()->getLatestName($user->uuid);
            ++$requests;

            if ( ! is_null($name) && $user->username != $name) {
                $user->username = $name;
                $user->save();
                if (is_null($channel)) {
                    $channel = $user->whitelist->channel;
                }
            }
        }
        if ( ! is_null($channel)) {
            $channel->notify(new MCSyncDone());
            $channel->whitelist_dirty = true;
            $channel->save();
        }
    }
}
