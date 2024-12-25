<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Area;
use App\Models\Genre;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests\StoreOwnerRequest;
use Illuminate\Support\Facades\Auth;


class StoreOwnerController extends Controller
{
    public function showLoginForm()
    {
        return view('owner.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt(array_merge($credentials, ['role' => 'store-owner']))) {
            return redirect()->route('owner.dashboard');
        }

        return back()->withErrors([
            'email' => 'メールアドレスまたはパスワードが間違っています',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('owner.login');
    }

    //ownerページ表示
    public function index(Request $request)
    {

        $userId = auth()->id();
        $restaurants = Restaurant::where('owner_id', $userId)->get();

        $restaurant = null;
        if ($request->has('search')) {
            $searchQuery = $request->input('search');
            $restaurant = Restaurant::where('owner_id', $userId)
                ->where('name', 'LIKE', '%' . $searchQuery . '%')
                ->first();
        }

        if (!$restaurant) {
            $restaurant = $restaurants->first();
        }

        $reservations = $restaurant
            ? $restaurant->reservations()->with('user')->paginate(10)
            : collect();

        $areas = Area::all();
        $genres = Genre::all();

        return view('owner.dashboard', compact('restaurant', 'reservations', 'restaurants', 'areas', 'genres'));
    }

    //店舗情報を新規作成
    public function createStore(StoreOwnerRequest $request)
    {
        $imagePath = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');

            if (!$image->isValid()) {
                return redirect()->back()->withErrors(['image' => '画像が無効です']);
            }

            $imageName = date('Ymd_His') . '_' . $image->getClientOriginalName();
            $image->move(public_path('img'), $imageName);
            $imagePath = $imageName;
        }

        $restaurant = Restaurant::create([
            'name' => $request->name,
            'address' => $request->address,
            'area_id' => $request->area_id,
            'genre_id' => $request->genre_id,
            'description' => $request->description,
            'image' => $imagePath,
            'owner_id' => auth()->id(),
        ]);

        return redirect()->route('owner.dashboard')->with('message', '店舗情報が作成されました');
    }

    //情報を更新
    public function updateStore(StoreOwnerRequest $request)
    {
        $restaurantId = $request->input('restaurant_id');
        $restaurant = Restaurant::where('id', $restaurantId)
            ->where('owner_id', auth()->id())
            ->first();

        if (!$restaurant) {
            return redirect()->route(
                'store_owner'
            )->with(['message' => '店舗情報が見つかりません']);
        }

        $validated = $request->all();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = date('Ymd_His') . '_' . $image->getClientOriginalName();
            $image->move(public_path('img'), $imageName);
            $validated['image'] = $imageName;
        } else {
            unset($validated['image']);
        }

        $restaurant->update($validated);

        return redirect()->route('owner.dashboard')->with('message', '店舗情報が更新されました');
    }


    //検索部分
    public function searchStore(Request $request)
    {
        $userId = auth()->id();
        $searchQuery = $request->input('search');
        $restaurant = Restaurant::where('owner_id', $userId)
            ->where('name', 'LIKE', '%' . $searchQuery . '%')
            ->first();

        $reservations = collect();

        if ($restaurant) {
            $reservations = $restaurant
                ->reservations()
                ->with('user')
                ->orderBy('date', 'desc')
                ->with('time')
                ->paginate(10);
        }

        $areas = Area::all();
        $genres = Genre::all();

        return view('owner.dashboard', compact('restaurant', 'reservations', 'areas', 'genres'));
    }


    //QRコード
    public function verifyQrCode(Request $request)
    {
        $validated = $request->validate([
            'qr_data' => 'required',
        ]);

        $qrData = json_decode($validated['qr_data'], true);

        $reservation = Reservation::find($qrData['reservation_id']);

        if (!$reservation) {
            return response()->json(['error' => '無効なQRコードです'], 404);
        }

        if (!Carbon::parse($reservation->date)->isToday()) {
            return response()->json(['error' => 'QRコードの有効期限が切れています'], 400);
        }

        return response()->json([
            'success' => '予約が確認されました',
            'reservation' => $reservation,
        ]);
    }
}