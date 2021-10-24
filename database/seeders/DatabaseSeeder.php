<?php

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\RequestStat;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        // Stats for all channels.
        Channel::all()->each(function (Channel $channel) {
            RequestStat::factory()
                ->count(rand(0, 500))
                ->for($channel)
                ->create();
        });
    }
}
