<?php
namespace App\Services;

use App\Models\Agenda;
use App\Models\Setting;
use Carbon\Carbon;

class AgendaService
{
    public function getCalendarEvents(
        ?string $startParam,
        ?string $endParam,
                $user,
        ?string $professionalId = null,
        ?string $colorSource = null
    ) {

        $colorSource ??= Setting::current()->color_in_agenda ?? 'professional';

        $start = $startParam ? Carbon::parse($startParam)->format('Y-m-d') : now()->startOfWeek()->format('Y-m-d');
        $end   = $endParam   ? Carbon::parse($endParam)->format('Y-m-d')   : now()->endOfWeek()->format('Y-m-d');

        $query = Agenda::with(['client', 'professional', 'service'])
            ->whereBetween('day', [$start, $end])
            ->visibleTo($user);

        if (!($user && $user->isRestrictedToOwnAgenda()) && filled($professionalId)) {
            $query->where('professional_id', $professionalId);
        }

        return $query->get()->map(function ($item) use ($colorSource) {
            $startsAt = Carbon::parse($item->day)->setTimeFromTimeString($item->start_hour);
            $endsAt   = Carbon::parse($item->day)->setTimeFromTimeString($item->end_hour);

            [$color, $textColor] = $this->resolveEventColors($item, $colorSource);

            return [
                'id'        => $item->id,
                'title'     => $item->service->name ?? 'Serviço',
                'start'     => $startsAt,
                'end'       => $endsAt,
                'category'  => optional($item->professional)->id,
                'color'     => $color,
                'textColor' => $textColor,
                'extendedProps' => [
                    'client'       => optional($item->client)->name ?? '',
                    'service'      => optional($item->service)->name ?? '',
                    'professional' => optional($item->professional)->name ?? '',
                    'notes'        => $item->notes ?? '',
                ],
            ];
        })->values();
    }

    private function resolveEventColors($item, string $colorSource): array
    {
        $fallback = '#2F87EB';

        if ($colorSource === 'service') {
            return [
                optional($item->service)->bg_color ?: $fallback,
                optional($item->service)->text_color ?: null,
            ];
        }

        return [
            optional($item->professional)->agenda_color ?: $fallback,
            null,
        ];
    }
}
