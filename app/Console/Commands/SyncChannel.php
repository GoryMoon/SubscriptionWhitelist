<?php

namespace App\Console\Commands;

use App\Models\TwitchUser;
use Illuminate\Console\Command;

class SyncChannel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whitelist:sync_channel {channel}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncs channel subscriptions';

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

        $channel = TwitchUser::where('name', $channel_name)->first()->channel;
        \App\Jobs\SyncChannel::dispatch($channel);
    }
}
