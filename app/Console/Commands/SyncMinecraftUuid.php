<?php

namespace App\Console\Commands;

use App\Jobs\SyncMinecraftUuids;
use App\Models\TwitchUser;
use Illuminate\Console\Command;

class SyncMinecraftUuid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whitelist:sync_mc_uuid {channel}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncs existing minecraft uuids';

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
     * @return void
     */
    public function handle()
    {
        $channel_name = $this->argument('channel');

        $whitelist = TwitchUser::where('name', $channel_name)->with('channel.whitelist.minecraft')->first()->channel->whitelist;
        $users = [];
        foreach ($whitelist as $entry) {
            if (!is_null($entry->minecraft)) {
                $users[] = $entry->minecraft;
            }
        }
        SyncMinecraftUuids::dispatch($users);
    }
}
