<?php

namespace App\Notifications;

use App\Broadcasting\SMS;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Infobip\ApiException;
use Infobip\Model\SmsAdvancedTextualRequest;
use Infobip\Model\SmsDestination;
use Infobip\Model\SmsTextualMessage;
use Infobip\Configuration;
use Infobip\Api\SmsApi;
use Infobip\Model\SmsLanguage;
class VerifyPhoneNoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The authenticated user.
     *
     * @var \Illuminate\Contracts\Auth\Authenticatable
     */
    public $user;
    public $code;

    /**
     * Create a new event instance.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return void
     */
    public function __construct($user,$code)
    {
        $this->user = $user;
        $this->code = $code;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [SMS::class];

    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toSMS($notifiable)
    {
        $configuration = new Configuration(
            env("INFOBIB_BASE_URL", ""),
            env("INFOBIB_API_KEY", "")
        );

        $sendSmsApi = new SmsApi($configuration);

        $message = new SmsTextualMessage(
            [new SmsDestination( $this->user->phone)],//$destinations,
            null,//$callbackData
            null, //new SmsDeliveryTimeWindow([])
            false,//$flash,
            env("APP_NAME", ""),//$from ,
            null,// $intermediateReport
            new SmsLanguage('EN'),//\Infobip\Model\SmsLanguage
            null,//$notifyContentType
            null,//$notifyUrl
            null,//$regional
            \Carbon\Carbon::now(),//$sendAt
            $this->code." is your ".env("APP_NAME", "")." verification code.",//$text
            null,//$transliteration
            null,//$validityPeriod
            null,//$entityId
            null,//$applicationId
        );

        $request = new SmsAdvancedTextualRequest([$message]);

        try {
            $sendSmsApi->sendSmsMessage($request);

        } catch (ApiException $apiException) {
            \Log::critical($apiException);

        }



        //return [];

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
