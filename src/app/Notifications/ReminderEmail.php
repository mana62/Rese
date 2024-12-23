<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;

class ReminderEmail extends Notification
{
    protected $reservation;
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
        $reservation = $this->reservation;

        //ログ
        Log::info('ReminderEmail デバッグ', [
            'reservation_id' => $reservation->id,
            'notifiable_email' => $notifiable->email,
        ]);

        //メール内容
        return (new MailMessage)
            ->line('ご予約日当日です。')
            ->line('日時: ' . $reservation->date ?? '不明')
            ->line('時間: ' . $reservation->time ?? '不明')
            ->line('店舗: ' . ($reservation->restaurant->name ?? '不明'))
            ->action('詳細を見る', url('/login'))
            ->line('ご利用ありがとうございます。');
    }
}