<?php

namespace App\Jobs;

use App\Models\RequestStat;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CleanRequestStats implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $amount = RequestStat::whereDate('created_at', '<', Carbon::now()->subDays(2))->delete();
        } catch (Exception $e) {
            Log::error('Failed to delete old stat data', ['ex' => $e]);

            return;
        }
        Log::info('Removed ' . $amount . ' stat data');
    }
}
