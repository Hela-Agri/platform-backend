<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class MailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $user;
    public $subject;
    public $message;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user,$message,$subject)
    {
        $this->user = $user;
        $this->subject = $subject;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return  ['mail'];
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
                    ->subject($this->subject)
                    ->line($this->message)
                    ->line('System powered by'.env('MAIL_FROM_NAME','').'.');
    }

}
