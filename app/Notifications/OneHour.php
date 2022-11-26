<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Auth\Notifications\VerifyEmail as Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;

class OneHour extends Notification
{
    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function joinUrl($notifiable)
    {
        $url = 
            route('join', ['token' => $notifiable->token], true);

        return $url;
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("MED Week 2020 - Friday Sessions")
            ->line("Use the below link to join into the virtual lobby, the conference will begin at 9:30 AM EST in the Main Stage. We can't wait to see you there!")
            ->action(
                'Join Event',
                $this->joinUrl($notifiable)
            )
            ->line(new HtmlString("&middot; Check out all our videos from this weeks sessions in our <a href='https://events.vizzi.live/ondemand-medweek2020/' target='_new'>MEDWeek 2020 OnDemand page.</a><small> <a href='https://events.vizzi.live/ondemand-medweek2020/' target='_new'>https://events.vizzi.live/ondemand-medweek2020/</a></small>"))
            ->line('Throughout the week, make sure you share your virtual experience on social media with the Hashtag #MEDWeek2020');
        }

}
