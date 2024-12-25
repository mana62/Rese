<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Favorite;
use App\Models\User;
use App\Models\Restaurant;

class FavoriteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();
        $restaurants = Restaurant::all();

        foreach ($users as $user) {
            $favoriteRestaurants = $restaurants->random(rand(1, 5));
            foreach ($favoriteRestaurants as $restaurant) {
                Favorite::create([
                    'user_id' => $user->id,
                    'restaurant_id' => $restaurant->id,
                ]);
            }
        }
    }
}
