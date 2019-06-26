<?php

namespace App\Jobs;

use App\Models\RequestStat;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class CleanRequestStats implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $amount = RequestStat::whereDate('created_at', '<', Carbon::now()->subDay(2))->delete();
        } catch (Exception $e) {
            Log::error('Failed to delete old stat data', ['ex' => $e]);
            return;
        }
        Log::info('Removed ' . $amount . ' stat data');
    }
}
