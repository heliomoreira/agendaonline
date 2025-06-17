<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
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

        $professionals = Professional::where('status', true)
            ->when($request->professional_id, fn($q) => $q->where('id', $request->professional_id))
            ->get();

        $slots = [];

        $period = CarbonPeriod::create($request->start_date, $request->end_date);

        foreach ($period as $date) {
            if (LocationClosure::where('day', $date->toDateString())->exists()) {
                continue;
            }

            foreach ($professionals as $professional) {
                if (ProfessionalUnavailability::where('professional_id', $professional->id)->where('day', $date->toDateString())->exists()) {
                    continue;
                }

                $weekday = $date->dayOfWeek;
                $workingHours = WorkingHour::where('professional_id', $professional->id)
                    ->where('weekday', $weekday)
                    ->get();

                foreach ($workingHours as $wh) {
                    $start = Carbon::parse($date->toDateString() . ' ' . $wh->start_hour);
                    $end = Carbon::parse($date->toDateString() . ' ' . $wh->end_hour);

                    $availableSlots = $this->generateTimeSlots($start, $end, $duration);

                    // Filter occupied slots
                    $appointments = Agenda::where('professional_id', $professional->id)
                        ->where('day', $date->toDateString())
                        ->get(['start_hour', 'end_hour']);

                    foreach ($appointments as $appt) {
                        $occupiedRange = Carbon::parse($appt->start_hour)->floatDiffInMinutes(Carbon::parse($appt->end_hour));
                        $startTime = Carbon::parse($date->toDateString() . ' ' . $appt->start_hour);
                        $conflicting = $this->generateTimeSlots($startTime, $startTime->copy()->addMinutes($occupiedRange), $duration);
                        $availableSlots = array_diff($availableSlots, $conflicting);
                    }

                    if (!empty($availableSlots)) {
                        $slots[] = [
                            'professional_id' => $professional->id,
                            'professional_name' => $professional->name,
                            'day' => $date->toDateString(),
                            'available_slots' => array_values($availableSlots),
                        ];
                    }
                }
            }
        }

        return response()->json($slots);
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
