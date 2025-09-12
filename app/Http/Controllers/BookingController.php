<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Client;
use App\Models\Professional;
use App\Models\Service;
use App\Models\Tenant;
use App\Notifications\BookingConfirmation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
/*    public function index()
    {
        $services = Service::where('status', true)->get();
        $professionals = Professional::where('status', true)->get();

        return view('front.booking.index', compact('services', 'professionals'));
    }*/

    public function bookSlot(Request $request)
    {
        // Validar inputs antes de qualquer ação
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:services,id',
            'professional_id' => 'required|exists:professionals,id',
            'day' => 'required|date',
            'start_hour' => 'required|date_format:H:i',
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'client_phone_1' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $tenant = Tenant::find(tenant('id'));

        // Criar cliente
        $client = Client::create([
            'name' => $request->client_name,
            'email' => $request->client_email,
            'phone_1' => $request->client_phone_1,
        ]);

        // Obter dados do serviço
        $service = Service::findOrFail($request->service_id);
        $start = Carbon::parse("{$request->day} {$request->start_hour}");
        $end = $start->copy()->addMinutes($service->duration);

        // Verificar conflitos
        $conflict = Agenda::where('professional_id', $request->professional_id)
            ->where('day', $request->day)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_hour', [$start->format('H:i'), $end->format('H:i')])
                    ->orWhereBetween('end_hour', [$start->format('H:i'), $end->format('H:i')])
                    ->orWhere(function ($q2) use ($start, $end) {
                        $q2->where('start_hour', '<=', $start->format('H:i'))
                            ->where('end_hour', '>=', $end->format('H:i'));
                    });
            })->exists();

        if ($conflict) {
            return response()->json(['error' => 'This slot is already taken.'], 409);
        }

        // Criar marcação
        $booking = Agenda::create([
            'service_id' => $request->service_id,
            'professional_id' => $request->professional_id,
            'client_id' => $client->id,
            'day' => $request->day,
            'start_hour' => $start->format('H:i'),
            'end_hour' => $end->format('H:i'),
            'notes' => $request->notes,
        ]);

        $booking->load(['client', 'service', 'professional']);

        Notification::route('mail', $request->client_email)
            ->notify(new BookingConfirmation($booking));

        return view('front.portal.confirmation', compact('booking','tenant'));

   /*
        return response()->json([
            'message' => 'Booking confirmed!',
            'agenda' => $booking
        ]);*/
    }
}
