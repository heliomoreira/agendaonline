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
                                data-bs-target="#navs-services"
                                aria-controls="navs-services"
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
                        @if(!$professional->id)
                            {{ html()->modelForm($professional, 'POST', route('professionals.store'))->open() }}
                        @else
                            {{ html()->modelForm($professional, 'PUT', route('professionals.update', $professional->id))->open() }}
                        @endif
                        <div class="row g-6">
                            <div class="col-md-4">
                                <label class="form-label" for="name">Nome</label>
                                {{html()->text('name')->id('name')->class('form-control')->placeholder('')}}
                            </div>
                            <div class="col-md-2">
                                <label class="form-label" for="phone_1">Contacto</label>
                                {{html()->text('phone_1')->id('phone_1')->class('form-control')->placeholder('')}}
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="email">Email</label>
                                {{html()->text('email')->id('email')->class('form-control')->placeholder('')}}
                            </div>
                            <div class="col-md-2">
                                <label class="form-label" for="phone_2">Contacto 2</label>
                                {{html()->text('phone_2')->id('phone_2')->class('form-control')->placeholder('')}}
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-1">
                                <label class="form-label" for="agenda_color">Cor na Agenda</label>
                                <input type="color" id="agenda_color" name="agenda_color"
                                       value="{{old('agenda_color', $professional->agenda_color)}}"
                                       class="form-control" style="height: 39px"/>
                            </div>
                            <div class="col-md-1">
                                <label class="form-label" for="status">Ordem</label>
                                {{html()->text('order')->id('order')->class('form-control')}}
                            </div>
                            <div class="col-md-2">
                                <label class="form-label" for="status">Estado</label>
                                {{html()->select('status')->id('status')->options([true => 'Activo', false => 'Inactivo'])->class('form-select')->placeholder('-- Seleccionar --')}}
                            </div>
                        </div>
                        <hr class="my-2"/>
                        <div class="row">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary waves-effect waves-light">
                                    <i class="icon-base ti tabler-device-floppy"></i> Gravar
                                </button>
                                <a href="{{ route('professionals.index') }}"
                                   class="btn btn-secondary waves-effect waves-light">
                                    <i class="icon-base ti tabler-arrow-left"></i> Voltar
                                </a>
                            </div>
                        </div>
                        {{html()->closeModelForm()}}
                    </div>
                    @if(isset($professional->id))
                        <div class="tab-pane fade" id="navs-services" role="tabpanel">
                                   <div class="col-md-12">
                                    <form method="POST"
                                          action="{{ route('professionals.update.services', $professional->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="card">
                                            <div class="card-header header-elements">
                                                <h5 class="mb-0 me-2">Serviços prestados</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="row g-6">
                                                    <table class="table">
                                                        <thead>
                                                        <tr>
                                                            <th style="width:50px">
                                                                <input type="checkbox" id="select_all_services"/>
                                                            </th>
                                                            <th>Serviço</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @if(isset($services))
                                                            @foreach($services as $service)
                                                                <tr>
                                                                    <td style="width:50px">
                                                                        <input type="checkbox" class="service-checkbox"
                                                                               name="services[]"
                                                                               value="{{ $service->id }}"
                                                                               id="service_{{ $service->id }}"
                                                                            {{ $professional->services->contains($service->id) ? 'checked' : '' }}/>
                                                                    </td>
                                                                    <td>
                                                                        <label
                                                                            for="service_{{ $service->id }}">{{ $service->name }}</label>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="card-footer">
                                                <div class="d-flex gap-2">
                                                    <button type="submit"
                                                            class="btn btn-primary waves-effect waves-light">
                                                        <i class="icon-base ti tabler-device-floppy"></i> Gravar
                                                    </button>
                                                    <a href="{{ route('professionals.index') }}"
                                                       class="btn btn-secondary waves-effect waves-light">
                                                        <i class="icon-base ti tabler-arrow-left"></i> Voltar
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                        </div>
                        <div class="tab-pane fade show active" id="navs-justified-profile" role="tabpanel">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header header-elements">
                                        <h5 class="mb-0 me-2">Horário de Trabalho</h5>
                                    </div>

                                    @php
                                        $dias = [
                                            1 => 'Segunda-feira',
                                            2 => 'Terça-feira',
                                            3 => 'Quarta-feira',
                                            4 => 'Quinta-feira',
                                            5 => 'Sexta-feira',
                                            6 => 'Sábado',
                                            0 => 'Domingo',
                                        ];
                                    @endphp

                                    <form action="{{ route('professionals.save.working-hours', $professional->id) }}"
                                          method="POST">
                                        @csrf
                                        @method('PUT')

                                        <div class="card-body">
                                            <div class="row g-3 align-items-center mb-3">
                                                <div class="col-md-4">
                                                    <label for="weekday" class="form-label">Dia da Semana</label>
                                                    <select class="form-select" id="weekday-select">
                                                        @foreach($dias as $num => $nome)
                                                            <option value="{{ $num }}">{{ $nome }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="form-label">Hora Início</label>
                                                    <input type="time" class="form-control" id="start-hour"
                                                           name="start_hour">
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="form-label">Hora Fim</label>
                                                    <input type="time" class="form-control" id="end-hour"
                                                           name="end_hour">
                                                </div>

                                                <div class="col-md-2 mt-4">
                                                    <button type="button" class="btn btn-success w-100"
                                                            id="add-hour-block">Adicionar
                                                    </button>
                                                </div>
                                            </div>

                                            <div id="working-hours-container">
                                                @php
                                                    $grouped = $workingHours->groupBy('weekday');
                                                @endphp

                                                @php
                                                    $grouped = $workingHours->groupBy('weekday');
                                                    $i = 0;
                                                @endphp

                                                @foreach ($grouped as $weekday => $blocks)
                                                    <div id="day-group-{{ $weekday }}" class="mb-4">
                                                        <h6 class="mb-3">{{ $dias[$weekday] }}</h6>
                                                        <div class="day-group-rows">
                                                            @foreach ($blocks as $block)
                                                                <div class="row g-3 align-items-center mb-2">
                                                                    <div class="col-md-3" style="display: none">
                                                                        <input type="text" class="form-control-plaintext" value="{{ $dias[$weekday] }}" readonly>
                                                                        <input type="hidden" name="working_hours[{{ $i }}][weekday]" value="{{ $weekday }}">
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <input type="time" name="working_hours[{{ $i }}][start_hour]" class="form-control"
                                                                               value="{{ \Carbon\Carbon::parse($block->start_hour)->format('H:i') }}" readonly>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <input type="time" name="working_hours[{{ $i }}][end_hour]" class="form-control"
                                                                               value="{{ \Carbon\Carbon::parse($block->end_hour)->format('H:i') }}" readonly>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <button type="button" class="btn btn-sm btn-danger remove-block">Remover</button>
                                                                    </div>
                                                                </div>
                                                                @php $i++; @endphp
                                                            @endforeach
                                                        </div>
                                                        <hr>
                                                    </div>
                                                @endforeach

                                                <input type="hidden" id="wh-next-index" value="{{ $i }}">
                                            </div>
                                        </div>

                                        <div class="card-footer">
                                            <div class="d-flex gap-2">
                                                <button type="submit" class="btn btn-primary waves-effect waves-light">
                                                    <i class="icon-base ti tabler-device-floppy"></i> Gravar
                                                </button>
                                                <a href="{{ route('professionals.index') }}"
                                                   class="btn btn-secondary waves-effect waves-light">
                                                    <i class="icon-base ti tabler-arrow-left"></i> Voltar
                                                </a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="navs-justified-messages" role="tabpanel">
                            @include('modules.professionals.partials.unavailabilities')
                        </div>
                    @endif
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
