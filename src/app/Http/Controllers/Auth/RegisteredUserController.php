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

        //Checkoutの情報を取得
        $checkouts = Checkout::whereIn('reservation_id', $reservations->pluck('id'))
            ->get()
            ->keyBy('reservation_id'); //reservation_id をキーにした配列を作成


        //ユーザーのお気に入りを取得
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

        //予約のステータスをcancelに変更
        $reservation->update(['status' => Reservation::STATUS_CANCELED]);
        $reservation->delete();

        return response()->json(['message' => '予約をキャンセルしました']);
    }
}
