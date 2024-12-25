<?php

namespace Database\Factories;

use App\Models\Favorite;
use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;
class FavoriteFactory extends Factory
{
    protected $model = Favorite::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'restaurant_id' => Restaurant::inRandomOrder()->first()?->id ?? Restaurant::factory()->create()->id,
        ];
    }
}
