<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Image;

class ImageController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'restaurant_id' => 'required|exists:restaurants,id',
        ], [
            'image.required' => '画像ファイルを選択してください',
            'image.mimes' => '画像はjpeg, png, jpg, gif形式のみ許可されています',
            'image.max' => '画像サイズは2MB以下にしてください',
            'restaurant_id.required' => 'お店のIDは必須です',
        ]);

        //画像ファイルを取得
        $image = $request->file('image');

        //オリジナルのファイル名を取得
        $originalName = $image->getClientOriginalName();

        //日付を含むファイル名を生成
        $dateFileName = date('Ymd-His') . '_' . $originalName;

        //画像を保存し、パスを取得
        $path = $image->storeAs('storage/img', $dateFileName, 'public');

        //データベースにパスを保存
        $image = Image::create([
            'restaurant_id' => $validatedData['restaurant_id'],
            'path' => $path,
        ]);

        return redirect()->back()->with('success', '画像をアップロードしました');
    }
}
