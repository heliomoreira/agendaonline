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
            <a href="#" class="btn btn-primary">
                <i class="ti tabler-plus me-2"></i> Novo Agendamento
            </a>
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
@endsection
@push('scripts')
    <script src="{{ asset('assets/plugins/fullcalendar/index.global.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales-all.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let calendarEl = document.getElementById('calendar');

            const allEvents = [
                {
                    id: 1,
                    title: 'Corte de Cabelo Homem',
                    start: '2025-06-15T10:00:00',
                    end: '2025-06-15T12:00:00',
                    category: 'cat1',
                    color: '#3498db',
                    extendedProps: {
                        cliente: 'João Silva',
                        tecnico: 'Ana Costa',
                        local: 'Loja 1',
                        notas: 'Verificar disco rígido'
                    }
                },
                {
                    id: 2,
                    title: 'Coloração de cabelo',
                    start: '2025-06-16T10:00:00',
                    end: '2025-06-16T12:00:00',
                    category: 'cat2',
                    color: '#2ecc71',
                    extendedProps: {
                        cliente: 'Maria Fonseca',
                        tecnico: 'Carlos Dias',
                        local: 'Loja 2',
                        notas: 'Atualização de software'
                    }
                }
            ];

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
                events: allEvents,
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
                eventContent: function(arg) {
                    const cliente = arg.event.extendedProps.cliente || '';
                    const tecnico = arg.event.extendedProps.tecnico || '';

                    return {
                        html:
                            '<div style="font-weight: bold">' + arg.event.title + '</div>' +
                            '<div style="font-size: 12px;">Cliente: ' + cliente + '</div>' +
                            '<div style="font-size: 12px;">Técnico: ' + tecnico + '</div>'
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

        });
    </script>
@endpush
