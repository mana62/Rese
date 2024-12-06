<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Restaurant;
use App\Models\Image;

class ImageController extends Controller
{
    public function saveImage(Request $request)
    {
        //①リクエストからレストランIDを取得
        $restaurantId = $request->input('restaurantId');

        //②レストランを取得
        $restaurant = Restaurant::findOrFail($restaurantId);

        //③取得元パスをpublic/imgから取得
        $imagePath = public_path('img/' . $restaurant->image);
        if (!file_exists($imagePath)) {
            return redirect()->back()->withErrors(['message' => '画像が見つかりません: ' . $imagePath]);
        }

        //④保存先のパスを定義
        $originalName = pathinfo($imagePath, PATHINFO_BASENAME);
        $fileName = now()->format('Ymd_His') . '_' . $originalName;
        $storagePath = 'restaurants/' . $fileName;

        //⑤画像をストレージに保存
        Storage::disk('public')->put($storagePath, file_get_contents($imagePath));

        //⑥DBにレコードを作成
        Image::create([
            'restaurant_id' => $restaurant->id,
            'path' => $storagePath,
        ]);

        //⑦成功メッセージを表示
        return redirect()->back()->with('message', '画像を保存しました');
    }
}