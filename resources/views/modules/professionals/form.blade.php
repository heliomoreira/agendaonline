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
                <i class="icon-base ti tabler-user"></i>
                {{ $professional->id ? 'Editar Profissional' : 'Novo Profissional' }}
                {!! $professional->name ? '| <span style="color:#2A7AD4">' . $professional->name . '</span>': ''  !!}
            </h5>
            <hr class="my-2"/>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="nav-align-top nav-tabs-shadow">
                <ul class="nav nav-tabs nav-fill" role="tablist">
                    <li class="nav-item">
                        <button
                            type="button"
                            class="nav-link active"
                            role="tab"
                            data-bs-toggle="tab"
                            data-bs-target="#navs-justified-home"
                            aria-controls="navs-justified-home"
                            aria-selected="true">
                          <span class="d-none d-sm-inline-flex align-items-center">
                            <i class="icon-base ti tabler-home icon-sm me-1_5"></i>Info
                            <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-danger ms-1_5"
                            >3</span
                            >
                          </span>
                            <i class="icon-base ti tabler-home icon-sm d-sm-none"></i>
                        </button>
                    </li>
                    @if(isset($professional->id))
                        <li class="nav-item">
                            <button
                                type="button"
                                class="nav-link"
                                role="tab"
                                data-bs-toggle="tab"
                                data-bs-target="#navs-justified-services"
                                aria-controls="navs-justified-services"
                                aria-selected="false">
                          <span class="d-none d-sm-inline-flex align-items-center"
                          ><i class="icon-base ti tabler-message-dots icon-sm me-1_5"></i>Serviços</span
                          >
                                <i class="icon-base ti tabler-message-dots icon-sm d-sm-none"></i>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button
                                type="button"
                                class="nav-link"
                                role="tab"
                                data-bs-toggle="tab"
                                data-bs-target="#navs-justified-profile"
                                aria-controls="navs-justified-profile"
                                aria-selected="false">
                          <span class="d-none d-sm-inline-flex align-items-center"
                          ><i class="icon-base ti tabler-user icon-sm me-1_5"></i>Horário de Trabalho</span
                          >
                                <i class="icon-base ti tabler-user icon-sm d-sm-none"></i>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button
                                type="button"
                                class="nav-link"
                                role="tab"
                                data-bs-toggle="tab"
                                data-bs-target="#navs-justified-messages"
                                aria-controls="navs-justified-messages"
                                aria-selected="false">
                          <span class="d-none d-sm-inline-flex align-items-center">
                              <i class="icon-base ti tabler-cancel icon-sm me-1_5"></i>Ausências
                          </span>
                            </button>
                        </li>
                    @endif
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="navs-justified-home" role="tabpanel">
                        @include('modules.professionals.partials.info')
                    </div>
                    <div class="tab-pane fade" id="navs-justified-services" role="tabpanel">
                        @if(isset($professional->id))
                            @include('modules.professionals.partials.services')
                        @endif
                    </div>
                    <div class="tab-pane fade" id="navs-justified-profile" role="tabpanel">
                        @if(isset($professional->id))
                            @include('modules.professionals.partials.working_hours')
                        @endif
                    </div>
                    <div class="tab-pane fade" id="navs-justified-messages" role="tabpanel">
                        @if(isset($professional->id))
                            @include('modules.professionals.partials.unavailabilities')
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        let whIndex = {{ isset($workingHours) ? $workingHours->count() : 0 }};

        document.getElementById('add-hour-block').addEventListener('click', function () {
            const weekday = document.getElementById('weekday-select').value;
            const weekdayName = {
                1:'Segunda-feira',2:'Terça-feira',3:'Quarta-feira',4:'Quinta-feira',5:'Sexta-feira',6:'Sábado',0:'Domingo'
            }[weekday];
            const startHour = document.getElementById('start-hour').value;
            const endHour = document.getElementById('end-hour').value;

            if (!startHour || !endHour) { alert('Preenche ambas as horas.'); return; }

            const dayGroupId = `day-group-${weekday}`;
            let dayGroup = document.getElementById(dayGroupId);
            if (!dayGroup) {
                dayGroup = document.createElement('div');
                dayGroup.id = dayGroupId;
                dayGroup.className = 'mb-4';
                dayGroup.innerHTML = `<h6 class="mb-3">${weekdayName}</h6><div class="day-group-rows"></div><hr>`;
                document.getElementById('working-hours-container').appendChild(dayGroup);
            }

            const idx = whIndex++;
            const row = document.createElement('div');
            row.className = 'row g-3 align-items-center mb-2';
            row.innerHTML = `
            <div class="col-md-3" style="display: none;">
                <input type="text" class="form-control-plaintext" value="${weekdayName}" readonly>
                <input type="hidden" name="working_hours[${idx}][weekday]" value="${weekday}">
            </div>
            <div class="col-md-3">
                <input type="time" name="working_hours[${idx}][start_hour]" class="form-control" value="${startHour}" readonly>
            </div>
            <div class="col-md-3">
                <input type="time" name="working_hours[${idx}][end_hour]" class="form-control" value="${endHour}" readonly>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-sm btn-danger remove-block">Remover</button>
            </div>
        `;
            dayGroup.querySelector('.day-group-rows').appendChild(row);

            document.getElementById('start-hour').value = '';
            document.getElementById('end-hour').value = '';
        });

        document.getElementById('working-hours-container').addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-block')) {
                const row = e.target.closest('.row');
                const group = row.closest('.mb-4');
                row.remove();
                if (group.querySelector('.day-group-rows').children.length === 0) group.remove();
            }
        });
    </script>



    <script>
        document.getElementById('select_all_services').addEventListener('change', function () {
            const checkboxes = document.querySelectorAll('.service-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    </script>
@endpush
