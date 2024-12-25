<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Image;
use App\Models\Restaurant;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Restaurant::all()->each(function ($restaurant) {
            Image::factory()->count(3)->create([
                'restaurant_id' => $restaurant->id,
            ]);
        });
}
}
