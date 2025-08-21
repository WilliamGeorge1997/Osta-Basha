<?php

namespace Modules\Admin\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\User\App\Models\User;
use Modules\Notification\App\Models\Notification;
use Modules\Notification\App\Notifications\ExpoNotification;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $title;
    protected $description;
    protected $image;
    protected $target_type;
    protected $target_ids;

    public function __construct($title, $description, $image, $target_type = null, $target_ids = [])
    {
        $this->title = $title;
        $this->description = $description;
        $this->image = $image;
        $this->target_type = $target_type;
        $this->target_ids = $target_ids;
    }

    public function handle(): void
    {
        $query = User::query()
            ->where('is_active', 1)
            ->whereNotNull('expo_token')
            ->where('expo_token', '!=', '');

        if ($this->target_type) {
            switch ($this->target_type) {
                case 'clients':
                    $query->where('type', User::TYPE_CLIENT);
                    break;
                case 'providers':
                    $query->where('type', User::TYPE_SERVICE_PROVIDER);
                    break;
                case 'shop_owners':
                    $query->where('type', User::TYPE_SHOP_OWNER);
                    break;
                case 'all':
                    break;
            }
        } elseif (!empty($this->target_ids)) {
            $query->whereIn('id', $this->target_ids);
        }

        if ($query->count() === 0) {
            return;
        }

        $query->chunkById(300, function ($users) {
            foreach ($users as $user) {
                Notification::create([
                    'title' => $this->title,
                    'description' => $this->description,
                    'image' => $this->image,
                    'notifiable_id' => $user->id,
                    'notifiable_type' => User::class,
                ]);

                $user->notify(new ExpoNotification(
                    $this->title,
                    $this->description,
                    ['title' => $this->title, 'description' => $this->description]
                ));
            }
        });
    }
}