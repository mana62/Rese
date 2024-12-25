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
        $input = $request->input('input', '');
        $areaId = $request->input('area');
        $genreId = $request->input('genre');
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

        $areas = Area::all();
        $genres = Genre::all();

        $favoriteIds = auth()->user() ? auth()->user()->favorites->pluck('id')->toArray() : [];

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
        $restaurant = Restaurant::findOrFail($id);

        $image = $request->file('image');
        $fileName = now()->format('Ymd_His') . '_' . $image->getClientOriginalName();
        $path = $image->storeAs('restaurants', $fileName, 'public');

        $restaurant->image = $path;
        $restaurant->save();

        return redirect()->back()->with('message', '画像をアップロードしました');
    }

}
