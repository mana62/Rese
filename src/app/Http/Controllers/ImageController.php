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
        $restaurantId = $request->input('restaurantId');
        $restaurant = Restaurant::findOrFail($restaurantId);

        $imagePath = public_path('img/' . $restaurant->image);
        if (!file_exists($imagePath)) {
            return redirect()->back()->withErrors(['message' => '画像が見つかりません: ' . $imagePath]);
        }

        $originalName = pathinfo($imagePath, PATHINFO_BASENAME);
        $fileName = now()->format('Ymd_His') . '_' . $originalName;
        $storagePath = 'restaurants/' . $fileName;

        Storage::disk('public')->put($storagePath, file_get_contents($imagePath));

        Image::create([
            'restaurant_id' => $restaurant->id,
            'path' => $storagePath,
        ]);

        return redirect()->back()->with('message', '画像を保存しました');
    }
}