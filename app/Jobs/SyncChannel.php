<?php

namespace App\Jobs;

use App\Models\Channel;
use App\Models\Whitelist;
use App\Notifications\SubSyncDone;
use App\Utils\TwitchUtils;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SyncChannel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var Whitelist[]
     */
    private $whitelists;

    /**
     * @var Channel
     */
    private $channel;

    /**
     * Create a new job instance.
     *
     * @param Channel $channel
     * @param null $whitelists
     */
    public function __construct(Channel $channel, $whitelists = null)
    {
        $this->channel = $channel;
        if (is_null($whitelists)) {
            $this->whitelists = $channel->whitelist;
        } else {
            $this->whitelists = $whitelists;
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $owner = $this->channel->owner;
        $changed = false;
        $checked = 0;
        for ($i = 0; $i < count($this->whitelists); $i++) {
            $entry = $this->whitelists[$i];
            if (is_null($entry->user)) {
                continue;
            }
            if ($checked >= 550) {
                SyncChannel::dispatch($this->channel, array_slice($this->whitelists, $i))->delay(now()->addMinutes(5));
                break;
            }
            $subbed = TwitchUtils::checkIfSubbed(false, $entry->user->uid, $this->channel, $owner->uid);
            $checked++;
            if ($entry->valid != $subbed) {
                $entry->valid = $subbed;
                $entry->save();
                $changed = true;
            }
        }

        if ($changed && !$this->channel->whitelist_dirty) {
            $this->channel->whitelist_dirty = true;
            $this->channel->save();
        }
        $this->channel->notify(new SubSyncDone());
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array
     */
    public function tags()
    {
        return ['sync', 'sync_channel:' . $this->channel->id];
    }
}
