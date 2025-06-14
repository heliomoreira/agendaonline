@extends('layouts.app')

@section('content')
    <div class="row align-items-end mb-3">
        <div class="col-md-3">
            <select id="categoryFilter" class="form-select">
                <option value="">Todas as Categorias</option>
                <option value="cat1">Consultas</option>
                <option value="cat2">Manutenção</option>
                <option value="cat3">Urgência</option>
            </select>
        </div>
        <div class="col-md-9 text-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newEventModal">
                <i class="ti tabler-plus me-2"></i> Novo Agendamento
            </button>
        </div>
    </div>
    <div class="row g-6">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div id='calendar'></div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalTitle">Detalhes do Evento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Início:</strong> <span id="eventStart"></span></p>
                    <p><strong>Fim:</strong> <span id="eventEnd"></span></p>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="newEventModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            {{ html()->form('POST', route('agenda.store'))->class('modal-content')->open() }}
            {{ html()->token() }}
            <div class="modal-header">
                <h5 class="modal-title">Novo Agendamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="service_id" class="form-label">Serviço</label>
                    {{ html()->select('service_id')
                        ->options($services)
                        ->class('form-select')
                        ->id('service_id')
                        ->placeholder('-- Selecionar --') }}
                </div>

                <div class="mb-3">
                    <label for="professional_id" class="form-label">Profissional</label>
                    {{ html()->select('professional_id')
                        ->options([])
                        ->class('form-select')
                        ->id('professional_id')
                        ->placeholder('-- Selecionar um serviço primeiro --')
                        ->disabled() }}
                </div>

                <div class="mb-3">
                    <label for="client_id" class="form-label">Cliente</label>
                    {{html()->select('client_id')->options($clients)->class('form-select')->placeholder('-- Seleccionar --')}}
                </div>

                <div class="mb-3">
                    <label for="day" class="form-label">Data</label>
                    <input type="date" id="day" name="day" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="start_hour" class="form-label">Hora de Início</label>
                    <input type="time" id="start_hour" name="start_hour" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="end_hour" class="form-label">Hora de Fim</label>
                    <input type="time" id="end_hour" name="end_hour" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Notas</label>
                    <textarea id="notes" name="notes" class="form-control" rows="3"></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Guardar Agendamento</button>
            </div>
            </form>
        </div>
    </div>
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    Cor do profissional atualizada com sucesso!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('assets/plugins/fullcalendar/index.global.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales-all.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let calendarEl = document.getElementById('calendar');

            let calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                locale: 'pt',
                height: 'auto',
                slotMinTime: '08:00:00',
                slotMaxTime: '20:00:00',
                allDaySlot: false,
                titleFormat: {year: 'numeric', month: 'long'},
                allDayText: 'Dia inteiro',
                noEventsContent: 'Sem eventos a mostrar',
                events: '/agenda/get-events',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
                },
                buttonText: {
                    today: 'Hoje',
                    month: 'Mês',
                    week: 'Semana',
                    day: 'Dia',
                    list: 'Agenda'
                },
                eventContent: function (arg) {
                    const client = arg.event.extendedProps.client || '';
                    const professional = arg.event.extendedProps.professional || '';

                    return {
                        html:
                            '<div style="font-weight: bold">' + arg.event.title + '</div>' +
                            '<div style="font-size: 12px;">Cliente: ' + client + '</div>'
                          /*  '<div style="font-size: 12px;">Técnico: ' + professional + '</div>'*/
                    };
                },
                eventClick: function (info) {
                    document.getElementById('eventModalTitle').innerText = info.event.title;
                    document.getElementById('eventStart').innerText = info.event.start.toLocaleString();
                    document.getElementById('eventEnd').innerText = info.event.end?.toLocaleString() ?? '—';

                    var modal = new bootstrap.Modal(document.getElementById('eventModal'));
                    modal.show();
                }
            });
            calendar.render();

            const filterSelect = document.getElementById('categoryFilter');
            filterSelect.addEventListener('change', function () {
                const selected = this.value;

                const filteredEvents = selected
                    ? allEvents.filter(e => e.category === selected)
                    : allEvents;

                calendar.removeAllEvents();
                calendar.addEventSource(filteredEvents);
            });

            @if(session('success'))
            const toastEl = document.getElementById('successToast');
            const toast = new bootstrap.Toast(toastEl);
            toast.show();
            @endif
        });

        document.addEventListener('DOMContentLoaded', function () {
            const serviceSelect = document.getElementById('service_id');
            const professionalSelect = document.getElementById('professional_id');

            serviceSelect.addEventListener('change', function () {
                const serviceId = this.value;
                professionalSelect.innerHTML = '<option value="">A carregar...</option>';
                professionalSelect.disabled = true;

                if (!serviceId) {
                    professionalSelect.innerHTML = '<option value="">Selecione um serviço primeiro</option>';
                    return;
                }

                fetch(`/services/${serviceId}/professionals`)
                    .then(response => response.json())
                    .then(data => {
                        let options = '<option value="">Selecione</option>';
                        data.forEach(pro => {
                            options += `<option value="${pro.id}">${pro.name}</option>`;
                        });
                        professionalSelect.innerHTML = options;
                        professionalSelect.disabled = false;
                    })
                    .catch(() => {
                        professionalSelect.innerHTML = '<option value="">Erro ao carregar profissionais</option>';
                    });
            });
        });
    </script>
@endpush
