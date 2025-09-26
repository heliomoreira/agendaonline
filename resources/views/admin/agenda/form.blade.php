@extends('layouts.app')
@section('content')
    @if (session('success'))
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-solid-success alert-dismissible fade show d-flex align-items-center"
                     role="alert">
            <span class="alert-icon rounded me-2">
                <i class="icon-base ti tabler-check icon-md"></i>
            </span>
                    <div class="flex-grow-1">
                        {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close ms-2" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            </div>
        </div>
    @endif
    @if ($errors->any())
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-solid-danger d-flex align-items-center" role="alert">
            <span class="alert-icon rounded">
              <i class="icon-base ti tabler-x"></i>
            </span>
                    <div>
                        <strong>Existem alguns erros:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="row mb-2">
        <div class="col-md-12">
            <h5 class="d-flex align-items-center gap-2 m-0">
                <i class="icon-base ti tabler-calendar"></i>
                {{ $agenda->id ? 'Editar Agendamento' : 'Novo Agendamento' }}
            </h5>
            <hr class="my-2"/>
        </div>
    </div>
    <div class="row g-6">
        <div class="col-md-12">
            @if(!$agenda->id)
                {{ html()->modelForm($agenda, 'POST', route('book.slot'))->open() }}
            @else
                {{ html()->modelForm($agenda, 'PUT', route('agenda.update', $agenda->id))->open() }}
            @endif
            {{ html()->token() }}
            <div class="card">
                <div class="card-header header-elements">
                    <h5 class="mb-0 me-2">Detalhe da Marcação</h5>
                </div>
                <div class="card-body">
                    <div class="row g-6">
                        <div class="col-md-3">
                            <label class="form-label" for="name">Serviço</label>
                            <select id="service_id" name="service_id" class="form-select" required>
                                <option value="">Selecionar...</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="name">Profissional</label>
                            <select id="professional_id" name="professional_id" class="form-select" disabled>
                                <option value="">Todos</option>
                                @foreach($professionals as $pro)
                                    <option value="{{ $pro->id }}">{{ $pro->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="day" class="form-label">Dia</label>
                            <input type="date" id="day" name="day" class="form-control" disabled required>
                        </div>
                        <div class="col-md-3">
                            <label for="time_select" class="form-label">Horários Disponíveis</label>
                            <select id="time_select" name="start_hour" class="form-select" disabled required>
                                <option value="">Selecionar horário...</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 pt-4">
                            <h5 class="mb-2 border-bottom pb-1">
                                <i class="icon-base ti tabler-user"></i>
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

                        <div class="col-md-3">
                            <label for="client_phone_1" class="form-label">Telemóvel</label>
                            <input type="text" id="client_phone_1" name="client_phone_1" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary waves-effect waves-light" id="submitBtn">
                            <i class="icon-base ti tabler-device-floppy"></i> Gravar
                        </button>
                        <a href="{{ route('products.index') }}" class="btn btn-secondary waves-effect waves-light">
                            <i class="icon-base ti tabler-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
            </div>
            {{html()->closeModelForm()}}
        </div>
    </div>
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
