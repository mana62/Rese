<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Area;
use App\Models\Genre;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        //検索クエリの取得
        $input = $request->input('input', '');
        $areaId = $request->input('area', '');
        $genreId = $request->input('genre', '');

        //エリアとジャンルの取得
        $areas = Area::all();
        $genres = Genre::all();

        //レストランの取得
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
        $restaurants = $query->with(['area', 'genre'])->get();

        $favoriteIds = auth()->check() ? auth()->user()->favorites->pluck('restaurant_id')->toArray() : [];

        return view('restaurants', compact('restaurants', 'input', 'areaId', 'genreId', 'areas', 'genres', 'favoriteIds'));
    }

    public function show($id)
    {
        $restaurant = Restaurant::with(['area', 'genre', 'reservations'])->findOrFail($id);
        return view('shop-detail', compact('restaurant'));
    }
}
