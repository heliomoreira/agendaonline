<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Client;
use App\Models\LocationBlock;
use App\Models\LocationClosure;
use App\Models\Portal;
use App\Models\Professional;
use App\Models\ProfessionalUnavailability;
use App\Models\ProfessionalWorkingHour;
use App\Models\Service;
use App\Models\Tenant;
use App\Notifications\BookingConfirmation;
use App\Services\CustomerService;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Setting;

class BookingController extends Controller
{
    private const SLOT_INTERVAL = 30;
    private const SUNDAY = 0;
    private const SATURDAY = 6;

    public function index()
    {
        $services = Service::all();
        $professionals = Professional::all();
        $portal = Portal::all()->first();

        return view('front.portal.booking', [
            'services' => $services,
            'professionals' => $professionals,
            'portal' => $portal,
            'requiresPayment' => $portal->requires_payment ?? false,
            'paymentPercentage' => $portal->payment_percentage ?? 0,
        ]);
    }

    public function bookSlot(Request $request, $admin = false)
    {
        $rules = [
            'service_id' => 'required|exists:services,id',
            'professional_id' => 'required|exists:professionals,id',
            'day' => 'required|date',
            'start_hour' => 'required|date_format:H:i',
            'client_name' => 'required|string|max:255',
            'client_phone_1' => 'required|integer',
            'client_id' => 'nullable|exists:clients,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if (!$admin) {
            $validator->sometimes('g-recaptcha-response', 'required|recaptchav3:register,0.5', function () {
                return true;
            });
        }

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }

        $tenant = Tenant::find(tenant('id'));
        $portal = Portal::all()->first();

        // Cliente escolhido na pesquisa (back-office) ou, caso contrário,
        // procura por telemóvel e cria um novo se não existir.
        $client = $request->filled('client_id')
            ? Client::find($request->client_id)
            : null;

        if (!$client) {
            $client = Client::firstOrCreate(
                [
                    'phone_1' => $request->client_phone_1
                ],
                [
                    'name' => $request->client_name,
                    'email' => $request->client_email,
                    'phone_1_country_code' => $request->client_phone_1_country_code ?? '351',
                ]
            );
        }

        $service = Service::findOrFail($request->service_id);
        $start = Carbon::parse("{$request->day} {$request->start_hour}");
        $end = $start->copy()->addMinutes($service->duration);

        // Sobreposição de marcações: se ligada nas definições, qualquer
        // marcação (portal ou admin) pode partilhar o mesmo horário e a
        // verificação de conflito é ignorada.
        $allowOverlap = Setting::current()->booking_allow_overlap;

        if (!$allowOverlap) {
            // Verificar conflitos
            $conflict = Agenda::where('professional_id', $request->professional_id)
                ->where('day', $request->day)
                ->where(function ($q) use ($start, $end) {
                    $q->where(function ($q2) use ($start, $end) {
                        $q2->where('start_hour', '<', $start->format('H:i'))
                            ->where('end_hour', '>', $start->format('H:i'));
                    })
                        ->orWhere(function ($q2) use ($start, $end) {
                            $q2->where('start_hour', '<', $end->format('H:i'))
                                ->where('end_hour', '>', $end->format('H:i'));
                        })
                        ->orWhere(function ($q2) use ($start, $end) {
                            $q2->where('start_hour', '>=', $start->format('H:i'))
                                ->where('end_hour', '<=', $end->format('H:i'));
                        });
                })->exists();

            if ($conflict) {
                return redirect()->back()->withInput()->withErrors(['Este horário já está reservado. Por favor, escolha outro.']);
            }
        }

        // Criar marcação
        $booking = Agenda::create([
            'service_id' => $request->service_id,
            'professional_id' => $request->professional_id,
            'client_id' => $client->id,
            'client_name' => $request->client_name,
            'client_email' => $request->client_email,
            'client_phone_1' => $request->client_phone_1,
            'day' => $request->day,
            'start_hour' => $start->format('H:i'),
            'end_hour' => $end->format('H:i'),
            'notes' => $request->notes,
        ]);

        $booking->load(['client', 'service', 'professional']);

        $serviceName = $service ? $service->name : 'o seu serviço';

