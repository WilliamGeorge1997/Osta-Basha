<?php

namespace Modules\User\App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserForgetPasswordOtp extends Notification
{
    public $otp;

    /**
     * Create a new notification instance.
     *
     * @param string $otp
     */
    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Password Reset OTP')
            ->greeting('Hello ' . ($notifiable->first_name ?? 'User') . '!')
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->line('Your OTP verification code is: **' . $this->otp . '**')
            ->line('If you did not request a password reset, no further action is required.')
            ->salutation('Thank you!');
    }
}
