<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Client;
use App\Models\Notification;
use App\Models\Professional;
use App\Models\Service;
use App\Services\BookingService;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    public function __construct(
        //private BookingService $bookingService,
    )
    {
    }

    public function index()
    {
        $services = Service::pluck('name', 'id');
        $professionals = Professional::pluck('name', 'id');
        $clients = Client::pluck('name', 'id');
        return view('admin.agenda.index',
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


    public function form()
    {
        $agenda = new Agenda();
        $services = Service::all();
        $professionals = Professional::all();

        return view('admin.agenda.form', [
            'agenda' => $agenda,
            'services' => $services,
            'professionals' => $professionals,
        ]);
    }

    public function getEvents(Request $request)
    {
        $startParam = $request->query('start'); // ISO string
        $endParam   = $request->query('end');   // ISO string

        // Fallbacks just in case (but the view sends both)
        $start = $startParam ? Carbon::parse($startParam)->format('Y-m-d') : now()->startOfWeek()->format('Y-m-d');
        $end   = $endParam   ? Carbon::parse($endParam)->format('Y-m-d')   : now()->endOfWeek()->format('Y-m-d');

        $query = Agenda::with(['client', 'professional', 'service'])
            ->whereBetween('day', [$start, $end]);

        // Optional server-side filter by professional
        if ($request->filled('professional_id')) {
            $query->where('professional_id', $request->query('professional_id'));
        }

        $events = $query->get()->map(function ($item) {
            $start = Carbon::parse("{$item->day} {$item->start_hour}")->toIso8601String();
            $end   = Carbon::parse("{$item->day} {$item->end_hour}")->toIso8601String();

            return [
                'id' => $item->id,
                'title' => $item->service->name ?? 'Serviço',
                'start' => $start,
                'end' => $end,
                // keep this if you still want to filter client-side by professional id
                'category' => optional($item->professional)->id,
                'color' => optional($item->professional)->agenda_color ?? '#2F87EB',
                'extendedProps' => [
                    'client' => optional($item->client)->name ?? '',
                    'service' => optional($item->service)->name ?? '',
                    'professional' => optional($item->professional)->name ?? '',
                    'notes' => $item->notes ?? '',
                ],
            ];
        });

        return response()->json($events->values());
    }
}
