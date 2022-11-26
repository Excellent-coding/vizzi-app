<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Models\Domain;

class RegisterNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    protected $code;
    protected $url;

    public function __construct($code, $url)
    {
        $this->code = $code;
        if ($url) {
            $this->url = $url.'.';
        } else {
            $this->url = '';
        }
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
        $domain_id = auth()->user()->domain_id;

        $domain = Domain::find($domain_id);

        return (new MailMessage)
            ->line('You have been invited to create an Exhibit Booth!')
                    ->action('Create Account', url('https://'.$domain->domain.'.vizzi.live/auth/wizard/'.$this->code))
                    ->line('Thank you for using our application!');
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
