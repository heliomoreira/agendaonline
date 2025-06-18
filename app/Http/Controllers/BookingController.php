<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Client;
use App\Models\Professional;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class BookingController extends Controller
{
    public function index()
    {
        $services = Service::where('status', true)->get();
        $professionals = Professional::where('status', true)->get();

        return view('front.booking.index', compact('services', 'professionals'));
    }

    public function bookSlot(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:services,id',
            'professional_id' => 'required|exists:professionals,id',
            'day' => 'required|date',
            'start_hour' => 'required|date_format:H:i',
        ]);

        $client = Client::create([
            'name' => $request->client_name,
            'email' => $request->client_email,
            'phone_1' => $request->client_phone_1,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $service = Service::findOrFail($request->service_id);
        $start = Carbon::parse($request->day . ' ' . $request->start_hour);
        $end = $start->copy()->addMinutes($service->duration);

        // Check for overlapping
        $conflict = Agenda::where('professional_id', $request->professional_id)
            ->where('day', $request->day)
            ->where(function ($q) use ($request, $start, $end) {
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

        $agenda = Agenda::create([
            'service_id' => $request->service_id,
            'professional_id' => $request->professional_id,
            'client_id' => $client->id,
            'day' => $request->day,
            'start_hour' => $start->format('H:i'),
            'end_hour' => $end->format('H:i'),
            'notes' => $request->notes,
        ]);

        return response()->json(['message' => 'Booking confirmed!', 'agenda' => $agenda]);
    }
}
