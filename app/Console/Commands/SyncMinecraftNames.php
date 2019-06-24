<?php

namespace App\Console\Commands;

use App\Jobs\SyncAllMinecraftNames;
use App\Models\TwitchUser;
use Illuminate\Console\Command;

class SyncMinecraftNames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whitelist:sync_mc_names {channel}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncs all unmatches names to try to find mc profiles';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $channel_name = $this->argument('channel');

        $channel = TwitchUser::where('name', $channel_name)->with('channel.whitelist')->first()->channel;
        $whitelist = $channel->whitelist;
        $whitelists = [];
        foreach ($whitelist as $entry) {
            if (is_null($entry->minecraft)) {
                $whitelists[] = $entry;
            }
        }
        SyncAllMinecraftNames::dispatch($channel, $whitelists);
    }
}
