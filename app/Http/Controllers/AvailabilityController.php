<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\LocationBlock;
use App\Models\LocationClosure;
use App\Models\Professional;
use App\Models\ProfessionalUnavailability;
use App\Models\ProfessionalWorkingHour;
use App\Models\Service;
use App\Models\WorkingHour;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    public function getAvailableSlots(Request $request)
    {
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $serviceId = $request->service_id;
        $specificProfessional = $request->professional_id;

        $service = Service::findOrFail($serviceId);
        $duration = $service->duration;
        $slotInterval = config('app.slot_interval', 30); // em minutos

        $results = [];

        $professionals = $specificProfessional
            ? Professional::where('id', $specificProfessional)->get()
            : Professional::where('status', true)->get();

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateString = $date->toDateString();
            $dayOfWeek = $date->dayOfWeek; // 0 = Domingo ... 6 = Sábado
            $dayName = $date->format('l');

            // Verificar se o local está encerrado
            if (LocationClosure::where('day', $dateString)->exists()) {
                continue;
            }

            foreach ($professionals as $pro) {
                $workingBlocks = ProfessionalWorkingHour::where('professional_id', $pro->id)
                    ->where('weekday', $dayOfWeek)
                    ->get();

                if ($workingBlocks->isEmpty()) continue;

                // Marcações existentes
                $bookings = Agenda::where('professional_id', $pro->id)
                    ->whereDate('day', $dateString)
                    ->get(['start_hour', 'end_hour']);

                $bookedSlots = [];
                foreach ($bookings as $b) {
                    $bookedSlots[] = [
                        'start' => Carbon::parse("{$dateString} {$b->start_hour}"),
                        'end' => Carbon::parse("{$dateString} {$b->end_hour}")
                    ];
                }

                // Bloqueios do local
                $locationBlocks = LocationBlock::all()->filter(function ($block) use ($dayName) {
                    $days = is_array($block->days_of_week)
                        ? $block->days_of_week
                        : json_decode($block->days_of_week, true);

                    return in_array($dayName, $days);
                });

                $blockedIntervals = [];
                foreach ($locationBlocks as $block) {
                    $blockedIntervals[] = [
                        'start' => Carbon::parse("{$block->day} {$block->start_hour}"),
                        'end' => Carbon::parse("{$block->day} {$block->end_hour}")
                    ];
                }

                // Ausências do profissional
                $unavailabilities = ProfessionalUnavailability::where('professional_id', $pro->id)
                    ->where('day', $dateString)
                    ->get();

                $skipDay = false;
                foreach ($unavailabilities as $ua) {
                    if ($ua->start_hour && $ua->end_hour) {
                        $blockedIntervals[] = [
                            'start' => Carbon::parse("{$dateString} {$ua->start_hour}"),
                            'end' => Carbon::parse("{$dateString} {$ua->end_hour}")
                        ];
                    } else {
                        $skipDay = true;
                        break;
                    }
                }

                if ($skipDay) continue;

                $slots = [];

                foreach ($workingBlocks as $block) {
                    $startHour = is_object($block->start_hour)
                        ? $block->start_hour->format('H:i')
                        : $block->start_hour;

                    $endHour = is_object($block->end_hour)
                        ? $block->end_hour->format('H:i')
                        : $block->end_hour;

                    $start = Carbon::parse("{$dateString} {$startHour}");
                    $end = Carbon::parse("{$dateString} {$endHour}");

                    $lunchStart = $block->lunch_start
                        ? Carbon::parse("{$dateString} " . (is_object($block->lunch_start) ? $block->lunch_start->format('H:i') : $block->lunch_start))
                        : null;

                    $lunchEnd = $block->lunch_end
                        ? Carbon::parse("{$dateString} " . (is_object($block->lunch_end) ? $block->lunch_end->format('H:i') : $block->lunch_end))
                        : null;

                    $current = $start->copy();

                    while ($current->copy()->addMinutes($duration)->lte($end)) {
                        $slotEnd = $current->copy()->addMinutes($duration);
                        $conflict = false;

                        // Conflitos com marcações
                        foreach ($bookedSlots as $b) {
                            if ($current->lt($b['end']) && $slotEnd->gt($b['start'])) {
                                $conflict = true;
                                break;
                            }
                        }

                        // Pausa almoço
                        if ($lunchStart && $lunchEnd && $current->lt($lunchEnd) && $slotEnd->gt($lunchStart)) {
                            $conflict = true;
                        }

                        // Conflitos com bloqueios
                        foreach ($blockedIntervals as $bi) {
                            if ($current->lt($bi['end']) && $slotEnd->gt($bi['start'])) {
                                $conflict = true;
                                break;
                            }
                        }

                        if (!$conflict) {
                            $slots[] = $current->format('H:i');
                        }

                        $current->addMinutes($slotInterval);
                    }
                }

                if (count($slots)) {
                    $results[] = [
                        'day' => $dateString,
                        'professional_id' => $pro->id,
                        'professional_name' => $pro->name,
                        'available_slots' => $slots
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
