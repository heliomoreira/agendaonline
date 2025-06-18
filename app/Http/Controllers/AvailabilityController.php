<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\LocationBlock;
use App\Models\LocationClosure;
use App\Models\Professional;
use App\Models\ProfessionalUnavailability;
use App\Models\Service;
use App\Models\WorkingHour;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'professional_id' => 'nullable|exists:professionals,id',
        ]);

        $service = Service::findOrFail($request->service_id);
        $duration = $service->duration;

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $daysRange = $endDate->diffInDays($startDate) + 1;

        $results = [];

        // Obtem profissionais ativos (filtrado ou todos)
        $professionals = $request->professional_id
            ? Professional::where('id', $request->professional_id)->where('status', true)->get()
            : Professional::where('status', true)->get();

        // Blocos globais (ex: almoço)
        $globalBlocks = LocationBlock::all();

        for ($i = 0; $i < $daysRange; $i++) {
            $currentDate = $startDate->copy()->addDays($i)->format('Y-m-d');
            $dayName = Carbon::parse($currentDate)->format('l');

            foreach ($professionals as $professional) {
                $startHour = Carbon::createFromTime(9, 0, 0);
                $endHour = Carbon::createFromTime(18, 0, 0);

                $slots = [];

                while ($startHour->copy()->addMinutes($duration)->lte($endHour)) {
                    $slotStart = $startHour->copy();
                    $slotEnd = $startHour->copy()->addMinutes($duration);

                    // Verifica marcações existentes
                    $exists = Agenda::where('professional_id', $professional->id)
                        ->where('day', $currentDate)
                        ->where(function ($q) use ($slotStart, $slotEnd) {
                            $q->whereBetween('start_hour', [$slotStart->format('H:i'), $slotEnd->format('H:i')])
                                ->orWhereBetween('end_hour', [$slotStart->format('H:i'), $slotEnd->format('H:i')])
                                ->orWhere(function ($q2) use ($slotStart, $slotEnd) {
                                    $q2->where('start_hour', '<=', $slotStart->format('H:i'))
                                        ->where('end_hour', '>=', $slotEnd->format('H:i'));
                                });
                        })->exists();

                    if ($exists) {
                        $startHour->addMinutes(15);
                        continue;
                    }

                    // Verifica bloqueios globais (almoço, etc)
                    $blocked = false;
                    foreach ($globalBlocks as $block) {
                        if (!in_array($dayName, $block->days_of_week)) {
                            continue;
                        }

                        $blockStart = Carbon::parse($block->start_hour);
                        $blockEnd = Carbon::parse($block->end_hour);

                        if (
                            $slotStart->between($blockStart, $blockEnd) ||
                            $slotEnd->between($blockStart, $blockEnd) ||
                            ($slotStart->lt($blockStart) && $slotEnd->gt($blockEnd))
                        ) {
                            $blocked = true;
                            break;
                        }
                    }

                    if ($blocked) {
                        $startHour->addMinutes(15);
                        continue;
                    }

                    // Adiciona slot
                    $slots[] = $slotStart->format('H:i');
                    $startHour->addMinutes(15);
                }

                if (!empty($slots)) {
                    $results[] = [
                        'day' => $currentDate,
                        'professional_id' => $professional->id,
                        'professional_name' => $professional->name,
                        'available_slots' => $slots,
                    ];
                }
            }
        }

        return response()->json($results);
    }

    private function generateTimeSlots(Carbon $start, Carbon $end, int $duration): array
    {
        $slots = [];
        while ($start->copy()->addMinutes($duration)->lte($end)) {
            $slots[] = $start->format('H:i');
            $start->addMinutes($duration);
        }
        return $slots;
    }
}
