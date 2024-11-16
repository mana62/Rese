<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Restaurant;
use App\Http\Requests\ImageRequest;

class ImageController extends Controller
{
    public function store(ImageRequest $request, $restaurantId)
    {
        //Restaurantの取得
        $restaurant = Restaurant::findOrFail($restaurantId);

        //画像ファイルの存在を確認
        if (!$request->hasFile('image')) {
            return redirect()->back()->withErrors('画像が選択されていません。');
        }

        $image = $request->file('image');

        //ファイル名の生成と変換
        $originalName = preg_replace('/\s+/', '_', $image->getClientOriginalName());
        $fileName = now()->format('Ymd_His') . '_' . $originalName;

        //画像の保存
        $path = $image->storeAs('restaurants', $fileName, 'public');

        //DBにレコードを作成
        Image::create([
            'restaurant_id' => $restaurant->id,
            'path' => $path,
        ]);
        return redirect()->back()->with('message', '画像をアップロードしました');
    }
}
