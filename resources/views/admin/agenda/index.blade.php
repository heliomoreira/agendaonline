@extends('layouts.app')

@section('content')
    <div class="row align-items-end mb-3">
        <div class="col-md-3">
            {{ html()->select('categoryFilter')
                ->options($professionals)
            ->class('form-select')
            ->id('categoryFilter')
            ->placeholder('Todos os Profissionais') }}
        </div>
        <div class="col-md-9 text-end">
            <a href="{{ route('agenda.form') }}" class="btn btn-primary">
                <i class="ti tabler-plus me-2"></i> Novo Agendamento
            </a>
        </div>
    </div>

    <div class="row g-6">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Event details modal --}}
    <div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalTitle">Detalhes do Evento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="csrf-token" value="{{ csrf_token() }}">
                    <p style="display: none"><strong>ID:</strong> <span id="eventId"></span></p>
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 100px">Início</th>
                            <td><span id="eventStart"></span></td>
                        </tr>
                        <tr>
                            <th>Fim</th>
                            <td><span id="eventEnd"></span></td>
                        </tr>
                        <tr>
                            <th>Profissional</th>
                            <td><span id="eventProfessional"></span></td>
                        </tr>
                        <tr>
                            <th>Cliente</th>
                            <td><span id="eventCustomer"></span></td>
                        </tr>
                        <tr>
                            <th>Serviço</th>
                            <td><span id="eventService"></span></td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" onclick="cancelEvent();">Cancelar Marcação</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ global_asset('assets/plugins/fullcalendar/index.global.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');

            // holds the currently selected professional id ('' = all)
            let activeProfessional = '';

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                locale: 'pt',
                height: 'auto',
                slotMinTime: '07:00:00',
                slotMaxTime: '23:59:59',
                allDaySlot: false,
                titleFormat: {year: 'numeric', month: 'long'},
                allDayText: 'Dia inteiro',
                noEventsContent: 'Sem eventos a mostrar',
                eventDidMount: function (info) {
                    info.el.style.cursor = 'pointer';
                },
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

                // IMPORTANT: send start/end (and optional professional) to the backend
                events: function (fetchInfo, successCallback, failureCallback) {
                    const params = new URLSearchParams({
                        start: fetchInfo.startStr,   // e.g. 2025-09-01T00:00:00Z
                        end: fetchInfo.endStr        // e.g. 2025-09-08T00:00:00Z
                    });
                    if (activeProfessional) {
                        params.append('professional_id', activeProfessional);
                    }

                    fetch(`/admin/agenda/get-events?${params.toString()}`)
                        .then(res => {
                            if (!res.ok) throw new Error('Failed to load events');
                            return res.json();
                        })
                        .then(data => successCallback(data))
                        .catch(err => failureCallback(err));
                },

                eventContent: function (arg) {
                    const client = arg.event.extendedProps.client || '';
                    return {
                        html:
                            '<div style="font-weight: bold">' + arg.event.title + '</div>' +
                            '<div style="font-size: 12px;">Cliente: ' + client + '</div>'
                    };
                },

                eventClick: function (info) {
                    document.getElementById('eventModalTitle').innerHTML = info.event.title + " | " + "<span style='color:#000031'>" + info.event.extendedProps.client + "</span>";
                    document.getElementById('eventId').innerText = info.event.id ?? '—';
                    document.getElementById('eventStart').innerText = info.event.start?.toLocaleString() ?? '—';
                    document.getElementById('eventEnd').innerText = info.event.end?.toLocaleString() ?? '—';
                    document.getElementById('eventProfessional').innerText = info.event.extendedProps.professional || 'Não definido';
                    document.getElementById('eventCustomer').innerText = info.event.extendedProps.client || 'Não definido';
                    document.getElementById('eventService').innerText = info.event.extendedProps.service || 'Não definido';

                    const modal = new bootstrap.Modal(document.getElementById('eventModal'));
                    modal.show();
                }
            });

            calendar.render();

            // professional filter
            const filterSelect = document.getElementById('categoryFilter');
            filterSelect.addEventListener('change', function () {
                activeProfessional = this.value || '';
                calendar.refetchEvents(); // ask server again for current range + filter
            });
        });

        function cancelEvent() {
            const eventId = document.getElementById('eventId').textContent;
            const csrfToken = document.getElementById('csrf-token').value;

            if (!confirm('Tem a certeza que quer cancelar esta marcação?')) {
                return;
            }

            fetch(`/admin/agenda/cancel-event/${eventId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro ao cancelar o evento');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Evento cancelado com sucesso:', data);
                    alert('Marcação cancelada com sucesso!');
                    // Redirecionar ou atualizar a página
                    window.location.reload(); // ou window.location.href = '/admin/agenda';
                })
                .catch(error => {
                    console.error('Erro:', error);
                    //alert('Erro ao cancelar a marcação. Tente novamente.');
                });
        }
    </script>
@endpush