        // ---- SMS ao cliente ----
        if ($tenant->sms_status && $tenant->sms_sender != null && $tenant->sms_credits > 0.04) {

            $service->load('smsTemplate');
            $template = $service->smsTemplate;

            $text = ($template && $template->status && filled($template->body))
                ? $this->renderTemplate($template->body, $booking)
                : "Olá, lembramos que tem o serviço {$serviceName} agendado para o dia "
                . Carbon::parse($request->day)->format('d/m/Y') . " às {$request->start_hour}. Em caso de dúvida ou alteração, contacte-nos. Obrigado.";

            $clientPhone = $client->phone_1_country_code . $client->phone_1;

            NotificationService::saveNotification(
                $tenant->id, $booking->id, $tenant->sms_sender,
                $clientPhone, 'sms', $text,
                $request->day, $start->format('H:i'), $end->format('H:i')
            );
        }

        // ---- SMS ao encarregado (só se for menor e a opção estiver ativa) ----
        if (
            $tenant->sms_status && $tenant->sms_sender != null && $tenant->sms_credits > 0.08
            && $client->is_minor && $client->send_sms_to_parent && $client->parent_phone_1
        ) {
            $client->load('parentSmsTemplate');
            $parentTemplate = $client->parentSmsTemplate;

            $parentText = ($parentTemplate && $parentTemplate->status && filled($parentTemplate->body))
                ? $this->renderTemplate($parentTemplate->body, $booking)
                : "Olá, informamos que {$booking->client_name} tem o serviço {$serviceName} agendado para o dia "
                . Carbon::parse($request->day)->format('d/m/Y') . " às {$request->start_hour}. Obrigado.";

            $parentPhone = $client->parent_phone_1_country_code . $client->parent_phone_1;

            NotificationService::saveNotification(
                $tenant->id, $booking->id, $tenant->sms_sender,
                $parentPhone, 'sms', $parentText,
                $request->day, $start->format('H:i'), $end->format('H:i')
            );
        }

        Notification::route('mail', $request->client_email)
            ->notify(new BookingConfirmation($booking));

