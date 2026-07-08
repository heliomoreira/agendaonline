@extends('layouts.app')
@section('content')

    @php
        // Data amigável + dia da semana (mesmo padrão da listagem)
        try {
            $date      = \Carbon\Carbon::parse($agenda->day)->locale('pt_PT');
            $dateLabel = $date->translatedFormat('d \d\e F \d\e Y');
            $weekday   = ucfirst($date->translatedFormat('l'));
        } catch (\Throwable $e) {
            $dateLabel = $agenda->day; $weekday = '';
        }

        // Iniciais do profissional
        $parts = array_values(array_filter(explode(' ', trim($agenda->professional->name ?? ''))));
        $proInitials = mb_strtoupper(
            mb_substr($parts[0] ?? '', 0, 1) .
            (count($parts) > 1 ? mb_substr(end($parts), 0, 1) : '')
        );
    @endphp

    {{-- ===== Cabeçalho ===== --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('agenda.list') }}" class="btn btn-icon btn-sm btn-outline-secondary" title="Voltar">
                    <i class="ti tabler-arrow-left"></i>
                </a>
                <h4 class="mb-0">Detalhe da Marcação</h4>
            </div>
            <small class="text-body-secondary ms-5 ps-2">#{{ $agenda->id }}</small>
        </div>

        <div class="d-flex gap-2">
            {{-- Ajustar a rota de edição ao que tiver (ex.: agenda.edit) --}}
            <a href="{{ route('agenda.form', $agenda) }}" class="btn btn-outline-primary">
                <i class="ti tabler-edit me-1"></i>Editar
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    @endif

    <div class="row g-6">

        {{-- ===== Coluna principal ===== --}}
        <div class="col-lg-8">

            {{-- Dados da marcação --}}
            <div class="card mb-6">
                <div class="card-header">
                    <h5 class="mb-0"><i class="ti tabler-calendar-event me-2"></i>Marcação</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <small class="text-body-secondary d-block mb-1">Serviço</small>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2">
                                    <span class="avatar-initial bg-label-primary rounded">
                                        <i class="ti tabler-briefcase"></i>
                                    </span>
                                </div>
                                <span class="fw-medium text-heading">{{ $agenda->service->name }}</span>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <small class="text-body-secondary d-block mb-1">Profissional</small>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2">
                                    <span class="avatar-initial bg-label-secondary rounded-circle">{{ $proInitials }}</span>
                                </div>
                                <span class="fw-medium text-heading">{{ $agenda->professional->name }}</span>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <small class="text-body-secondary d-block mb-1">Data</small>
                            <span class="text-heading">{{ $dateLabel }}</span>
                            @if($weekday)<small class="text-body-secondary d-block">{{ $weekday }}</small>@endif
                        </div>

                        <div class="col-sm-6">
                            <small class="text-body-secondary d-block mb-1">Horário</small>
                            <span class="badge bg-label-info rounded-pill">
                                <i class="ti tabler-clock me-1"></i>{{ $agenda->start_hour }}h – {{ $agenda->end_hour }}h
                            </span>
                        </div>

                        {{-- Campos opcionais: descomente se existirem no seu model/BD --}}
                        {{-- <div class="col-sm-6">
                            <small class="text-body-secondary d-block mb-1">Duração</small>
                            <span class="text-heading">{{ $agenda->service->duration ?? '—' }} min</span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-body-secondary d-block mb-1">Preço</small>
                            <span class="text-heading">{{ number_format($agenda->service->price ?? 0, 2, ',', '.') }} €</span>
                        </div> --}}
                    </div>

                    @if(!empty($agenda->notes))
                        <hr class="my-4">
                        <small class="text-body-secondary d-block mb-1">Observações</small>
                        <p class="mb-0 text-heading">{{ $agenda->notes }}</p>
                    @endif
                </div>
            </div>

            {{-- Cliente --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="ti tabler-user me-2"></i>Cliente</h5>
                </div>
                <div class="card-body">
                    @if($agenda->client)
                        <div class="row g-4">
                            <div class="col-sm-6">
                                <small class="text-body-secondary d-block mb-1">Nome</small>
                                <span class="fw-medium text-heading">{{ $agenda->client->name }}</span>
                            </div>
                            @if(!empty($agenda->client->email))
                                <div class="col-sm-6">
                                    <small class="text-body-secondary d-block mb-1">Email</small>
                                    <a href="mailto:{{ $agenda->client->email }}" class="text-heading">{{ $agenda->client->email }}</a>
                                </div>
                            @endif
                            @if(!empty($agenda->client->phone))
                                <div class="col-sm-6">
                                    <small class="text-body-secondary d-block mb-1">Telefone</small>
                                    <a href="tel:{{ $agenda->client->phone }}" class="text-heading">{{ $agenda->client->phone }}</a>
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="mb-0 text-body-secondary">Marcação sem cliente associado.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- ===== Coluna lateral: pagamento ===== --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="ti tabler-cash me-2"></i>Pagamento</h5>
                </div>
                <div class="card-body text-center">
                    @php $ps = $agenda->paymentStatus; @endphp

                    <span class="badge bg-label-{{ $ps->color ?? 'secondary' }} rounded-pill fs-6 px-3 py-2 mb-3">
                        {{ $ps->name ?? 'Sem estado' }}
                    </span>

                    @if($agenda->paid)
                        <p class="text-body-secondary small mb-3">
                            <i class="ti tabler-check me-1"></i>
                            Pago em {{ \Carbon\Carbon::parse($agenda->paid)->translatedFormat('d/m/Y') }}
                        </p>
                    @endif

                    {{-- Alterar estado: um clique por estado, sem JS extra --}}
                    <div class="dropdown d-grid">
                        <button class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti tabler-refresh me-1"></i>Alterar estado
                        </button>
                        <ul class="dropdown-menu w-100">
                            @foreach($paymentStatuses as $status)
                                <li>
                                    <form method="POST" action="{{ route('agenda.payment', $agenda) }}" class="d-grid">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="payment_status_id" value="{{ $status->id }}">
                                        <button type="submit"
                                                class="dropdown-item d-flex align-items-center justify-content-between {{ optional($ps)->id === $status->id ? 'active' : '' }}">
                                            <span>
                                                <span class="badge bg-label-{{ $status->color }} rounded-circle p-1 me-2">&nbsp;</span>
                                                {{ $status->name }}
                                            </span>
                                            @if(optional($ps)->id === $status->id)
                                                <i class="ti tabler-check"></i>
                                            @endif
                                        </button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
