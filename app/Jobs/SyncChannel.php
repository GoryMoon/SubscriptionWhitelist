<?php

namespace App\Jobs;

use App\Models\Channel;
use App\Models\Whitelist;
use App\Notifications\SubSyncDone;
use App\Utils\TwitchUtils;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncChannel implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var Collection|Whitelist[]
     */
    private $whitelists;
    private Channel $channel;
    public int $tries = 2;

    /**
     * Create a new job instance.
     *
     * @param Channel $channel
     * @param Collection|Whitelist[] $whitelists
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
        $owner_id = $this->channel->owner->id;
        $name = $this->channel->owner->name;
        Log::info("Syncing channel $name", [$this->channel]);
        for ($i = 0; $i < $size; ++$i) {
            $entry = $this->whitelists[$i];
            if (is_null($entry->user)) {
                continue;
            }
            if ($checked >= 550) {
                SyncChannel::dispatch($this->channel, array_slice($this->whitelists, $i))->delay(now()->addMinutes(5));
                break;
            }

            $channels[] = $entry;
            if ($i + 1 == $size || 80 == count($channels)) {
                $channels = collect($channels)->mapWithKeys(function ($item) {
                    return [$item->user->uid => $item];
                });
                $subs = TwitchUtils::checkSubscriptions($this->channel, $channels->map(function ($item, $key) {
                    return $key;
                }));

                if ( ! is_null($subs)) {
                    foreach ($subs as $key => $value) {
                        $whitelist = $channels->get($key);

                        if ($whitelist->user_id === $owner_id) {
                            $value = true; // Whitelist on own channel are always valid
                        }
                        if ($whitelist->valid != $value) {
                            $whitelist->valid = $value;
                            $whitelist->save();
                            $changed = true;
                        }
                    }
                } else {
                    $this->fail();
                }
                ++$checked;
                $channels = [];
            }
        }

        if ($changed && ! $this->channel->whitelist_dirty) {
            $this->channel->whitelist_dirty = true;
            $this->channel->save();
            Log::info("Channel $name had changed subscriptions, marked dirty");
        }
        Log::info("Finished syncing channel $name");
        $this->channel->notify(new SubSyncDone());
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array
     */
    public function tags(): array
    {
        return ['sync', 'sync_channel:' . $this->channel->id];
    }
}
