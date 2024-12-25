<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Http\Requests\ReservationRequest;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ReservationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['store', 'update']);
    }

    //予約完了ページ表示
    public function index()
    {
        return view('booked');
    }

    //予約情報を取得
    public function store(ReservationRequest $request)
    {
        $reservation = Reservation::create([
            'restaurant_id' => $request->restaurant_id,
            'user_id' => auth()->id(),
            'date' => $request->date,
            'time' => $request->time,
            'guests' => $request->guests,
        ]);

        try {
            $qrData = json_encode([
                'reservation_id' => $reservation->id,
                'user_id' => $reservation->user_id,
                'restaurant_id' => $reservation->restaurant_id,
                'date' => $reservation->date,
            ]);

            $qrCodePath = "qrcodes/{$reservation->id}.png";

            QrCode::format('png')
                ->size(300)
                ->generate($qrData, storage_path("app/public/{$qrCodePath}"));

            $reservation->update(['qr_code' => $qrCodePath]);
        } catch (\Exception $e) {
            return redirect()->route('booked')->with('error', 'QRコードの生成に失敗しました');
        }

        return redirect()->route('booked')->with('message', '予約が完了しました');
    }

    //QRコードを表示
    public function showQrCode($id)
    {
        $reservation = Reservation::findOrFail($id);
        return view('qr', compact('reservation'));
    }

    //予約変更
    public function update(ReservationRequest $request, $reservationId)
    {
        $reservation = Reservation::findOrFail($reservationId);
        $reservation->update([
            'date' => $request->date,
            'time' => $request->time,
            'guests' => $request->guests,
        ]);

        return redirect()->route('mypage')->with('message', '予約を変更しました');
    }
}