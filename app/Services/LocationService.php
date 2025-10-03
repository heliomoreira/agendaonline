<?php

namespace App\Services;

use App\Models\Location;

class LocationService
{
    public function listLocations(){
        return Location::all();
    }
}
