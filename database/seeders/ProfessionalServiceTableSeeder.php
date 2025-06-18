<?php

namespace Database\Seeders;

use App\Models\Professional;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProfessionalServiceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $haircutId = 1;

        $associations = [
            1 => [$haircutId], // Alice Gomes
            2 => [$haircutId], // Bruno Costa
            3 => [$haircutId], // Carla Silva
        ];

        foreach ($associations as $professionalId => $serviceIds) {
            $professional = Professional::find($professionalId);
            if ($professional) {
                $professional->services()->sync($serviceIds);
            }
        }
    }
}
