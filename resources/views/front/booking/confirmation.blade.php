@extends('layouts.front')

@section('content')
    <div class="text-center py-5">
        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
        <h2 class="mt-3 text-success">Marcação Confirmada com Sucesso!</h2>
        <p class="lead mt-3">Recebeu um email com todos os detalhes da sua marcação.</p>

        <div class="card mx-auto mt-4 shadow rounded-4 border-0" style="max-width: 600px;">
            <div class="card-body">
                <h5 class="mb-3 border-bottom pb-2">
                    <i class="bi bi-info-circle me-1 text-primary"></i> Detalhes da Marcação
                </h5>

                <ul class="list-group list-group-flush text-start">
                    <li class="list-group-item"><strong>Nome:</strong> {{ $booking->client->name }}</li>
                    <li class="list-group-item"><strong>Email:</strong> {{ $booking->client->email }}</li>
                    <li class="list-group-item"><strong>Telemóvel:</strong> {{ $booking->client->phone_1 }}</li>
                    <li class="list-group-item"><strong>Serviço:</strong> {{ $booking->service->name }}</li>
                    <li class="list-group-item"><strong>Profissional:</strong> {{ $booking->professional->name ?? 'N/A' }}</li>
                    <li class="list-group-item"><strong>Data:</strong> {{ \Carbon\Carbon::parse($booking->day)->format('d/m/Y') }}</li>
                    <li class="list-group-item"><strong>Hora:</strong> {{ $booking->start_hour }}h</li>
                    @if($booking->notes)
                        <li class="list-group-item"><strong>Notas:</strong> {{ $booking->notes }}</li>
                    @endif
                </ul>
            </div>
        </div>

        <a href="{{ route('book.index') }}" class="btn btn-outline-primary mt-4">
            <i class="bi bi-arrow-left-circle me-1"></i>
            Fazer nova marcação
        </a>
    </div>
@endsection

@push('scripts')
@endpush
