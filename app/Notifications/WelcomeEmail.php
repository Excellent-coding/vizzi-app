<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\User;

class WelcomeEmail extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
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
					->subject("You're in! IAPHS Welcome Email")
					->attach(public_path('assets/documents/Welcome.pdf'))
                    ->line('Hello!')
                    ->line('The IAPHS 2020 Conference is almost here!')
					->line('Please click the link below to access the virtual platform and create your password.  The virtual event space will go live on Wednesday morning. In the meantime, we have attached an informational guide to this email on what to expect inside the virtual event. Throughout the event, make sure you share your virtual experience on social media with the Hashtag #IAPHS2020.')
					->action(
						'Set Password',
						'https://iaphs.vizzi.live/auth/register/'.$notifiable->id
					)
                    ->line('If you have any technical questions, please contact us at help@virtualcreativestudio.com.')
                    ->line('We cant wait to see you there!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
