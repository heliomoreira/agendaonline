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
        $slotInterval = config('app.slot_interval', 30);

        $results = [];

        $professionals = $specificProfessional
            ? Professional::where('id', $specificProfessional)->get()
            : Professional::where('status', true)->get();

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateString = $date->toDateString();
            $dayOfWeek = $date->dayOfWeek; // 0 = Sunday ... 6 = Saturday
            $dayName = $date->format('l'); // e.g. "Monday"

            // Check if location is closed
            if (LocationClosure::where('day', $dateString)->exists()) {
                continue;
            }

            foreach ($professionals as $pro) {
                $working = ProfessionalWorkingHour::where('professional_id', $pro->id)
                    ->where('weekday', $dayOfWeek)
                    ->first();

                if (!$working) continue;

                $start = Carbon::parse("{$dateString} " . $working->start_hour->format('H:i'));
                $end = Carbon::parse("{$dateString} " . $working->end_hour->format('H:i'));
                $lunchStart = $working->lunch_start ? Carbon::parse("{$dateString} " . $working->lunch_start->format('H:i')) : null;
                $lunchEnd = $working->lunch_end ? Carbon::parse("{$dateString} " . $working->lunch_end->format('H:i')) : null;

                // Existing bookings
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

                // Location blocks
                $locationBlocks = LocationBlock::all()->filter(function ($block) use ($dayName) {
                    $days = is_array($block->days_of_week)
                        ? $block->days_of_week
                        : json_decode($block->days_of_week, true);

                    return in_array($dayName, $days);
                });

                $blockedIntervals = [];
                foreach ($locationBlocks as $block) {
                    $blockedIntervals[] = [
                        'start' => Carbon::parse("{$dateString} {$block->start_hour}"),
                        'end' => Carbon::parse("{$dateString} {$block->end_hour}")
                    ];
                }

                $unavailabilities = ProfessionalUnavailability::where('professional_id', $pro->id)
                    ->where('day', $dateString)
                    ->get();

                foreach ($unavailabilities as $ua) {
                    if ($ua->start_hour && $ua->end_hour) {
                        // Parcial Absence
                        $blockedIntervals[] = [
                            'start' => Carbon::parse("{$dateString} {$ua->start_hour}"),
                            'end' => Carbon::parse("{$dateString} {$ua->end_hour}")
                        ];
                    } else {
                        // Full-Day Absence
                        $slots = [];
                        break;
                    }
                }

                // Generate slots
                $current = $start->copy();
                $slots = [];

                while ($current->copy()->addMinutes($duration)->lte($end)) {
                    $slotEnd = $current->copy()->addMinutes($duration);
                    $conflict = false;

                    // Booking overlap
                    foreach ($bookedSlots as $b) {
                        if ($current->lt($b['end']) && $slotEnd->gt($b['start'])) {
                            $conflict = true;
                            break;
                        }
                    }

                    // Lunch break
                    if ($lunchStart && $lunchEnd && $current->lt($lunchEnd) && $slotEnd->gt($lunchStart)) {
                        $conflict = true;
                    }

                    // Location block
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
