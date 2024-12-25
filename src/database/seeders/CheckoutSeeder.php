<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Checkout;

class CheckoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Checkout::factory()->count(20)->create();
    }
}
