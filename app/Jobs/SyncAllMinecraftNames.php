<?php

namespace App\Jobs;

use App\Models\Channel;
use App\Models\Whitelist;
use App\Notifications\MCSyncDone;
use App\Utils\MinecraftUtils;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class SyncAllMinecraftNames implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    /**
     * @var Channel|null
     */
    private ?Channel $channel;

    /**
     * @var Collection
     */
    private Collection $whitelist;

    /**
     * Create a new job instance.
     *
     * @param Channel|null $channel
     * @param Whitelist[] $whitelist
     */
    public function __construct(Channel $channel, array $whitelist)
    {
        $this->channel = $channel;
        $this->whitelist = collect($whitelist);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $names = [];
        foreach ($this->whitelist as $item) {
            $names[] = $item->username;
        }
        $response = MinecraftUtils::instance()->getProfiles($names);
        if ( ! is_null($response)) {
            $whitelistMap = $this->whitelist->mapWithKeys(function ($item, $key) {
                return [strtolower($item['username']) => $item];
            });
            foreach ($response as $item) {
                $whitelist = $whitelistMap->get(strtolower($item->name));
                $data = $whitelist->minecraft()->updateOrCreate(['uuid' => $item->id], ['username' => $item->name]);
                $whitelist->minecraft()->associate($data);
                $whitelist->save();
            }
            $this->channel->notify(new MCSyncDone());
        }
    }
}
