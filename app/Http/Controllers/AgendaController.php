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
        private NotificationService $notificationService,
    )
    {}

    public function index()
    {
        $services = Service::pluck('name', 'id');

        $user = auth()->user();
        if ($user && $user->isRestrictedToOwnAgenda()) {
            $professionals = Professional::where('id', $user->professional_id)->pluck('name', 'id');
        } else {
            $professionals = Professional::pluck('name', 'id');
        }

        $clients = Client::pluck('name', 'id');

        return view('admin.agenda.index', compact('services', 'professionals', 'clients'));
    }


    public function form()
    {
        $agenda = new Agenda();
        $services = Service::pluck('name', 'id');;
        $professionals = Professional::all();

        return view('admin.agenda.form', [
            'agenda' => $agenda,
            'services' => $services,
            'professionals' => $professionals,
        ]);
    }
    public function list()
    {
        $events = Agenda::with(['service', 'professional'])
            ->visibleTo(auth()->user())
            ->upcoming()
            ->get();

        return view('admin.agenda.list', [
            'events' => $events,
        ]);
    }
    public function getEvents(Request $request)
    {

        $startParam = $request->query('start'); // ISO string
        $endParam = $request->query('end');   // ISO string

        // Fallbacks just in case (but the view sends both)
        $start = $startParam ? Carbon::parse($startParam)->format('Y-m-d') : now()->startOfWeek()->format('Y-m-d');
        $end = $endParam ? Carbon::parse($endParam)->format('Y-m-d') : now()->endOfWeek()->format('Y-m-d');

        $query = Agenda::with(['client', 'professional', 'service'])
            ->whereBetween('day', [$start, $end])
            ->visibleTo(auth()->user());

        $user = auth()->user();

        if (! ($user && $user->isRestrictedToOwnAgenda()) && $request->filled('professional_id')) {
            $query->where('professional_id', $request->query('professional_id'));
        }

        $events = $query->get()->map(function ($item) {
            $start = Carbon::parse($item->day)
                ->setTimeFromTimeString($item->start_hour);
            $end = Carbon::parse($item->day)
                ->setTimeFromTimeString($item->end_hour);

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

    public function cancelEvent($eventId)
    {
        $cancel = Agenda::find($eventId);
        $cancel->delete();

        $deleteNotification = $this->notificationService->deleteNotification(tenant('id'), $eventId);

        return response()->json(['success' => true], 200);
    }
}
