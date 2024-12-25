<?php

namespace Database\Factories;

use App\Models\Image;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ImageFactory extends Factory
{
    protected $model = Image::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'restaurant_id' => Restaurant::inRandomOrder()->first()?->id ?? Restaurant::factory()->create()->id,
            'path' => $this->faker->imageUrl(640, 480, 'food', true, 'dummy_image'),
        ];
    }
}
