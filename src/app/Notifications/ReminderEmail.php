<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ReminderEmail extends Notification
{
    public function __construct($reservation)
    {
        $this->reservation = $reservation;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('本日は予約当日です。')
                    ->line('日時: ' . $this->reservation->date)
                    ->line('店舗: ' . $this->reservation->restaurant->name)
                    ->action('詳細を見る', url('/mypage'))
                    ->line('ご利用ありがとうございます。');
    }
}