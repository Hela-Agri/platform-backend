<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use \Illuminate\Support\Facades\Log;
use App\Broadcasting\FCM;
use Illuminate\Support\Facades\Http;
class FcmNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $serverKey;
    public $data;
    protected $target;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($target = array(), $data = array())
    {
        //
        $this->serverKey     = env("FCM_SERVER_KEY", "");
        $this->data = $data;
        $this->target = $target;
        
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [FCM::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toFCM($notifiable)
    {
        //
        try{
             $response =Http::withHeaders([
                'Authorization' => 'key=' .$this->serverKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->post('https://fcm.googleapis.com/fcm/send', [
                'data' => $this->data,
                'registration_ids' =>$this->target,
            ]);
           
            if($response->ok()){
                $fcm_response=json_decode($response->body());
               
            }else{
               
                Log::critical("Error occured"); 
            }

        } catch (\Exception $e) {
            Log::critical($e);
        }
    }

}
