<?php

namespace Modules\ShopOwner\App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Modules\User\App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Admin\App\Models\Admin;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Modules\ShopOwner\App\Models\ShopOwner;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Notification\Service\NotificationService;

class DeactivateExpiredShopOwnersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::beginTransaction();
        try {
            $today = Carbon::today();
            $notificationService = new NotificationService();

            $expiredShopOwners = ShopOwner::query()
                ->whereNotNull('end_date')
                ->whereDate('end_date', '<', $today)
                ->where('is_active', 1)
                ->with('user')
                ->get();

            $deactivatedCount = 0;
            foreach ($expiredShopOwners as $shopOwner) {
                $shopOwner->update(['is_active' => 0]);
                $deactivatedCount++;
                $notificationService->sendNotification(
                    'انتهاء الاشتراك',
                    'لقد انتهى اشتراكك، يرجى تجديد الاشتراك للاستمرار في استخدام الخدمة',
                    $shopOwner->user->id,
                    User::class
                );
                $admins = Admin::where('is_active', 1)->get();
                foreach ($admins as $admin) {
                    if ($admin->fcm_token) {
                        $notificationService->sendNotification(
                            'انتهاء اشتراك صاحب متجر',
                            'انتهى اشتراك صاحب المتجر ' . ($shopOwner->user->name ?? 'صاحب المتجر'),
                            $admin->id,
                            Admin::class
                        );
                    }
                }
            }

            Log::info("Deactivated {$deactivatedCount} expired shop owners.");
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error deactivating expired shop owners: " . $e->getMessage());
        }
    }
}
