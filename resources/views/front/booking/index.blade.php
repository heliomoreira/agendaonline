@extends('layouts.front')

@section('content')
    <div class="container">
        <form id="slotForm" class="row g-3">
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
                <label for="professional_id" class="form-label">Professional (opcional)</label>
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

            <div class="col-12 text-end">
                <button type="submit" id="submitBtn" class="btn btn-primary mt-3" disabled>
                    Confirmar Marcação
                </button>
            </div>
        </form>

        {{--<form id="slotForm" class="row g-3">
            <div class="col-md-4">
                <label for="service_id" class="form-label">Service</label>
                <select id="service_id" name="service_id" class="form-select" required>
                    <option value="">Choose...</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label for="professional_id" class="form-label">Professional (optional)</label>
                <select id="professional_id" name="professional_id" class="form-select">
                    <option value="">Any</option>

                    @foreach($professionals as $pro)
                        <option value="{{ $pro->id }}">{{ $pro->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="form-control" required>
            </div>

            <div class="col-md-2">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" name="end_date" id="end_date" class="form-control" required>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary">Search Slots</button>
            </div>
        </form>

        <hr>

        <div id="slots-container">
            <h4>Available Slots</h4>
            <div id="slots-result" class="row"></div>
        </div>--}}
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

            serviceSelect.addEventListener('change', () => {
                const hasService = serviceSelect.value !== '';
                professionalSelect.disabled = !hasService;
                dayInput.disabled = !hasService;

                timeSelect.disabled = true;
                timeSelect.innerHTML = '<option value="">Selecionar horário...</option>';
                updateSubmitButtonState();
            });

            professionalSelect.addEventListener('change', function () {
                timeSelect.disabled = true;
                timeSelect.innerHTML = '<option value="">Selecionar horário...</option>';

                const hasService = serviceSelect.value !== '';
                const hasDay = dayInput.value !== '';

                if (hasService && hasDay) {
                    loadAvailableSlots();
                }

                updateSubmitButtonState();
            });

            dayInput.addEventListener('change', function () {
                const hasService = serviceSelect.value !== '';
                const hasDay = dayInput.value !== '';

                if (hasService && hasDay) {
                    loadAvailableSlots();
                } else {
                    timeSelect.disabled = true;
                    timeSelect.innerHTML = '<option value="">Selecionar horário...</option>';
                }

                updateSubmitButtonState();
            });

            timeSelect.addEventListener('change', updateSubmitButtonState);

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
                        timeSelect.innerHTML = '<option value="">Selecionar horário...</option>';
                        const seen = new Set();

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

                        timeSelect.disabled = seen.size === 0;
                        updateSubmitButtonState();
                    })
                    .catch(err => {
                        console.error(err);
                        timeSelect.innerHTML = '<option>Error loading slots</option>';
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


    {{--

        <!-- Booking Modal -->
        <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="bookingForm" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="bookingModalLabel">Confirm Booking</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="service_id">
                        <input type="hidden" name="professional_id">
                        <input type="hidden" name="day">
                        <input type="hidden" name="start_hour">
                        <input type="hidden" name="client_id" value="1"> <!-- mock client -->

                        <p><strong>Service:</strong> <span id="modalService"></span></p>
                        <p><strong>Professional:</strong> <span id="modalProfessional"></span></p>
                        <p><strong>Date:</strong> <span id="modalDate"></span></p>
                        <p><strong>Time:</strong> <span id="modalTime"></span></p>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes (optional)</label>
                            <textarea name="notes" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Book</button>
                    </div>
                </form>
            </div>
        </div>--}}

@endpush
