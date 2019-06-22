<?php

namespace App\Jobs;

use App\Models\MinecraftUser;
use App\Utils\MinecraftUtils;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SyncMinecraftUuids implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var null
     */
    private $users;

    /**
     * Create a new job instance.
     *
     * @param null $users
     */
    public function __construct($users = null)
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
        for ($i = 0; $i < count($this->users); $i++) {
            $user = $this->users[$i];
            $user->uuid;
            if ($requests >= 550) {
                SyncMinecraftUuids::dispatch(array_slice($this->users, $i))->delay(now()->addMinutes(11));
                break;
            }

            $name = MinecraftUtils::instance()->getLatestName($user->uuid);
            $requests++;

            if ($user->username != $name) {
                $user->username = $name;
                $user->save();
                if (is_null($channel)) {
                    $channel = $user->whitelist->channel;
                }
            }
        }
        if (!is_null($channel)) {
            $channel->whitelist_dirty = true;
            $channel->save();
        }

    }
}
