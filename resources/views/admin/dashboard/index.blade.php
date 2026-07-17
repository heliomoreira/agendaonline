@extends('layouts.app')
@section('content')

    {{-- ===== Cabeçalho de boas-vindas ===== --}}
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 my-4">
                <div>
                    <h4 class="mb-1">Dashboard</h4>
                    <p class="mb-0 text-body-secondary text-capitalize">
                        {{ \Carbon\Carbon::now()->translatedFormat('l, d \d\e F \d\e Y') }}
                    </p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('agenda.form') }}" class="btn btn-primary waves-effect waves-light">
                        <i class="ti tabler-calendar-plus me-1" aria-hidden="true"></i> Nova Marcação
                    </a>
                    <a href="{{ route('agenda.index') }}" class="btn btn-outline-secondary waves-effect">
                        <i class="ti tabler-calendar me-1" aria-hidden="true"></i> Ver Agenda
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Cartões de estatística ===== --}}
    {{-- NOTA: $servicesMonth e $professionalsCount precisam de ser passados pelo controller (mostram 0 enquanto não existirem) --}}
    <div class="row g-4 mb-2">
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="avatar">
                            <span class="avatar-initial bg-label-primary rounded">
                                <i class="ti tabler-calendar-event icon-lg" aria-hidden="true"></i>
                            </span>
                        </div>
                        <span class="badge bg-label-primary rounded-pill">Hoje</span>
                    </div>
                    <p class="mb-1 text-body-secondary">Serviços de Hoje</p>
                    <h4 class="mb-0">{{ $servicesToday ?? 0 }}</h4>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="avatar">
                            <span class="avatar-initial bg-label-info rounded">
                                <i class="ti tabler-calendar-week icon-lg" aria-hidden="true"></i>
                            </span>
                        </div>
                        <span class="badge bg-label-info rounded-pill">Semana</span>
                    </div>
                    <p class="mb-1 text-body-secondary">Serviços esta Semana</p>
                    <h4 class="mb-0">{{ $servicesWeek ?? 0 }}</h4>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="avatar">
                            <span class="avatar-initial bg-label-success rounded">
                                <i class="ti tabler-calendar-stats icon-lg" aria-hidden="true"></i>
                            </span>
                        </div>
                        <span class="badge bg-label-success rounded-pill">Mês</span>
                    </div>
                    <p class="mb-1 text-body-secondary">Serviços este Mês</p>
                    <h4 class="mb-0">{{ $servicesMonth ?? 0 }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Próximos serviços ===== --}}
    <div class="row">
        <div class="col-12 mt-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="ti tabler-calendar-clock me-1" aria-hidden="true"></i> Próximos Serviços
                    </h5>
                    <span class="badge bg-label-secondary rounded-pill">
                        {{ count($nextEvents) }} agendado{{ count($nextEvents) === 1 ? '' : 's' }}
                    </span>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="border-bottom">
                            <tr>
                                <th>Serviço</th>
                                <th>Data</th>
                                <th>Horário</th>
                                <th>Profissional</th>
                            </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                            @forelse($nextEvents as $event)
                                @php
                                    $professionalName = $event->professional?->name ?? 'Sem profissional';
                                    $serviceName      = $event->service?->name ?? 'Serviço removido';

                                    // Iniciais do profissional (1ª e última palavra)
                                    $parts = array_values(array_filter(explode(' ', trim($professionalName))));
                                    $initials = mb_strtoupper(
                                        mb_substr($parts[0] ?? '', 0, 1) .
                                        (count($parts) > 1 ? mb_substr(end($parts), 0, 1) : '')
                                    );

                                    // Data amigável + destaque para hoje/amanhã
                                    try {
                                        $date = \Carbon\Carbon::parse($event->day);
                                        $dateLabel = $date->translatedFormat('d M Y');
                                        $weekday   = ucfirst($date->translatedFormat('l'));
                                        $isToday    = $date->isToday();
                                        $isTomorrow = $date->isTomorrow();
                                    } catch (\Throwable $e) {
                                        $dateLabel = $event->day; $weekday = ''; $isToday = false; $isTomorrow = false;
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3">
                                                <span class="avatar-initial bg-label-primary rounded">
                                                    <i class="ti tabler-briefcase" aria-hidden="true"></i>
                                                </span>
                                            </div>
                                            <span class="fw-medium text-heading">{{ $serviceName }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="text-heading">{{ $dateLabel }}</span>
                                            @if($isToday)
                                                <span class="badge bg-label-success rounded-pill mt-1 align-self-start">Hoje</span>
                                            @elseif($isTomorrow)
                                                <span class="badge bg-label-warning rounded-pill mt-1 align-self-start">Amanhã</span>
                                            @elseif($weekday)
                                                <small class="text-body-secondary">{{ $weekday }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-info rounded-pill">
                                            <i class="ti tabler-clock me-1" aria-hidden="true"></i>{{ $event->start_hour }}h – {{ $event->end_hour }}h
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span
                                                    class="avatar-initial bg-label-secondary rounded-circle">{{ $initials }}</span>
                                            </div>
                                            <span class="text-heading">{{ $professionalName }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">
                                        <div class="text-center py-5">
                                            <i class="ti tabler-calendar-off text-body-secondary mb-2"
                                               style="font-size: 2.5rem;" aria-hidden="true"></i>
                                            <p class="mb-3 text-body-secondary">Não há serviços agendados de
                                                momento.</p>
                                            <a href="{{ route('agenda.form') }}"
                                               class="btn btn-sm btn-primary waves-effect waves-light">
                                                <i class="ti tabler-calendar-plus me-1" aria-hidden="true"></i> Criar
                                                Marcação
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer text-end pt-5">
                    <a href="{{ route('agenda.index') }}" class="fw-medium">
                        Ver Agenda completa <i class="ti tabler-arrow-right ms-1" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

@endsection
