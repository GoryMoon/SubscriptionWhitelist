<?php

namespace App\Jobs;

use App\Models\TwitchUser;
use App\Utils\TwitchUtils;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SyncUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var TwitchUser
     */
    private $user;

    /**
     * Create a new job instance.
     *
     * @param TwitchUser $user
     */
    public function __construct(TwitchUser $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $whitelists = $this->user->whitelist;
        foreach ($whitelists as $whitelist) {
            $channel = $whitelist->channel;

            $subbed = TwitchUtils::checkIfSubbed(true, $this->user->uid, $channel, $channel->owner->uid);
            if ($whitelist->valid != $subbed) {
                $whitelist->valid = $subbed;
                $whitelist->save();

                if (!$channel->whitelist_dirty) {
                    $channel->whitelist_dirty = true;
                    $channel->save();
                }
            }
        }
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array
     */
    public function tags()
    {
        return ['sync', 'sync_user:' . $this->user->id];
    }
}
