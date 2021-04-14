<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Encrypt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whitelist:encrypt {data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Returns the data encrypted';

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
        $data = $this->argument('data');
        $this->info('Encrypted: ' . encrypt($data));
    }
}
