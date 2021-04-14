<?php

namespace App\Jobs;

use App\Models\TwitchUser;
use App\Utils\TwitchUtils;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncUser implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private TwitchUser $user;
    private string $access_token;

    /**
     * Create a new job instance.
     *
     * @param TwitchUser $user
     * @param string $access_token
     */
    public function __construct(TwitchUser $user, string $access_token)
    {
        $this->user = $user;
        $this->access_token = $access_token;
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

            $subbed = TwitchUtils::checkIfSubbed($this->user, $channel->owner);
            if ($whitelist->valid != $subbed) {
                $whitelist->valid = $subbed;
                $whitelist->save();

                if ( ! $channel->whitelist_dirty) {
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
    public function tags(): array
    {
        return ['sync', 'sync_user:' . $this->user->id];
    }
}
