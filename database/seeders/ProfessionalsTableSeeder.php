<?php

namespace Database\Seeders;

use App\Models\Professional;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProfessionalsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $professionals = [
            ['id' => 1, 'name' => 'Alice Gomes', 'status' => true],
            ['id' => 2, 'name' => 'Bruno Costa', 'status' => true],
            ['id' => 3, 'name' => 'Carla Silva', 'status' => true],
        ];

        foreach ($professionals as $professional) {
            Professional::updateOrCreate(['id' => $professional['id']], $professional);
        }
    }
}
