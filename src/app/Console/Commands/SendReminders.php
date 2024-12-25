<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use App\Models\User;
use App\Notifications\ReminderEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:reminders';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '予約当日のリマインダーを送信します';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $reservations = Reservation::whereDate('date', today())->get();
        $this->info('今日の予約数: ' . $reservations->count());

        foreach ($reservations as $reservation) {

            if (!$reservation->user) {
                $this->error('ユーザー情報が見つかりません: 予約ID ' . $reservation->id);
                continue;
            }
            $this->info('送信中: ' . $reservation->user->email);
            try {
                $reservation->user->notify(new ReminderEmail($reservation));
            } catch (\Exception $e) {
                Log::error('ユーザーへメール送信中にエラーが発生しました', [
                    'error_message' => $e->getMessage(),
                    'user_email' => $reservation->user->email,
                ]);
            }

            $ownerId = $reservation->restaurant?->owner_id;
            $storeOwner = $ownerId ? User::find($ownerId) : null;

            if ($storeOwner) {
                $this->info('送信中: ' . $storeOwner->email);
                try {
                    $storeOwner->notify(new ReminderEmail($reservation));
                } catch (\Exception $e) {
                    Log::error('店舗オーナーへメール送信中にエラーが発生しました', [
                        'error_message' => $e->getMessage(),
                        'owner_email' => $storeOwner->email,
                        'reservation_id' => $reservation->id,
                    ]);
                }
            } else {
                $this->error('店舗オーナーが見つかりません: 予約ID ' . $reservation->id);
            }
        }
    }
}