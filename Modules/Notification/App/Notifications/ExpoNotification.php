<?php

namespace Modules\Notification\App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\ExpoPushNotifications\ExpoChannel;
use NotificationChannels\ExpoPushNotifications\ExpoMessage;

class ExpoNotification extends Notification
{
    use Queueable;

    protected $title;
    protected $body;
    protected $data;

    public function __construct($title, $body, $data = [])
    {
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;
        $this->onQueue('default')->onConnection('database');
    }

    public function via($notifiable)
    {
        return [ExpoChannel::class];
    }

    public function toExpoPush($notifiable)
    {
        $token = $notifiable->routeNotificationForExpoPushNotifications();

        if (empty($token)) {
            \Log::error('No token found');
            return null;
        }

        $message = ExpoMessage::create();
        $message->title($this->title);
        $message->body($this->body);
        $message->enableSound();

        if (!empty($this->data)) {
            $message->setJsonData($this->data);
        }
        \Log::info('ExpoMessage debug:', [
            'token' => $token,
            'message_class' => get_class($message),
            'message_content' => $message->toArray() ?? 'null',
            'title' => $this->title,
            'body' => $this->body
        ]);

        return $message;
    }
}