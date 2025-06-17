<?php

namespace Database\Seeders;

use App\Models\ProfessionalUnavailability;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProfessionalUnavailabilitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $baseDate = Carbon::today();

        $unavailabilities = [
            ['professional_id' => 1, 'day' => $baseDate->copy()->addDays(14)->toDateString(), 'reason' => 'Personal Time Off'],
            ['professional_id' => 2, 'day' => $baseDate->copy()->addDays(9)->toDateString(), 'reason' => 'Personal Time Off'],
            ['professional_id' => 3, 'day' => $baseDate->copy()->addDays(12)->toDateString(), 'reason' => 'Personal Time Off'],
        ];

        foreach ($unavailabilities as $item) {
            ProfessionalUnavailability::create($item);
        }
    }
}
