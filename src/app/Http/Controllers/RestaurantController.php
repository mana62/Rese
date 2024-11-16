<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ImageRequest;
use App\Models\Restaurant;
use App\Models\Area;
use App\Models\Genre;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        //検索クエリの取得
        $input = $request->input('input', '');
        $areaId = $request->input('area');
        $genreId = $request->input('genre');

        //クエリ
        $query = Restaurant::query();

        if ($input = $request->input('input')) {
            $query->where('name', 'like', "%$input%");
        }

        if ($areaId = $request->input('area')) {
            $query->where('area_id', $areaId);
        }

        if ($genreId = $request->input('genre')) {
            $query->where('genre_id', $genreId);
        }

        $restaurants = $query->with(['area', 'genre'])->get();


        //エリアとジャンルを取得（検索フォーム用）
        $areas = Area::all();
        $genres = Genre::all();

        //お気に入り情報
        $restaurants = Restaurant::with(['area', 'genre'])->get();

        $favoriteIds = auth()->check()
            ? auth()->user()->favorites->pluck('restaurant_id')->toArray()
            : []; //ログインしていない場合は空配列

        if ($request->ajax()) {
            return view('partials.restaurants', compact('restaurants'))->render();
        }

        return view('restaurants', compact('restaurants', 'input', 'areaId', 'genreId', 'areas', 'genres', 'favoriteIds'));
    }

    public function show($id)
    {
        $restaurant = Restaurant::with(['area', 'genre', 'reservations'])->findOrFail($id);
        return view('detail', compact('restaurant'));
    }

    public function uploadImage(ImageRequest $request, $id)
    {
        //レストランを取得
        $restaurant = Restaurant::findOrFail($id);

        //画像を保存
        $image = $request->file('image');
        $fileName = now()->format('Ymd_His') . '_' . $image->getClientOriginalName();
        $path = $image->storeAs('restaurants', $fileName, 'public');

        //imageカラムを更新
        $restaurant->image = $path;
        $restaurant->save();

        return redirect()->back()->with('message', '画像をアップロードしました');
    }

}
