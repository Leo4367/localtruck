<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Twilio\Rest\Client;

class AppointmentNotification extends Notification
{
    use Queueable;


    protected $send;
    /**
     * Create a new notification instance.
     */
    public function __construct($send)
    {
        $this->send = $send;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        //return ['mail', 'sms'];
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Appointment Notice')
            ->line('A new user driver has completed the reservation：')
            ->action('Log in to the background to view details', url('/admin'))
            ->line('Appointment Time：' . $this->send->time_slot)
            ->line('Name：' . $this->send->driver_name)
            ->line('Phone Number：' . $this->send->phone_number)
            ->line('Appt Number：' . $this->send->appt_number)
            ->line('Warehouse：' . $this->send->warehouse->name)
            ->line('Type：' . $this->send->type)
            ->line('Thanks！');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [

        ];
    }

    public function toSms($notifiable)
    {
        $phone = $notifiable->phone_number;
        $message = "您的预约已成功，您的账户已自动注册。您的密码是：12345678";
        $this->sendSms($phone, $message);
    }

    /**
     * 发送短信的实际逻辑
     */
    protected function sendSms($to, $message)
    {
        // 使用 Twilio SDK 发送短信
        $client = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));

        $client->messages->create(
            $to, // 接收者手机号
            [
                'from' => env('TWILIO_PHONE_NUMBER'), // Twilio 的号码
                'body' => $message, // 短信内容
            ]
        );
    }
}
