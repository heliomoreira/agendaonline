<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Client;
use App\Models\Notification;
use App\Models\PaymentStatus;
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
    {
    }

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

    public function list(Request $request)
    {
        $services = Service::orderBy('name')->get();
        $professionals = Professional::orderBy('name')->get();

        $events = Agenda::with(['service', 'professional'])
            ->visibleTo(auth()->user())
            ->upcoming()
            ->when($request->filled('service_id'),
                fn($q) => $q->where('service_id', $request->service_id))
            ->when($request->filled('professional_id'),
                fn($q) => $q->where('professional_id', $request->professional_id))
            ->when($request->filled('date_from'),
                fn($q) => $q->whereDate('day', '>=', $request->date_from))
            ->when($request->filled('date_to'),
                fn($q) => $q->whereDate('day', '<=', $request->date_to))
            ->paginate(15)
            ->withQueryString();

        return view('admin.agenda.list', [
            'events' => $events,
            'services' => $services,
            'professionals' => $professionals,
        ]);
    }

    public function show(Agenda $agenda)
    {
        $this->authorizeAgenda($agenda);

        $agenda->load(['client', 'service', 'professional', 'paymentStatus']);

        return view('admin.agenda.show', [
            'agenda' => $agenda,
            'paymentStatuses' => PaymentStatus::ordered()->get(),
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

        if (!($user && $user->isRestrictedToOwnAgenda()) && $request->filled('professional_id')) {
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

    public function update(Request $request, $id)
    {
        $agenda = Agenda::findOrFail($id);

        $this->authorizeAgenda($agenda);

        $data = $request->validate([
            'payment_status_id' => ['required', 'exists:payment_statuses,id'],
            'paid' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $status = PaymentStatus::findOrFail($data['payment_status_id']);

        $paid = $request->filled('paid')
            ? $data['paid']
            : ($status->is_paid ? ($agenda->paid ?? now()) : null);

        $agenda->update([
            'payment_status_id' => $status->id,
            'paid' => $status->is_paid,
            'paid_at' => $paid,
            'notes' => $data['notes'] ?? null,
        ]);

        return back()->with('success', 'Marcação atualizada.');
    }

    private function authorizeAgenda(Agenda $agenda): void
    {
        $user = auth()->user();
        abort_if(
            $user && $user->isRestrictedToOwnAgenda() && $agenda->professional_id !== $user->professional_id,
            403
        );
    }
}
