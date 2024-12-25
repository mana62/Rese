<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Restaurant;

class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'restaurant_id' => Restaurant::inRandomOrder()->first()?->id ?? Restaurant::factory()->create()->id,
            'user_id' => User::factory(),
            'date' => $this->faker->dateTimeBetween('now', '+3 month')->format('Y-m-d'),
            'time' => $this->faker->time('H:i'),
            'guests' => $this->faker->numberBetween(1, 10),
            'qr_code' => null,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
