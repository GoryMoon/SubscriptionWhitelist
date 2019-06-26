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
        $changed = false;
        $checked = 0;
        $channels = [];
        $size = count($this->whitelists);
        for ($i = 0; $i < $size; $i++) {
            $entry = $this->whitelists[$i];
            if (is_null($entry->user)) {
                continue;
            }
            if ($checked >= 550) {
                SyncChannel::dispatch($this->channel, array_slice($this->whitelists, $i))->delay(now()->addMinutes(5));
                break;
            }

            $channels[] = $entry;
            if ($i + 1 == $size || count($channels) == 100) {
                $channels = collect($channels)->mapWithKeys(function ($item) {
                    return [$item->user->uid => $item];
                });
                $subs = TwitchUtils::checkSubscriptions($this->channel, $channels->map(function ($item, $key) {
                    return $key;
                }));

                if (!is_null($subs)) {
                    foreach ($subs as $key => $value) {
                        $whitelist = $channels->get($key);

                        if ($whitelist->valid != $value) {
                            $whitelist->valid = $value;
                            $whitelist->save();
                            $changed = true;
                        }
                    }
                }
                $checked++;
                $channels = [];
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
