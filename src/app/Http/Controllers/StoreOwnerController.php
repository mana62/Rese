<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Area;
use App\Models\Genre;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests\StoreOwnerRequest;
use PhpParser\Node\Stmt\ElseIf_;

class StoreOwnerController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $restaurant = $user->restaurant;

        //予約情報を取得
        $reservations = $restaurant
            ? $restaurant->reservations()->with('user')->get()
            : collect();

        //エリア・ジャンルの取得
        $areas = Area::all();
        $genres = Genre::all();

        return view('owner', compact('reservations', 'restaurant', 'areas', 'genres'));
    }


    //店舗情報を新規作成
    public function createStore(StoreOwnerRequest $request)
    {
        $user = auth()->user();

        // 画像の保存
        $image = $request->file('image');
        $imageName = date('Y-m-d_His') . '_' . $image->getClientOriginalName();
        $imagePath = $image->storeAs('restaurants', $imageName, 'public');

        // 店舗情報の作成
        $restaurant = Restaurant::create([
            'name' => $request->name,
            'address' => $request->address,
            'area_id' => $request->area_id,
            'genre_id' => $request->genre_id,
            'description' => $request->description,
            'image' => $imagePath, // ここで保存
            'owner_id' => auth()->id(),
        ]);

        return redirect()->route('owner')->with('message', '店舗情報が作成されました');
    }

    //情報を更新
    public function updateStore(Request $request)
    {
        $restaurant = auth()->user()->restaurant;

        if (!$restaurant) {
            return redirect()->route('owner')->withErrors(['error' => '店舗情報が見つかりません']);
        }

        // 画像の保存
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = date('Y-m-d_His') . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('restaurants', $imageName, 'public');
            $restaurant->update(['image' => $imagePath]);
        }

        $restaurant->update($request->only(['name', 'address', 'area_id', 'genre_id', 'description']));

        return redirect()->route('owner')->with('message', '店舗情報が更新されました');
    }

    //QRコード
    public function verifyQrCode(Request $request)
    {
        $validated = $request->validate([
            'qr_data' => 'required',
        ]);

        $qrData = json_decode($validated['qr_data'], true);

        $reservation = Reservation::find($qrData['reservation_id']);

        //QRの予約情報
        if (!$reservation) {
            return response()->json(['error' => '無効なQRコードです'], 404);
        }

        //QRコードの有効期限
        if (!Carbon::parse($reservation->date)->isToday()) {
            return response()->json(['error' => 'QRコードの有効期限が切れています'], 400);
        }

        return response()->json([
            'success' => '予約が確認されました',
            'reservation' => $reservation,
        ]);
    }
}

