<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Reservation;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\Request;
use App\Models\Checkout;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    //会員登録処理
    public function store(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('thanks');
    }

    public function index()
    {
        return view('thanks');
    }

    public function mypage()
    {
        $user = auth()->user();

        $reservations = Reservation::where('user_id', $user->id)
            ->whereDate('date', '>=', now()->toDateString())
            ->with('restaurant')
            ->orderBy('date', 'asc')
            ->get();

        $checkouts = Checkout::whereIn('reservation_id', $reservations->pluck('id'))
            ->get()
            ->keyBy('reservation_id');

        $favorites = $user->favorites()->with('area', 'genre')->get();
        $favoriteIds = $favorites->pluck('restaurant_id')->toArray();

        return view('mypage', compact('user', 'reservations', 'favorites', 'favoriteIds', 'checkouts'));
    }

    //お気に入り・削除
    public function toggleFavorite(Request $request, $restaurantId)
    {
        $user = auth()->user();
        $user->favorites()->toggle($restaurantId);

        $status = $user->favorites()->where('restaurant_id', $restaurantId)->exists() ? 'added' : 'removed';
        return response()->json(['status' => $status]);
    }

    //予約キャンセル
    public function cancelReservation($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->update(['status' => Reservation::STATUS_CANCELED]);
        $reservation->delete();

        return response()->json(['message' => '予約をキャンセルしました']);
    }
}
