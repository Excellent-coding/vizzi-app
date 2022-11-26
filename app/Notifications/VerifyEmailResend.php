<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Auth\Notifications\VerifyEmail as Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class VerifyEmailResend extends Notification
{
    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        $url = URL::temporarySignedRoute(
            'verification.verify', Carbon::now()->addMinutes(60), ['user' => $notifiable->id]
        );

        return str_replace('/api', '', $url);
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("You're in! - MED Week 2020")
            ->line("We sent you an email to verify your email address but have not seen you complete registration for this event, please click the button below, we're hoping to see you there!")
            ->line('Thank you for registering for MED Week 2020.')
            ->line('Please click the button below to verify your email address.')
            ->action(
                'Verify Email Address',
                $this->verificationUrl($notifiable)
            ); // todo: use Lang::getFromJson( here
    }

}