        if ($admin) {
            return redirect()->route('agenda.show', $booking->id)
                ->with('success', 'Agendamento reservado com sucesso!');
        } else {
            return view('front.portal.confirmation', compact('portal', 'booking', 'tenant'));
        }
    }


    public function getAvailableSlots(Request $request): JsonResponse
    {
        try {
            $validatedData = $this->validateRequest($request);

            $service = Service::findOrFail($validatedData['service_id']);
            $professionals = $this->getProfessionals($validatedData['professional_id'] ?? null);

            if ($professionals->isEmpty()) {
                return response()->json(['message' => 'Nenhum profissional disponível'], 404);
            }

            // Com a sobreposição ligada, os horários já reservados continuam
            // a aparecer como disponíveis para qualquer pessoa.
            $ignoreBookings = Setting::current()->booking_allow_overlap;

            $results = $this->calculateAvailableSlots(
                $validatedData['start_date'],
                $validatedData['end_date'],
                $service,
                $professionals,
                $ignoreBookings
            );

            return response()->json($results);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro interno do servidor'], 500);
        }
    }

    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'service_id' => 'required|exists:services,id',
            'professional_id' => 'nullable|exists:professionals,id'
        ]);
    }

    private function getProfessionals(?int $professionalId): Collection
    {
        $query = Professional::query();

        if ($professionalId) {
            return $query->where('id', $professionalId)->get();
        }

        return $query->where('status', true)->get();
    }

    private function calculateAvailableSlots(
        string     $startDate,
        string     $endDate,
        Service    $service,
        Collection $professionals,
        bool       $ignoreBookings = false
    ): array
    {
        $results = [];
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        // Pre-load data to reduce database queries
        $closedDates = $this->getClosedDates($startDate, $endDate);
        $locationBlocks = $this->getLocationBlocks();

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            if ($this->isLocationClosed($date, $closedDates)) {
                continue;
            }

            foreach ($professionals as $professional) {
                $slots = $this->getProfessionalAvailableSlots($date, $professional, $service, $locationBlocks, $ignoreBookings);

                if (!empty($slots)) {
                    $results[] = [
                        'day' => $date->toDateString(),
                        'professional_id' => $professional->id,
                        'professional_name' => $professional->name,
                        'available_slots' => $slots
                    ];
                }
            }
        }

        return $results;
    }

    private function getClosedDates(Carbon $startDate, Carbon $endDate): array
    {
        return LocationClosure::whereBetween('day', [
            $startDate->toDateString(),
            $endDate->toDateString()
        ])->pluck('day')->toArray();
    }

    private function getLocationBlocks(): Collection
    {
        return LocationBlock::all();
    }

    private function isLocationClosed(Carbon $date, array $closedDates): bool
    {
        return in_array($date->toDateString(), $closedDates);
    }

    private function getProfessionalAvailableSlots(
        Carbon       $date,
        Professional $professional,
        Service      $service,
        Collection   $locationBlocks,
        bool         $ignoreBookings = false
    ): array
    {
        $workingBlocks = $this->getProfessionalWorkingHours($professional->id, $date->dayOfWeek);

        if ($workingBlocks->isEmpty()) {
            return [];
        }

        if ($this->isProfessionalUnavailable($professional->id, $date)) {
            return [];
        }

        // Só as marcações existentes são ignoradas quando a sobreposição está
        // ligada. Almoço, bloqueios de local e indisponibilidades continuam
        // a ser respeitados (não são "sobreposições" de marcações).
        $bookedSlots = $ignoreBookings ? [] : $this->getBookedSlots($professional->id, $date);
        $blockedIntervals = $this->getBlockedIntervals($professional->id, $date, $locationBlocks);

        return $this->generateAvailableSlots($workingBlocks, $date, $service->duration, $bookedSlots, $blockedIntervals);
    }

    private function getProfessionalWorkingHours(int $professionalId, int $dayOfWeek): Collection
    {
        return ProfessionalWorkingHour::where('professional_id', $professionalId)
            ->where('weekday', $dayOfWeek)
            ->get();
    }

    private function isProfessionalUnavailable(int $professionalId, Carbon $date): bool
    {
        return ProfessionalUnavailability::where('professional_id', $professionalId)
            ->where('day', $date->toDateString())
            ->whereNull('start_hour')
            ->whereNull('end_hour')
            ->exists();
    }

    private function getBookedSlots(int $professionalId, Carbon $date): array
    {
        $bookings = Agenda::where('professional_id', $professionalId)
            ->whereDate('day', $date->toDateString())
            ->get(['start_hour', 'end_hour']);

        return $bookings->map(function ($booking) use ($date) {
            return [
                'start' => Carbon::parse($date->toDateString() . ' ' . $booking->start_hour),
                'end' => Carbon::parse($date->toDateString() . ' ' . $booking->end_hour)
            ];
        })->toArray();
    }

    private function getBlockedIntervals(int $professionalId, Carbon $date, Collection $locationBlocks): array
    {
        $intervals = [];
        $dayName = $date->format('l');
        $dateString = $date->toDateString();

        // Location blocks
        foreach ($locationBlocks as $block) {
            if ($this->isDayInLocationBlock($dayName, $block)) {
                $intervals[] = [
                    'start' => Carbon::parse($dateString . ' ' . $block->start_hour),
                    'end' => Carbon::parse($dateString . ' ' . $block->end_hour)
                ];
            }
        }

        // Professional unavailabilities (partial)
        $unavailabilities = ProfessionalUnavailability::where('professional_id', $professionalId)
            ->where('day', $dateString)
            ->whereNotNull('start_hour')
            ->whereNotNull('end_hour')
            ->get();

        foreach ($unavailabilities as $unavailability) {
            $intervals[] = [
                'start' => Carbon::parse($dateString . ' ' . $unavailability->start_hour),
                'end' => Carbon::parse($dateString . ' ' . $unavailability->end_hour)
            ];
        }

        return $intervals;
    }

    private function isDayInLocationBlock($dayName, $block): bool
    {
        $days = is_array($block->days_of_week)
            ? $block->days_of_week
            : json_decode($block->days_of_week, true);

        return is_array($days) && in_array($dayName, $days);
    }

    private function generateAvailableSlots(
        Collection $workingBlocks,
        Carbon     $date,
        int        $serviceDuration,
        array      $bookedSlots,
        array      $blockedIntervals
    ): array
    {
        $allSlots = [];
        $dateString = $date->toDateString();
        $slotInterval = config('booking.slot_interval', self::SLOT_INTERVAL);

        foreach ($workingBlocks as $block) {
            $blockSlots = $this->generateSlotsForWorkingBlock(
                $block,
                $dateString,
                $serviceDuration,
                $slotInterval,
                $bookedSlots,
                $blockedIntervals
            );

            $allSlots = array_merge($allSlots, $blockSlots);
        }

        return array_values(array_unique($allSlots));
    }

    private function generateSlotsForWorkingBlock(
        $workingBlock,
        string $dateString,
        int $serviceDuration,
        int $slotInterval,
        array $bookedSlots,
        array $blockedIntervals
    ): array
    {
        $slots = [];

        $startHour = $this->formatTime($workingBlock->start_hour);
        $endHour = $this->formatTime($workingBlock->end_hour);

        $start = Carbon::parse("{$dateString} {$startHour}");
        $end = Carbon::parse("{$dateString} {$endHour}");

        $lunchPeriod = $this->getLunchPeriod($workingBlock, $dateString);

        $current = $start->copy();
        while ($current->copy()->addMinutes($serviceDuration)->lte($end)) {
            $slotEnd = $current->copy()->addMinutes($serviceDuration);

            if ($this->isSlotAvailable($current, $slotEnd, $bookedSlots, $blockedIntervals, $lunchPeriod)) {
                $slots[] = $current->format('H:i');
            }

            $current->addMinutes($slotInterval);
        }

        return $slots;
    }

    private function formatTime($time): string
    {
        return is_object($time) ? $time->format('H:i') : (string)$time;
    }

    private function getLunchPeriod($workingBlock, string $dateString): ?array
    {
        if (!$workingBlock->lunch_start || !$workingBlock->lunch_end) {
            return null;
        }

        return [
            'start' => Carbon::parse($dateString . ' ' . $this->formatTime($workingBlock->lunch_start)),
            'end' => Carbon::parse($dateString . ' ' . $this->formatTime($workingBlock->lunch_end))
        ];
    }

    private function isSlotAvailable(
        Carbon $slotStart,
        Carbon $slotEnd,
        array  $bookedSlots,
        array  $blockedIntervals,
        ?array $lunchPeriod
    ): bool
    {
        // Check conflicts with bookings
        foreach ($bookedSlots as $booking) {
            if ($this->periodsOverlap($slotStart, $slotEnd, $booking['start'], $booking['end'])) {
                return false;
            }
        }

        // Check lunch break
        if ($lunchPeriod && $this->periodsOverlap($slotStart, $slotEnd, $lunchPeriod['start'], $lunchPeriod['end'])) {
            return false;
        }

        // Check blocked intervals
        foreach ($blockedIntervals as $interval) {
            if ($this->periodsOverlap($slotStart, $slotEnd, $interval['start'], $interval['end'])) {
                return false;
            }
        }

        return true;
    }

    private function periodsOverlap(Carbon $start1, Carbon $end1, Carbon $start2, Carbon $end2): bool
    {
        return $start1->lt($end2) && $end1->gt($start2);
    }

    public function createPaymentIntent(Request $request)
    {
        $service = Service::findOrFail($request->service_id);
        $amount = $service->price * 100; // em cêntimos

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $amount,
            'currency' => 'eur',
            'payment_method_types' => [
                'card',
                'multibanco',
                'ideal',  // opcional
            ],
            'metadata' => [
                'service_id' => $request->service_id,
                'professional_id' => $request->professional_id,
                'day' => $request->day,
                'start_hour' => $request->start_hour,
            ],
        ]);

        return response()->json([
            'clientSecret' => $paymentIntent->client_secret,
            'paymentIntentId' => $paymentIntent->id,
        ]);
    }

    public function success(Request $request)
    {
        $paymentIntentId = $request->query('payment_intent');

        if ($paymentIntentId) {
            // Buscar na Stripe o status
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            $pi = \Stripe\PaymentIntent::retrieve($paymentIntentId);

            // Se Multibanco e ainda não pago
            if ($pi->status === 'requires_action') {
                return view('booking.pending-payment', [
                    'entity' => $pi->next_action->multibanco_display_details->entity ?? null,
                    'reference' => $pi->next_action->multibanco_display_details->reference ?? null,
                    'amount' => $pi->amount / 100,
                ]);
            }

            // Se já pago, mostrar confirmação
            if ($pi->status === 'succeeded') {
                $booking = Agenda::where('payment_intent_id', $paymentIntentId)->first();
                return view('booking.confirmation', compact('booking'));
            }
        }

        return redirect('/');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'professional_id' => 'nullable|exists:professionals,id',
            'day' => 'required|date|after_or_equal:today',
            'start_hour' => 'required',
            'client_name' => 'required|string|max:255',
            'client_phone_1' => 'required|string|max:20',
            'client_email' => 'nullable|email|max:255',
            'notes' => 'nullable|string|max:1000',
            'payment_intent_id' => 'nullable|string', // se veio do Stripe
        ]);

        $service = Service::with('portal')->findOrFail($validated['service_id']);

        // Sobreposição: se ligada, ignora a verificação de disponibilidade
        // e deixa a marcação partilhar o horário com outra existente.
        $allowOverlap = Setting::current()->booking_allow_overlap;

        if (!$allowOverlap) {
            //@todo Validar
            $isAvailable = $this->checkSlotAvailability(
                $validated['service_id'],
                $validated['day'],
                $validated['start_hour'],
                $validated['professional_id']
            );

            if (!$isAvailable) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este horário já não está disponível. Por favor, escolha outro.',
                ], 422);
            }
        }

        $startTime = Carbon::createFromFormat('H:i', $validated['start_hour']);
        $endTime = $startTime->copy()->addMinutes($service->duration);

        $client = CustomerService::findOrCreate([
            'name'    => $validated['client_name'],
            'email'   => $validated['client_email'],
            'phone_1' => $validated['client_phone_1'],
        ]);

        // Determinar status inicial
        $status = 'pending';
        $paid = false;

        if ($validated['payment_intent_id']) {
            // Pagamento já foi feito (cartão)
            $status = 'confirmed';
            $paid = true;
        } elseif ($service->portal->requires_payment) {
            // Requer pagamento mas ainda não foi pago (Multibanco pendente)
            $status = 'payment_pending';
        } else {
            // Não requer pagamento
            $status = 'pending';
        }

        // Criar marcação
        $booking = Agenda::create([
            'client_id' => $client?->id,
            'service_id' => $validated['service_id'],
            'professional_id' => $validated['professional_id'],
            'day' => $validated['day'],
            'start_hour' => $validated['start_hour'],
            'end_hour' => $endTime->format('H:i'),
            'client_name' => $validated['client_name'],
            'client_email' => $validated['client_email'],
            'client_phone_1' => $validated['client_phone_1'],
            'notes' => $validated['notes'],
            'amount' => $service->price,
            'payment_intent_id' => $validated['payment_intent_id'],
            'status' => $status,
            'paid' => $paid,
            'confirmed_at' => $paid ? now() : null,
        ]);

        // Enviar emails
        if ($booking->client_email) {
            // Email de confirmação (se já pago) ou aguardando pagamento
            if ($paid) {
                // Mail::to($booking->client_email)->send(new BookingConfirmed($booking));
            }

            // Se cliente não tem conta, convidar a criar
            if ($client && !$client->hasAccount()) {
                // Mail::to($client->email)->send(new InviteToCreateAccount($client, $booking));
            }
        }

        return response()->json([
            'success' => true,
            'message' => $paid
                ? 'Marcação confirmada com sucesso!'
                : 'Marcação registada. Aguardando confirmação de pagamento.',
            'booking_id' => $booking->id,
            'redirect' => route('booking.confirmation', $booking->id),
        ]);
    }

    private function renderTemplate(string $body, Agenda $booking): string
    {
        $date = Carbon::parse($booking->day)->locale('pt_PT');

        return strtr($body, [
            '[NOME_CLIENTE]' => $booking->client_name,
            '[DATA]' => $date->format('d/m/Y'),
            '[DIA_SEMANA]' => $this->weekDay($date),
            '[HORA]' => $booking->start_hour,
            '[SERVICO]' => $booking->service->name ?? '',
        ]);
    }

    private function weekDay(Carbon $date): string
    {
        return [
            Carbon::MONDAY    => 'Segunda-feira',
            Carbon::TUESDAY   => 'Terça-feira',
            Carbon::WEDNESDAY => 'Quarta-feira',
            Carbon::THURSDAY  => 'Quinta-feira',
            Carbon::FRIDAY    => 'Sexta-feira',
            Carbon::SATURDAY  => 'Sábado',
            Carbon::SUNDAY    => 'Domingo',
        ][$date->dayOfWeek];
    }
}
