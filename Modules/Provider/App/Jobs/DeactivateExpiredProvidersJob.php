<?php

namespace Modules\Provider\App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Modules\Provider\App\Models\Provider;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DeactivateExpiredProvidersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::beginTransaction();
        try {
            $today = Carbon::today();
            $expiredProviders = Provider::query()
                ->whereNotNull('end_date')
                ->whereDate('end_date', '<', $today)
                ->where('is_active', 1)
                ->get();
                
            $deactivatedCount = 0;
            foreach ($expiredProviders as $provider) {
                $provider->update(['is_active' => 0]);
                $deactivatedCount++;
            }
            Log::info("Deactivated {$deactivatedCount} expired providers.");
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error deactivating expired providers: " . $e->getMessage());
        }
    }
}
