@extends('layouts.app')
@section('content')
    <div class="row g-6">
        <div class="col-md-12">
            @if(count($events)>0)
                <div class="card">
                    <div class="card-header header-elements">
                        <h5 class="mb-0 me-2">Listagem de Marcações</h5>
                        <div class="card-header-elements ms-auto">
                            <a href="{{route('agenda.form')}}"
                               class="btn btn-primary waves-effect waves-light">
                                <span class="icon-base ti tabler-plus icon-xs me-1"></span>Nova Marcação
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive text-nowrap">
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
                                @forelse($events as $event)
                                    @php
                                        // Iniciais do profissional (1ª e última palavra)
                                        $parts = array_values(array_filter(explode(' ', trim($event->professional->name))));
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
                                                    <i class="ti tabler-briefcase"></i>
                                                </span>
                                                </div>
                                                <span class="fw-medium text-heading">{{ $event->service->name }}</span>
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
                                            <i class="ti tabler-clock me-1"></i>{{ $event->start_hour }}h – {{ $event->end_hour }}h
                                        </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <span class="avatar-initial bg-label-secondary rounded-circle">{{ $initials }}</span>
                                                </div>
                                                <span class="text-heading">{{ $event->professional->name }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">
                                            <div class="text-center py-5">
                                                <i class="ti tabler-calendar-off text-body-secondary mb-2" style="font-size: 2.5rem;"></i>
                                                <p class="mb-0 text-body-secondary">Não há serviços agendados de momento.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <x-empty-state
                    title="Nenhum cliente encontrado"
                    message="Ainda não foram adicionados registos à lista de Marcações."
                    button="Nova Marcação"
                    link="{{ route('agenda.form') }}"
                />
            @endif
        </div>
    </div>
@endsection
