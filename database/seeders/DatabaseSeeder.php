<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Professional;
use App\Models\Service;
use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create();

        Client::factory()->create([
            'name' => 'John Doe',
            'phone_1' => '910000000',
            'email' => 'email' . now() . '@email.pt']);

        Professional::factory()->create([
            'name' => 'Test Professional',
            'phone_1' => '1234567890',
            'phone_2' => '0987654321',
            'email' => 'email' . now() . '@email.pt']);

        Service::factory()->create([
            'name' => 'Cabelo e Barba',
            'duration' => 30,
            'price' => 17.50,
            'status' => 1
        ]);
    }
}
