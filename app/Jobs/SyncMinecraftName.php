<?php

namespace App\Jobs;

use App\Models\Whitelist;
use App\Utils\MinecraftUtils;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SyncMinecraftName implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var Whitelist
     */
    private $whitelist;

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
        if (!is_null($response)) {
            $data = $this->whitelist->minecraft()->updateOrCreate(['uuid' => $response->id], ['username' => $response->name]);
            $this->whitelist->minecraft()->associate($data);
            $this->whitelist->save();
        } else {
            $this->whitelist->minecraft()->delete();
        }
        $channel->whitelist_dirty = true;
        $channel->save();
    }
}
