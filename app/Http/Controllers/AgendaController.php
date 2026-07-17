<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Client;
use App\Models\Notification;
use App\Models\PaymentStatus;
use App\Models\Professional;
use App\Models\Service;
use App\Services\AgendaService;
use App\Services\BookingService;
use App\Services\NotificationService;
use App\Services\ServicesService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    public function __construct(
        private NotificationService $notificationService,
        private ServicesService $servicesService,
        private AgendaService $agendaService
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
        $services = $this->servicesService->getServicesForSelect();
        $professionals = Professional::all();
        $smsTemplates = \App\Models\Service::with('smsTemplate')->get()
            ->mapWithKeys(fn ($s) => [
                $s->id => ($s->smsTemplate && $s->smsTemplate->status && filled($s->smsTemplate->body))
                    ? $s->smsTemplate->body : null
            ])
            ->filter()
            ->toArray();

        return view('admin.agenda.form', [
            'agenda' => $agenda,
            'services' => $services,
            'professionals' => $professionals,
            'smsTemplates' => $smsTemplates,
        ]);
    }

    public function list(Request $request)
    {
        $services = Service::orderBy('name')->get();
        $professionals = Professional::orderBy('name')->get();

        $events = Agenda::with(['service', 'professional', 'paymentStatus'])
            ->visibleTo(auth()->user())
            ->upcoming()
            ->when($request->filled('service_id'),
                fn($q) => $q->where('service_id', $request->service_id))
            ->when($request->filled('professional_id'),
                fn($q) => $q->where('professional_id', $request->professional_id))
            ->when($request->filled('payment_status_id'),
                fn($q) => $q->where('payment_status_id', $request->payment_status_id))
            ->when($request->filled('date_from'),
                fn($q) => $q->whereDate('day', '>=', $request->date_from))
            ->when($request->filled('date_to'),
                fn($q) => $q->whereDate('day', '<=', $request->date_to))
            ->paginate(15)
            ->withQueryString();

        return view('admin.agenda.list', [
            'events' => $events,
            'services' => $services,
            'paymentStatuses' => PaymentStatus::ordered()->get(),
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
        $events = $this->agendaService->getCalendarEvents(
            $request->query('start'),
            $request->query('end'),
            auth()->user(),
            $request->query('professional_id'),
        );

        return response()->json($events);
    }

   public function cancelEvent($eventId)
    {
        $cancel = Agenda::find($eventId);

        if (!$cancel) {
            return response()->json(['success' => false, 'message' => 'Evento não encontrado'], 404);
        }

        $cancel->delete();
        $this->notificationService->deleteNotification(tenant('id'), $eventId);

        return response()->json(['success' => true], 200);
    }

    public function update(Request $request, $id)
    {
        $agenda = Agenda::findOrFail($id);

        $this->authorizeAgenda($agenda);

        $data = $request->validate([
            'payment_status_id' => ['required', 'exists:payment_statuses,id'],
            'paid_at'           => ['nullable', 'date'],
            'notes'             => ['nullable', 'string', 'max:2000'],
        ]);

        $status = PaymentStatus::findOrFail($data['payment_status_id']);

        // Usa a data manual se preenchida; senão, deriva do is_paid
        $paidAt = $request->filled('paid_at')
            ? $data['paid_at']
            : ($status->is_paid ? ($agenda->paid_at ?? now()) : null);

        $agenda->update([
            'payment_status_id' => $status->id,
            'paid'              => $status->is_paid,
            'paid_at'           => $paidAt,
            'notes'             => $data['notes'] ?? null,
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
