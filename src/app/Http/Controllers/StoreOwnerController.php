<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Area;
use App\Models\Genre;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StoreOwnerController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $restaurant = auth()->user()->restaurant;
        $reservations = $restaurant ? $restaurant->reservations : collect();



        if (!$restaurant || !$restaurant->id) {
            $reservations = collect();
        } else {
            $reservations = Reservation::where('restaurant_id', $restaurant->id)->get();
        }

        $areas = Area::all();
        $genres = Genre::all();

        return view('store-owner', compact('reservations', 'restaurant', 'areas', 'genres'));
    }

    //店舗情報を新規作成
    public function createStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'area_id' => 'required|exists:areas,id',
            'genre_id' => 'required|exists:genres,id',
            'description' => 'required|string|min:10',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'name.required' => 'お名前を入力してください',
            'address.required' => '住所を入力してください',
            'address.string' => '正しい形式で入力してください',
            'area_id.required' => 'エリアを選択してください',
            'genre_id.required' => 'ジャンルを選択してください',
            'description.required' => '説明文を入力してください',
            'description.min' => '説明文は10文字以上で入力してください',
        ]);


        $user = auth()->user();
        $imagePath = $request->file('image') ? $request->file('image')->store('restaurants', 'public') : null;

        Restaurant::create([
            'name' => $request->name,
            'address' => $request->address,
            'area_id' => $request->area_id,
            'genre_id' => $request->genre_id,
            'description' => $request->description,
            'image' => $imagePath,
            'owner_id' => $user->id,
        ]);

        return redirect()->route('store-owner')->with('message', '店舗情報が作成されました');
    }

    //情報を更新
    public function updateStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'area_id' => 'required|exists:areas,id',
            'genre_id' => 'required|exists:genres,id',
            'description' => 'required|string|min:10',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'name.required' => 'お名前を入力してください',
            'address.required' => '住所を入力してください',
            'address.string' => '正しい形式で入力してください',
            'area_id.required' => 'エリアを選択してください',
            'genre_id.required' => 'ジャンルを選択してください',
            'description.required' => '説明文を入力してください',
            'description.min' => '説明文は10文字以上で入力してください',
        ]);

        $restaurant = auth()->user()->restaurant;

        if (!$restaurant) {
            return redirect()->route('store-owner')->withErrors(['error' => '店舗情報が見つかりません']);
        }

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('restaurants', 'public');
            $restaurant->update(['image' => $imagePath]);
        }

        $restaurant->update($request->only(['name', 'address', 'area_id', 'genre_id', 'description']));

        return redirect()->route('store-owner')->with('message', '店舗情報が更新されました');
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

