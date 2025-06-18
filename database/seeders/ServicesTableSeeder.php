<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Service::updateOrCreate([
            'id' => 1
        ], [
            'name' => 'Corte de cabelo',
            'duration' => 30,
            'price' => 20.00,
            'status' => true
        ]);
    }
}
