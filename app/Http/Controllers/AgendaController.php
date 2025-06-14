<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Client;
use App\Models\Notification;
use App\Models\Professional;
use App\Models\Service;
use App\Services\NotificationService;
use Carbon\Carbon;
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

    public function store(Request $request)
    {
        Agenda::create($request->all());

        $service = Service::find($request->service_id);
        $serviceName = $service ? $service->name : 'o seu serviço';

        $text = "Olá, lembramos que tem o serviço {$serviceName} agendado para amanhã às {$request->start_hour}. Em caso de dúvida ou alteração, contacte-nos. Obrigado.";

        NotificationService::saveNotification(
            'AGENDA',
            '919781176',
            'sms',
            $text,
            $request->day,
            $request->start_hour,
            $request->end_hour
        );


        return redirect()->route('agenda.index')->with('success', 'Appointment created successfully.');
    }

    public function getEvents()
    {
        $events = Agenda::with(['client', 'professional', 'service'])
            ->get()
            ->map(function ($item) {
                $start = Carbon::parse("{$item->day} {$item->start_hour}")->toIso8601String();
                $end = Carbon::parse("{$item->day} {$item->end_hour}")->toIso8601String();

                return [
                    'id' => $item->id,
                    'title' => $item->service->name ?? 'Serviço',
                    'start' => $start,
                    'end' => $end,
                    'category' => $item->professional->id,
                    'color' => $item->professional->agenda_color ?? '#2F87EB',
                    'extendedProps' => [
                        'client' => $item->client->name ?? '',
                        'professional' => $item->professional->name ?? '',
                        'notes' => $item->notes ?? ''
                    ]
                ];
            });


        return response()->json($events);
    }
}
