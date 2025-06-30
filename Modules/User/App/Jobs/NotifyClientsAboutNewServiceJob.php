<?php

namespace Modules\User\App\Jobs;

use Illuminate\Bus\Queueable;
use Modules\User\App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Common\Helper\FCMService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Notification\Service\NotificationService;

class NotifyClientsAboutNewServiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $userType;
    protected $city;
    protected $country;
    protected $chunkSize = 300;

    /**
     * Create a new job instance.
     */
    public function __construct($userId, $userType, $city, $country)
    {
        $this->userId = $userId;
        $this->userType = $userType;
        $this->city = $city;
        $this->country = $country;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $serviceProvider = User::find($this->userId);
            if (!$serviceProvider) {
                Log::error("User not found for notification job: {$this->userId}");
                return;
            }
            $title = '';
            $description = '';
            if ($this->userType === User::TYPE_SERVICE_PROVIDER) {
                $title = 'مزود خدمة جديد في منطقتك';
                $description = 'تم تسجيل مزود خدمة جديد ' . $serviceProvider->first_name . ' ' . $serviceProvider->last_name . ' في ' . $this->city;
            } else if ($this->userType === User::TYPE_SHOP_OWNER) {
                $title = 'متجر جديد في منطقتك';
                $description = 'تم تسجيل متجر جديد ' . $serviceProvider->first_name . ' ' . $serviceProvider->last_name . ' في ' . $this->city;
            } else {
                Log::error("Invalid user type for notification job: {$this->userType}");
                return;
            }
            $notificationService = new NotificationService();
            User::query()
                ->where('type', User::TYPE_CLIENT)
                ->where('is_active', 1)
                ->where('city', $this->city)
                ->where('country', $this->country)
                ->whereNotNull('fcm_token')
                ->where('id', '!=', $this->userId)
                ->chunkById($this->chunkSize, function ($clients) use ($notificationService, $title, $description) {
                    try {
                        foreach ($clients as $client) {
                            $data = [
                                'title' => $title,
                                'description' => $description,
                                'user_id' => $client->id,
                            ];
                            $notificationService->save($data, User::class);
                        }
                        $fcm = new FCMService();
                        $clientTokens = $clients->pluck('fcm_token')->filter()->all();
                        if (count($clientTokens) > 0) {
                            $data = [
                                'title' => $title,
                                'description' => $description,
                            ];
                            $fcm->sendNotification($data, $clientTokens);
                        }
                        Log::info("Processed notification batch: saved " . count($clients) . " notifications, sent to " . count($clientTokens) . " devices");
                    } catch (\Exception $e) {
                        Log::error("Error processing notification batch: " . $e->getMessage());
                    }
                });
            Log::info("Notifications job completed for new {$this->userType} in {$this->city}, {$this->country}");
        } catch (\Exception $e) {
            Log::error("Error in NotifyClientsAboutNewServiceJob: " . $e->getMessage());
        }
    }
}