<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;
use App\Broadcasting\SMS;
use AfricasTalking\SDK\AfricasTalking;
class SmsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $user;

    public $message;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user,$message)
    {
        $this->user = $user;
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
        return  [SMS::class];
    }

    
    /**
     * Get the sms representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\sms
     */
    public function toSMS($notifiable)
    {
         $username   =  env("AT_API_USERNAME", "");
         $apiKey     =  env("AT_API_SECRET", "");
         $sender_id     =  env("AT_API_SENDER_ID", "");
         // Initialize the SDK
         $AT = new AfricasTalking($username, $apiKey );


        // Get the SMS service
        $sms        = $AT->sms();

        try{
            $result = $sms->send([
                'to'      => $this->user->phone,
                'message' => 'Dear '.$this->user->fname.', '.$this->message,
                'from'    => $sender_id
            ]);
            header("Content-Type: application/json; charset=UTF-8");
 
             return $result;
        }catch(\Exception $e){
           
           \Log::critical($e);
        }
        //return [];
          
    }
    
}
