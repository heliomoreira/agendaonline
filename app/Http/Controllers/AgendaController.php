<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Professional;
use App\Models\Service;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    public function index()
    {
        $services = Service::pluck('name', 'id');
        $professionals = Professional::pluck('name', 'id');
        $clients = Client::pluck('name', 'id');
        return view('modules.agenda.index',
            [
                'services' => $services,
                'professionals' => $professionals,
                'clients' => $clients,
            ]);
    }
}
