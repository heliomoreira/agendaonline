<?php

namespace Database\Seeders;

use App\Models\LocationClosure;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationClosuresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $startDate = Carbon::today();
        for ($i = 0; $i <= 30; $i += 10) {
            LocationClosure::create([
                'day' => $startDate->copy()->addDays($i)->toDateString(),
                'reason' => 'National Holiday'
            ]);
        }
    }
}
