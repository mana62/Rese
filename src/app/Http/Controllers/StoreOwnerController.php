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
        $userId = auth()->id();
        $restaurants = Restaurant::where('owner_id', $userId)->get();

        //現在の最初の店舗（1つだけ選択）
        $restaurant = $restaurants->first();

        //予約情報を取得
        $reservations = $restaurant
            ? $restaurant->reservations()->with('user')->paginate(10)
            : collect();

        //エリア・ジャンルの取得
        $areas = Area::all();
        $genres = Genre::all();

        return view('owner', compact('restaurant', 'reservations', 'restaurants', 'areas', 'genres'));
    }

    //店舗情報を新規作成
    public function createStore(StoreOwnerRequest $request)
    {
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

            //保存パスを記録
            $imagePath = '' . $imageName; //データベース用パス
        }

        //店舗情報を作成
        $restaurant = Restaurant::create([
            'name' => $request->name,
            'address' => $request->address,
            'area_id' => $request->area_id,
            'genre_id' => $request->genre_id,
            'description' => $request->description,
            'image' => $imagePath,
            'owner_id' => auth()->id(),
        ]);

        return redirect()->route('owner')->with('message', '店舗情報が作成されました');
    }

    // 情報を更新
    public function updateStore(StoreOwnerRequest $request)
    {
        $restaurant = auth()->user()->restaurant;

        if (!$restaurant) {
            return redirect()->route('owner')->withErrors(['message' => '店舗情報が見つかりません']);
        }

        //画像処理
        $validated = $request->all();
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = date('Ymd_His') . '_' . $image->getClientOriginalName();
            $image->move(public_path('img'), $imageName);
            $validated['image'] = 'img/' . $imageName;
        }

        //情報を更新
        $restaurant->update($validated);

        return redirect()->route('owner')->with('message', '店舗情報が更新されました');
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
        $reservations = $restaurant
            ? $restaurant->reservations()->with('user')->paginate(10)
            : collect();

        //エリア・ジャンルを取得
        $areas = Area::all();
        $genres = Genre::all();

        return view('owner', compact('restaurant', 'reservations', 'areas', 'genres'));
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

