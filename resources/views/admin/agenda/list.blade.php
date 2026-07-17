@extends('layouts.app')
@section('content')

    @php
        $filtersApplied = request()->filled('service_id')
            || request()->filled('professional_id')
            || request()->filled('payment_status_id')
            || request()->filled('date_from')
            || request()->filled('date_to');
    @endphp

    <div class="row g-6">
        <div class="col-md-12">
            @if($events->total() === 0 && !$filtersApplied)
                <x-empty-state
                    title="Nenhuma marcação encontrada"
                    message="Ainda não foram adicionados registos à lista de Marcações."
                    button="Nova Marcação"
                    link="{{ route('agenda.form') }}"
                />
            @else
                <div class="card">
                    <div class="card-header header-elements">
                        <h5 class="mb-0 me-2">Listagem de Marcações</h5>
                        <div class="card-header-elements ms-auto">
                            <a href="{{ route('agenda.form') }}" class="btn btn-primary waves-effect waves-light">
                                <span class="icon-base ti tabler-plus icon-xs me-1"></span>Nova Marcação
                            </a>
                        </div>
                    </div>

                    {{-- ===== Barra de filtros ===== --}}
                    <div class="card-body border-bottom">
                        <form method="GET" action="{{ route('agenda.list') }}" class="row g-3">
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label mb-1" for="filter-service">Serviço</label>
                                <select name="service_id" id="filter-service" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}" @selected(request('service_id') == $service->id)>
                                            {{ $service->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label mb-1" for="filter-professional">Profissional</label>
                                <select name="professional_id" id="filter-professional" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach($professionals as $professional)
                                        <option value="{{ $professional->id }}" @selected(request('professional_id') == $professional->id)>
                                            {{ $professional->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <label class="form-label mb-1" for="filter-payment">Pagamento</label>
                                <select name="payment_status_id" id="filter-payment" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach($paymentStatuses as $status)
                                        <option value="{{ $status->id }}" @selected(request('payment_status_id') == $status->id)>
                                            {{ $status->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label mb-1" for="filter-date-from">De</label>
                                        <input type="date" name="date_from" id="filter-date-from"
                                               class="form-control" value="{{ request('date_from') }}">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label mb-1" for="filter-date-to">Até</label>
                                        <input type="date" name="date_to" id="filter-date-to"
                                               class="form-control" value="{{ request('date_to') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 d-flex gap-2">
                                <button type="submit" class="btn btn-primary waves-effect waves-light">
                                    <i class="ti tabler-filter me-1"></i>Filtrar
                                </button>
                                @if($filtersApplied)
                                    <a href="{{ route('agenda.list') }}" class="btn btn-outline-secondary waves-effect"
                                       title="Limpar filtros">
                                        <i class="ti tabler-x me-1"></i>Limpar
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>

                    {{-- ===== Tabela ===== --}}
                    <div class="card-body">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="border-bottom">
                                <tr>
                                    <th>Serviço</th>
                                    <th>Data</th>
                                    <th>Horário</th>
                                    <th>Pagamento</th>
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
                                            $date = \Carbon\Carbon::parse($event->day)->locale('pt_PT');
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
                                                <a href="{{ route('agenda.show', $event) }}" class="fw-medium text-heading">
                                                    {{ $event->service->name }}
                                                </a>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="text-heading">{{ $dateLabel }}</span>
                                                @if($isToday)
                                                    <span class="badge bg-label-success mt-1 align-self-start">Hoje</span>
                                                @elseif($isTomorrow)
                                                    <span class="badge bg-label-warning mt-1 align-self-start">Amanhã</span>
                                                @elseif($weekday)
                                                    <small class="text-body-secondary">{{ $weekday }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-label-info">
                                                <i class="ti tabler-clock me-1"></i>{{ $event->start_hour }}h – {{ $event->end_hour }}h
                                            </span>
                                        </td>
                                        <td>
                                            @if($event->paymentStatus)
                                                <span class="badge bg-label-{{ $event->paymentStatus->color }}">
                                                    {{ $event->paymentStatus->name }}
                                                </span>
                                            @else
                                                <span class="badge bg-label-secondary">Sem estado</span>
                                            @endif
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
                                        <td colspan="5">
                                            <div class="text-center py-5">
                                                <i class="ti tabler-calendar-search text-body-secondary mb-2"
                                                   style="font-size: 2.5rem;"></i>
                                                <p class="mb-1 text-body-secondary">Nenhuma marcação corresponde aos filtros aplicados.</p>
                                                <a href="{{ route('agenda.list') }}" class="fw-medium">Limpar filtros</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- ===== Rodapé: contagem + paginação ===== --}}
                    @if($events->total() > 0)
                        <div class="card-footer d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <small class="text-body-secondary">
                                A mostrar {{ $events->firstItem() }}–{{ $events->lastItem() }}
                                de {{ $events->total() }} marcaç{{ $events->total() === 1 ? 'ão' : 'ões' }}
                            </small>
                            @if($events->hasPages())
                                {{ $events->withQueryString()->links() }}
                            @endif
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </div>
@endsection
