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

        if ($input) {
            $query->where('name', 'like', "%$input%");
        }

        if ($areaId) {
            $query->where('area_id', $areaId);
        }

        if ($genreId) {
            $query->where('genre_id', $genreId);
        }

        //検索結果を取得
        $restaurants = $query->with(['area', 'genre'])->get();

        //エリアとジャンルを取得（検索フォーム用）
        $areas = Area::all();
        $genres = Genre::all();

        //お気に入り情報
        $favoriteIds = auth()->user() ? auth()->user()->favorites->pluck('id')->toArray() : [];

        //Ajax リクエストの場合は部分的なビューを返す
        if ($request->ajax()) {
            return view('partials.restaurants', compact('restaurants'))->render();
        }

        //通常のリクエストの場合は全体のビューを返す
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
