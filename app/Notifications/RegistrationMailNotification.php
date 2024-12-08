<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;
class RegistrationMailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $user;
    public $password;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($_user,$_password)
    {
        $this->user = $_user;
        $this->password = $_password;

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
        //send mail
        $mail=(new MailMessage)
                ->subject('Account Creation')
                ->line('Your account has been successfully created on '.env('APP_NAME').'.')
                ->line("Please login with the below details")
                ->line(new HtmlString('<b>URL:</b> '.env('APP_URL')))
                ->line(new HtmlString('<b>Username/Email:</b> '.$this->user->email))
                ->line(new HtmlString('<b>Password:</b> '.$this->password))
                ->action('Login', env('APP_URL'));

                $mail->line('System powered by '.env('APP_NAME').'.');

        return  $mail;

    }

}
