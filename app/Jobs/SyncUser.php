<?php

namespace App\Jobs;

use App\Models\TwitchUser;
use App\Utils\TwitchUtils;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Session;

class SyncUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var TwitchUser
     */
    private $user;
    private $access_token;

    /**
     * Create a new job instance.
     *
     * @param TwitchUser $user
     * @param $access_token
     */
    public function __construct(TwitchUser $user, $access_token)
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
        Session::put('access_token', $this->access_token);
        $whitelists = $this->user->whitelist;
        foreach ($whitelists as $whitelist) {
            $channel = $whitelist->channel;

            $subbed = TwitchUtils::checkIfSubbed($this->user->uid, $channel, $channel->owner);
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
