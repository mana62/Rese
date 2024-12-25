<?php

namespace Database\Factories;

use App\Models\Checkout;
use App\Models\User;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

class CheckoutFactory extends Factory
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
            'reservation_id' => Reservation::factory(),
            'payment_intent_id' => uniqid('pi_'),
            'amount' => $this->faker->numberBetween(1000, 10000),
            'status' => $this->faker->randomElement(['pending', 'success', 'failed']),
            'currency' => 'jpy',
        ];
    }
}
