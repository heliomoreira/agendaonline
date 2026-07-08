@extends('client.layout')

@section('title', 'A minha conta')

@section('content')
    <div class="container py-5">
        <h1 class="h3 mb-1">Olá, {{ $client->name }}!</h1>
        <p class="text-muted">Aqui pode acompanhar as suas marcações.</p>

        <div class="row mt-4 g-4">
            <div class="col-lg-8">
                <h5 class="mb-3">Próximas Marcações</h5>

                @forelse($upcomingBookings as $booking)
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-2">{{ $booking->service?->name ?? 'Serviço' }}</h6>
                                    <p class="text-muted mb-1">
                                        <i class="ri-calendar-line"></i>
                                        {{ $booking->formatted_date }}
                                    </p>
                                    <p class="text-muted mb-1">
                                        <i class="ri-time-line"></i>
                                        {{ $booking->time_range }}
                                    </p>
                                    @if($booking->professional)
                                        <p class="text-muted mb-0">
                                            <i class="ri-user-line"></i>
                                            {{ $booking->professional->name }}
                                        </p>
                                    @endif
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-{{ $booking->status_color }} mb-2">
                                        {{ $booking->status_label }}
                                    </span>
                                    @if($booking->canBeCancelled())
                                        <form action="{{ route('client.bookings.cancel', $booking->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Cancelar esta marcação?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="ri-close-line"></i> Cancelar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card mb-3">
                        <div class="card-body text-center text-muted py-5">
                            <i class="ri-calendar-line" style="font-size: 2rem;"></i>
                            <p class="mb-0 mt-2">Não tem marcações agendadas.</p>
                        </div>
                    </div>
                @endforelse

                <a href="{{ route('book.index') }}" class="btn btn-primary">
                    <i class="ri-add-line"></i> Nova Marcação
                </a>

                @if($pastBookings->count())
                    <h5 class="mt-5 mb-3">Marcações Recentes</h5>
                    <div class="card">
                        <ul class="list-group list-group-flush">
                            @foreach($pastBookings as $booking)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="fw-medium">{{ $booking->service?->name ?? 'Serviço' }}</span>
                                        <small class="text-muted d-block">
                                            {{ $booking->formatted_date }} · {{ $booking->time_range }}
                                        </small>
                                    </div>
                                    <span class="badge bg-{{ $booking->status_color }}">
                                        {{ $booking->status_label }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-3">O meu perfil</h6>
                        <p class="mb-1"><strong>Nome:</strong> {{ $client->name }}</p>
                        <p class="mb-1"><strong>Email:</strong> {{ $client->email }}</p>
                        <p class="mb-0"><strong>Telefone:</strong> {{ $client->phone_1 }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
