<?php

namespace App\Jobs;

use App\Models\Whitelist;
use App\Notifications\MCUserSyncDone;
use App\Utils\MinecraftUtils;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncMinecraftName implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    /**
     * @var Whitelist
     */
    private Whitelist $whitelist;

    /**
     * Create a new job instance.
     *
     * @param Whitelist $whitelist
     */
    public function __construct(Whitelist $whitelist)
    {
        $this->whitelist = $whitelist;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $response = MinecraftUtils::instance()->getProfile($this->whitelist->username);
        $channel = $this->whitelist->channel;
        $name = '';
        if ( ! is_null($response)) {
            $data = $this->whitelist->minecraft()->updateOrCreate(['uuid' => $response->id], ['username' => $response->name]);
            $name = $data->username;
            $this->whitelist->minecraft()->associate($data);
            $this->whitelist->save();
        } else {
            $this->whitelist->minecraft()->delete();
        }
        $this->whitelist->user->notify(new MCUserSyncDone($name));

        $channel->whitelist_dirty = true;
        $channel->save();
    }
}
