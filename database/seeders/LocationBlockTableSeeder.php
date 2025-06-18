<?php

namespace Database\Seeders;

use App\Models\LocationBlock;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationBlockTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LocationBlock::create([
            'name' => 'Almoço',
            'start_hour' => '12:30:00',
            'end_hour' => '14:00:00',
            'days_of_week' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']
        ]);
    }
}
