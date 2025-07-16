<?php

namespace App\Services;

use App\Models\Professional;

class ProfessionalService
{
    public static function list()
    {
        return Professional::where('status', 1)
            ->orderBy('name', 'asc')
            ->get();
    }

}
