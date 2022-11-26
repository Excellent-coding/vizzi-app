<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Auth\Notifications\VerifyEmail as Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;

class Agenda extends Notification
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
            ->subject("The excitement continues: MBDA LIVE")
            ->line(new HtmlString("<p>Good morning,</p><p> Today is Day 4 of National MED Week and the excitement continues!!!! We kick off with MBDA Live! today at 9:30 am where we will hear about the new Office of Policy Analysis and Development from Efrain Gonzalez and we will also hear from US Chamber of Commerce President Suzanne Clark. This will be followed by our next segment at 10:00am of the How I Did It Series featuring  KFA, Inc our Minority Innovative Technology Firm of the Year and Y-Not Manufacturing, our Minority Manufacturing Firm of the Year.</p> <p>Our Eco-System sessions start at 1pm and will feature the following:</p> <p><b>1:00pm:</b></b> Supply Chain Shift: How MBEs Strengthened a Manufacturer’s Response to Disruption</p><p><b>2:00pm:</b> Diversity Wins: Lessons in Contracting and Inclusion from Local-level Transportation Procurement</p><p><b>3:00pm:</b> Straight Talk: Planning, Positioning, and Pitching Your Business</p><p><b>3:30pm:</b>  Artificial Intelligence (AI) will dominate sourcing in the next 5 years.</p> <p><h4>SEE YOU THERE!</h4></p>"))
            ->action(
                'Join Event',
                $this->joinUrl($notifiable)
            )
            ->line(new HtmlString("<b>P.S.</b> - Don't forget to check out all the previous sessions On Demand at <a href='https://events.vizzi.live/ondemand-medweek2020/' target='_new'>https://events.vizzi.live/ondemand-medweek2020/</a>"))
            ->line('Throughout the week, make sure you share your virtual experience on social media with the Hashtag #MEDWeek2020'); // todo: use Lang::getFromJson( here
    }

}
