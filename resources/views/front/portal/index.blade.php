@extends('layouts.front')

@section('content')
    <form id="slotForm" action="{{ route('book.slot') }}" method="POST" class="row g-2">
        @csrf
        {!! RecaptchaV3::field('register') !!}
        {{-- Secção: Marcação --}}
        <div class="col-12">
            <h5 class="mb-2 border-bottom pb-1">
                <i class="bi bi-calendar-check me-2 text-primary"></i>
                Informações da Marcação
            </h5>
        </div>

        <div class="col-md-6">
            <label for="service_id" class="form-label">Serviço</label>
            <select id="service_id" name="service_id" class="form-select" required>
                <option value="">Selecionar...</option>
                @foreach($services as $service)
                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label for="professional_id" class="form-label">Profissional (opcional)</label>
            <select id="professional_id" name="professional_id" class="form-select" disabled>
                <option value="">Todos</option>
                @foreach($professionals as $pro)
                    <option value="{{ $pro->id }}">{{ $pro->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label for="day" class="form-label">Dia</label>
            <input type="date" id="day" name="day" class="form-control" disabled required>
        </div>

        <div class="col-md-6">
            <label for="time_select" class="form-label">Horários Disponíveis</label>
            <select id="time_select" name="start_hour" class="form-select" disabled required>
                <option value="">Selecionar horário...</option>
            </select>
        </div>

        {{-- Secção: Cliente --}}
        <div class="col-12 pt-4">
            <h5 class="mb-2 border-bottom pb-1">
                <i class="bi bi-person-circle me-2 text-success"></i>
                Dados do Cliente
            </h5>
        </div>

        <div class="col-md-4">
            <label for="client_name" class="form-label">Nome</label>
            <input type="text" id="client_name" name="client_name" class="form-control" required>
        </div>

        <div class="col-md-4">
            <label for="client_email" class="form-label">Email</label>
            <input type="email" id="client_email" name="client_email" class="form-control" required>
        </div>

        <div class="col-md-4">
            <label for="client_phone_1" class="form-label">Telemóvel</label>
            <input type="text" id="client_phone_1" name="client_phone_1" class="form-control" required>
        </div>

        <div class="col-12">
            <label for="notes" class="form-label">Notas (opcional)</label>
            <textarea id="notes" name="notes" class="form-control" rows="2"></textarea>
        </div>
        {{-- Submeter --}}
        <div class="col-12 text-end">
            <button type="submit" id="submitBtn" class="btn btn-primary mt-4" disabled>
                <i class="bi bi-check-circle me-1"></i>
                Confirmar Marcação
            </button>
        </div>
    </form>
@endsection


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const serviceSelect = document.getElementById('service_id');
            const professionalSelect = document.getElementById('professional_id');
            const dayInput = document.getElementById('day');
            const timeSelect = document.getElementById('time_select');
            const submitBtn = document.getElementById('submitBtn');

            professionalSelect.disabled = true;
            dayInput.disabled = true;
            timeSelect.disabled = true;
            submitBtn.disabled = true;

            // Quando muda o serviço
            serviceSelect.addEventListener('change', () => {
                const serviceId = serviceSelect.value;
                const hasService = serviceId !== '';

                // Reset campos seguintes
                professionalSelect.innerHTML = '<option value="">Todos</option>';
                timeSelect.innerHTML = '<option value="">Selecionar horário...</option>';
                timeSelect.disabled = true;

                if (hasService) {
                    // Carregar profissionais para o serviço
                    fetch(`/services/${serviceId}/professionals`)
                        .then(res => res.json())
                        .then(data => {
                            data.forEach(pro => {
                                const option = document.createElement('option');
                                option.value = pro.id;
                                option.text = pro.name;
                                professionalSelect.appendChild(option);
                            });

                            professionalSelect.disabled = false;
                            dayInput.disabled = false;
                        })
                        .catch(err => {
                            console.error('Erro ao carregar profissionais:', err);
                            professionalSelect.disabled = true;
                            dayInput.disabled = true;
                        });
                } else {
                    professionalSelect.disabled = true;
                    dayInput.disabled = true;
                }

                updateSubmitButtonState();
            });

            // Quando muda o profissional
            professionalSelect.addEventListener('change', () => {
                clearAndLoadSlotsIfPossible();
            });

            // Quando muda o dia
            dayInput.addEventListener('change', () => {
                clearAndLoadSlotsIfPossible();
            });

            // Quando muda o horário
            timeSelect.addEventListener('change', updateSubmitButtonState);

            function clearAndLoadSlotsIfPossible() {
                timeSelect.disabled = true;
                timeSelect.innerHTML = '<option value="">Selecionar horário...</option>';

                const hasService = serviceSelect.value !== '';
                const hasDay = dayInput.value !== '';

                if (hasService && hasDay) {
                    loadAvailableSlots();
                }

                updateSubmitButtonState();
            }

            function loadAvailableSlots() {
                const serviceId = serviceSelect.value;
                const professionalId = professionalSelect.value;
                const day = dayInput.value;

                if (!serviceId || !day) return;

                timeSelect.disabled = true;
                timeSelect.innerHTML = '<option>A carregar...</option>';

                const params = new URLSearchParams({
                    service_id: serviceId,
                    start_date: day,
                    end_date: day
                });
                if (professionalId) params.append('professional_id', professionalId);

                fetch(`/available-slots?${params.toString()}`)
                    .then(res => res.json())
                    .then(data => {
                        const seen = new Set();
                        timeSelect.innerHTML = '';

                        data.forEach(entry => {
                            entry.available_slots.forEach(time => {
                                if (!seen.has(time)) {
                                    seen.add(time);

                                    let label = time;
                                    if (!professionalId && entry.professional_name) {
                                        label += ` (${entry.professional_name})`;
                                    }

                                    const option = document.createElement('option');
                                    option.value = time;
                                    option.text = label;
                                    option.setAttribute('data-professional-id', entry.professional_id);
                                    timeSelect.appendChild(option);
                                }
                            });
                        });

                        if (seen.size === 0) {
                            const option = document.createElement('option');
                            option.value = 'none';
                            option.text = 'Sem horários disponíveis';
                            timeSelect.appendChild(option);
                            timeSelect.disabled = true;
                        } else {
                            const defaultOption = document.createElement('option');
                            defaultOption.value = '';
                            defaultOption.text = 'Selecionar horário...';
                            defaultOption.selected = true;
                            defaultOption.disabled = true;
                            timeSelect.insertBefore(defaultOption, timeSelect.firstChild);
                            timeSelect.disabled = false;
                        }

                        updateSubmitButtonState();
                    })
                    .catch(err => {
                        console.error('Erro ao carregar horários:', err);
                        timeSelect.innerHTML = '<option>Erro a carregar horários</option>';
                        timeSelect.disabled = true;
                        updateSubmitButtonState();
                    });
            }

            function updateSubmitButtonState() {
                const serviceValid = serviceSelect.value !== '';
                const dayValid = dayInput.value !== '';
                const timeValid = timeSelect.value !== '' && !timeSelect.disabled;

                submitBtn.disabled = !(serviceValid && dayValid && timeValid);
            }
        });
    </script>
@endpush
