<?php

namespace App\Services;


use App\Models\Service;

class ServicesService
{
    public function getServices()
    {
        return Service::active()->orderBy('order')
            ->get();
    }

    public function getServicesForSelect()
    {
        return Service::active()
            ->orderBy('order')
            ->pluck('name', 'id');
    }
}
