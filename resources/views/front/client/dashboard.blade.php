@extends('layouts.client')

@section('content')
    <div class="container py-5">
        <h1>Olá, {{ $client->name }}!</h1>

        <div class="row mt-4">
            <div class="col-md-8">
                <h3>Próximas Marcações</h3>

                @forelse($upcomingBookings as $booking)
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5>{{ $booking->service->name }}</h5>
                                    <p class="text-muted mb-1">
                                        <i class="ri-calendar-line"></i>
                                        {{ $booking->formatted_date }}
                                    </p>
                                    <p class="text-muted mb-1">
                                        <i class="ri-time-line"></i>
                                        {{ $booking->time_range }}
                                    </p>
                                    @if($booking->professional)
                                        <p class="text-muted">
                                            <i class="ri-user-line"></i>
                                            {{ $booking->professional->name }}
                                        </p>
                                    @endif
                                </div>
                                <div>
                                <span class="badge bg-{{ $booking->status_color }}">
                                    {{ $booking->status_label }}
                                </span>
                                    @if($booking->canBeCancelled())
                                        <a href="{{ route('client.bookings.show', $booking) }}"
                                           class="btn btn-sm btn-outline-primary mt-2">
                                            Ver detalhes
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted">Não tem marcações agendadas.</p>
                @endforelse

                <a href="{{ route('booking.index') }}" class="btn btn-primary">
                    <i class="ri-add-line"></i> Nova Marcação
                </a>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5>Perfil</h5>
                        <p><strong>Email:</strong> {{ $client->email }}</p>
                        <p><strong>Telefone:</strong> {{ $client->phone_1 }}</p>
                        <a href="{{ route('client.profile.edit') }}" class="btn btn-sm btn-outline-secondary">
                            Editar Perfil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
