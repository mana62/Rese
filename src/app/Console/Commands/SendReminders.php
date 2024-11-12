<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use App\Notifications\ReminderEmail;
use Illuminate\Console\Command;


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
        $this->info('送信中: ' . $reservation->user->email);
        $reservation->user->notify(new ReminderEmail($reservation));
    }

    $this->info('リマインダーを送信しました');
}
}
