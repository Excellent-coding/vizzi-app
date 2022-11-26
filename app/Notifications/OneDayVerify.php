<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Auth\Notifications\VerifyEmail as Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class OneDayVerify extends Notification
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
        $url = $this->verificationUrl($notifiable);
        return (new MailMessage)
            ->subject("Starting tomorrow! MedWeek 2020")
            ->line("MED Week 2020 is almost here!")
            ->line('Tomorrow morning you will receive a link that will give you access to the virtual experience. In the meantime, we have attached an information guide to this email on what to expect inside the virtual event. Throughout the week, make sure you share your virtual experience on social media with the Hashtag #MEDWeek2020')
            ->line("We can't wait to see you there!")
            ->action(
                'Instructions for Event',
                'https://mbda.vizzi.live/assets/pdf/mbda_attendee_guide.pdf'
            ) // todo: use Lang::getFromJson( here
            ->line('You have not verified your email address yet! You can also set your avatar and edit settings. Please click the link below to verify your email address.')
            ->line(
                new \Illuminate\Support\HtmlString('<a href="' . $url . '">Verify Email Address</a>')                
            ); // todo: use Lang::getFromJson( here
    }

}
