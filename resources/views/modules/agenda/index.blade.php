@extends('layouts.app')

@section('content')
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
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                locale: 'pt',
                height: 'auto',
                slotMinTime: '08:00:00',
                slotMaxTime: '20:00:00',
                allDaySlot: false,
                titleFormat: {year: 'numeric', month: 'long'},
                allDayText: 'Dia inteiro',
                noEventsContent: 'Sem eventos a mostrar',
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
                events: [
                    {
                        title: 'Reparação - PC João',
                        start: '2025-06-17T09:00:00',
                        end: '2025-06-17T12:00:00',
                        color: '#3498db'
                    },
                    {
                        title: 'Instalação - Impressora Ana',
                        start: '2025-06-17T11:30:00',
                        end: '2025-06-17T12:30:00',
                        color: '#3498db'
                    },
                    {
                        title: 'Formatação - Laptop Pedro',
                        start: '2025-06-18T14:00:00',
                        end: '2025-06-18T15:00:00',
                        color: '#e74c3c'
                    }
                ],
                eventClick: function(info) {
                    document.getElementById('eventModalTitle').innerText = info.event.title;
                    document.getElementById('eventStart').innerText = info.event.start.toLocaleString();
                    document.getElementById('eventEnd').innerText = info.event.end?.toLocaleString() ?? '—';

                    var modal = new bootstrap.Modal(document.getElementById('eventModal'));
                    modal.show();
                }
            });
            calendar.render();
        });
    </script>
@endpush
