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

class RegisteredUserController extends Controller
{
    //会員登録ページを表示
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

    //サンクスページを表示
    public function index()
    {
        return view('thanks');
    }

    //マイページ
    public function mypage()
    {
        $user = auth()->user();

        //現在日時以降の予約を取得(日付が過ぎたものは表示されない)
        $reservations = Reservation::where('user_id', $user->id)
            ->whereDate('date', '>=', now()->toDateString())
            ->with('restaurant')
            ->orderBy('date', 'asc')
            ->get();

        //ユーザーのお気に入りを取得
        $favorites = $user->favorites ?? collect();

        //お気に入りのレストランIDを取得
        $favoriteIds = $favorites->pluck('restaurant_id')->toArray();
        $favorites = $user->favorites()->with('area', 'genre')->get();

        return view('mypage', compact('user', 'reservations', 'favorites', 'favoriteIds'));
    }

    //お気に入り・削除
    public function toggleFavorite(Request $request, $restaurantId)
    {
        $user = auth()->user();
        $isFavorited = $user->favorites()->where('restaurant_id', $restaurantId)->exists();

        if ($isFavorited) {
            // 既にお気に入りなら削除
            $user->favorites()->detach($restaurantId);
            return response()->json(['status' => 'removed']);
        } else {
            // 新しくお気に入りに追加
            $user->favorites()->attach($restaurantId);
            return response()->json(['status' => 'added']);
        }
    }

    //予約キャンセル
    public function cancelReservation($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();

        return response()->json(['message' => '予約をキャンセルしました']);
    }
}
