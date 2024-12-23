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

    //ログイン処理
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        //ログイン認証
        if (Auth::attempt(array_merge($credentials, ['role' => 'store-owner']))) {
            return redirect()->route('owner.dashboard');
        }

        return back()->withErrors([
            'email' => 'メールアドレスまたはパスワードが間違っています',
        ]);
    }

    //ログアウト処理
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('owner.login');
    }

    public function index(Request $request)
    {

        //ユーザーとレストランからowner_idを取得
        $userId = auth()->id();
        $restaurants = Restaurant::where('owner_id', $userId)->get();

        //検索クエリがある場合は特定の店舗を取得
        $restaurant = null;
        if ($request->has('search')) {
            $searchQuery = $request->input('search');
            $restaurant = Restaurant::where('owner_id', $userId)
                ->where('name', 'LIKE', '%' . $searchQuery . '%')
                ->first();
        }

        //初期表示時に最初の店舗を選択
        if (!$restaurant) {
            $restaurant = $restaurants->first();
        }

        //ページネーションを設定
        $reservations = $restaurant
            ? $restaurant->reservations()->with('user')->paginate(10)
            : collect();

        //エリアとジャンルを取得
        $areas = Area::all();
        $genres = Genre::all();

        return view('owner.dashboard', compact('restaurant', 'reservations', 'restaurants', 'areas', 'genres'));
    }

    //店舗情報を新規作成
    public function createStore(StoreOwnerRequest $request)
    {

        //$imagePathをnullに
        $imagePath = null;

        //ファイルがアップロードされた場合の処理
        if ($request->hasFile('image')) {
            $image = $request->file('image');

            //画像ファイルの存在確認
            if (!$image->isValid()) {
                return redirect()->back()->withErrors(['image' => '画像が無効です']);
            }

            //ファイル名を生成
            $imageName = date('Ymd_His') . '_' . $image->getClientOriginalName();

            //ファイルをpublic/img に保存
            $image->move(public_path('img'), $imageName);

            //データベース用のパス
            $imagePath = $imageName;
        }

        //情報をデータベースに保存
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
        //restaurant_idを取得して$restaurantIdに代入
        $restaurantId = $request->input('restaurant_id');

        //店舗を取得
        $restaurant = Restaurant::where('id', $restaurantId)
            ->where('owner_id', auth()->id())
            ->first();

        //owner_idが見つからなければエラー
        if (!$restaurant) {
            return redirect()->route(
                'store_owner'
            )->with(['message' => '店舗情報が見つかりません']);
        }

        //更新データを取得
        $validated = $request->all();

        //画像があれば名前をつける
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = date('Ymd_His') . '_' . $image->getClientOriginalName();

            //ファイルをpublic/imgに保存
            $image->move(public_path('img'), $imageName);

            //データベース保存
            $validated['image'] = $imageName;
        } else {
            unset($validated['image']); //画像がない場合は更新しない
        }

        //店舗情報を更新
        $restaurant->update($validated);

        return redirect()->route('owner.dashboard')->with('message', '店舗情報が更新されました');
    }


    public function searchStore(Request $request)
    {
        $userId = auth()->id();

        //検索クエリを取得
        $searchQuery = $request->input('search');

        //現在の店舗を検索
        $restaurant = Restaurant::where('owner_id', $userId)
            ->where('name', 'LIKE', '%' . $searchQuery . '%')
            ->first();

        //該当店舗の予約情報を取得
        $reservations = collect();

        if ($restaurant) {
            $reservations = $restaurant
                ->reservations()
                ->with('user')
                ->orderBy('date', 'desc')
                ->with('time')
                ->paginate(10);
        }

        //エリア・ジャンルを取得
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