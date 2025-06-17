@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">Check Available Time Slots</h2>

        <form id="slotForm" class="row g-3">
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
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('slotForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const serviceId = document.getElementById('service_id').value;
            const professionalId = document.getElementById('professional_id').value;
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;

            if (!serviceId || !startDate || !endDate) {
                alert('Please fill in all required fields.');
                return;
            }

            const params = new URLSearchParams({
                service_id: serviceId,
                start_date: startDate,
                end_date: endDate
            });
            if (professionalId) params.append('professional_id', professionalId);

            fetch(`/available-slots?${params.toString()}`)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('slots-result');
                    container.innerHTML = '';

                    if (data.length === 0) {
                        container.innerHTML = '<p>No available slots found for the selected criteria.</p>';
                        return;
                    }

                    data.forEach(slot => {
                        const slotCard = document.createElement('div');
                        slotCard.classList.add('col-md-4', 'mb-3');
                        slotCard.innerHTML = `
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">${slot.professional_name}</h5>
                            <h6 class="card-subtitle mb-2 text-muted">${slot.day}</h6>
                            <p class="card-text">
                                ${slot.available_slots.map(time => `
                                    <button class="btn btn-outline-success btn-sm book-btn mt-1"
                                            data-day="${slot.day}"
                                            data-time="${time}"
                                            data-professional="${slot.professional_id}">
                                        ${time}
                                    </button>
                                `).join('')}
                            </p>
                        </div>
                    </div>`;
                        container.appendChild(slotCard);
                    });
                })
                .catch(err => {
                    console.error(err);
                    alert('Something went wrong while fetching available slots.');
                });
        });
    </script>

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
    </div>

@endpush
