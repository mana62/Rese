<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
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
            'rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->sentence(10),
            'updated_at' => now(),
        ];
    }
}
