<?php

namespace Database\Seeders;

use App\Models\WorkingHour;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkingHoursTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $data = [];
        foreach ([1, 2, 3] as $professionalId) {
            foreach (range(0, 4) as $weekday) {
                $data[] = [
                    'professional_id' => $professionalId,
                    'weekday' => $weekday,
                    'start_hour' => '09:00',
                    'end_hour' => '17:00',
                ];
            }
        }

        foreach ($data as $entry) {
            WorkingHour::create($entry);
        }
    }
}
