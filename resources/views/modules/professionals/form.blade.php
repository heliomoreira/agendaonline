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
                          <span class="d-none d-sm-inline-flex align-items-center"
                          ><i class="icon-base ti tabler-message-dots icon-sm me-1_5"></i>Disponbilidade</span
                          >
                            <i class="icon-base ti tabler-message-dots icon-sm d-sm-none"></i>
                        </button>
                    </li>
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
                    <div class="tab-pane fade" id="navs-justified-profile" role="tabpanel">
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
                                <form action="{{route('professionals.save.working-hours', $professional->id)}}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="card-body">
                                        <div class="row g-6">
                                            <table class="table">
                                                <thead>
                                                <tr>
                                                    <th>Dia da Semana</th>
                                                    <th>Manhã (Início)</th>
                                                    <th>Manhã (Fim)</th>
                                                    <th>Tarde (Início)</th>
                                                    <th>Tarde (Fim)</th>
                                                </tr>
                                                </thead>
                                                <tbody>

                                                @foreach($dias as $num => $nome)
                                                    <tr>
                                                        <td>
                                                            {{ $nome }}
                                                            <input type="hidden"
                                                                   name="working_hours[{{ $num }}][weekday]"
                                                                   value="{{ $num }}">
                                                        </td>
                                                        <td><input type="time"
                                                                   name="working_hours[{{ $num }}][start_hour]"
                                                                   class="form-control"
                                                                   value="{{ old("working_hours.$num.start_hour") }}">
                                                        </td>
                                                        <td><input type="time"
                                                                   name="working_hours[{{ $num }}][lunch_start]"
                                                                   class="form-control"
                                                                   value="{{ old("working_hours.$num.lunch_start") }}">
                                                        </td>
                                                        <td><input type="time"
                                                                   name="working_hours[{{ $num }}][lunch_end]"
                                                                   class="form-control"
                                                                   value="{{ old("working_hours.$num.lunch_end") }}">
                                                        </td>
                                                        <td><input type="time"
                                                                   name="working_hours[{{ $num }}][end_hour]"
                                                                   class="form-control"
                                                                   value="{{ old("working_hours.$num.end_hour") }}">
                                                        </td>
                                                    </tr>
                                                @endforeach

                                                </tbody>
                                            </table>
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
                        <p>
                            Oat cake chupa chups dragée donut toffee. Sweet cotton candy jelly beans macaroon gummies
                            cupcake gummi bears cake chocolate.
                        </p>
                        <p class="mb-0">
                            Cake chocolate bar cotton candy apple pie tootsie roll ice cream apple pie brownie cake.
                            Sweet
                            roll icing sesame snaps caramels danish toffee. Brownie biscuit dessert dessert. Pudding
                            jelly
                            jelly-o tart brownie jelly.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--
        <div class="row g-6">
            <div class="col-md-6">
                @if(!$professional->id)
                    {{ html()->modelForm($professional, 'POST', route('professionals.store'))->open() }}
                @else
                    {{ html()->modelForm($professional, 'PUT', route('professionals.update', $professional->id))->open() }}
                @endif
                {{ html()->token() }}
                <div class="card">
                    <div class="card-header header-elements">
                        <h5 class="mb-0 me-2">
                            Detalhe {!!  $professional->name ? '| <span style="color:#2A7AD4">' . $professional->name . '</span>': ''  !!}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-6">
                            <div class="col-md-8">
                                <label class="form-label" for="name">Nome</label>
                                {{html()->text('name')->id('name')->class('form-control')->placeholder('')}}
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="phone_1">Contacto</label>
                                {{html()->text('phone_1')->id('phone_1')->class('form-control')->placeholder('')}}
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-8">
                                <label class="form-label" for="email">Email</label>
                                {{html()->text('email')->id('email')->class('form-control')->placeholder('')}}
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="phone_2">Contacto 2</label>
                                {{html()->text('phone_2')->id('phone_2')->class('form-control')->placeholder('')}}
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <label class="form-label" for="agenda_color">Cor na Agenda</label>
                                <input type="color" id="agenda_color" name="agenda_color" value="{{old('agenda_color', $professional->agenda_color)}}" class="form-control" />
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="status">Ordem</label>
                                {{html()->text('order')->id('order')->class('form-control')}}
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="status">Estado</label>
                                {{html()->select('status')->id('status')->options([true => 'Activo', false => 'Inactivo'])->class('form-select')->placeholder('-- Seleccionar --')}}
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary waves-effect waves-light">
                                <i class="icon-base ti tabler-device-floppy"></i> Gravar
                            </button>
                            <a href="{{ route('professionals.index') }}" class="btn btn-secondary waves-effect waves-light">
                                <i class="icon-base ti tabler-arrow-left"></i> Voltar
                            </a>
                        </div>
                    </div>
                </div>
                {{html()->closeModelForm()}}
            </div>
            @if(isset($professional->id))
                <div class="col-md-6">
                    <form method="POST" action="{{ route('professionals.update.services', $professional->id) }}">
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
                                                        <input type="checkbox" class="service-checkbox" name="services[]"
                                                               value="{{ $service->id }}"
                                                               id="service_{{ $service->id }}"
                                                            {{ $professional->services->contains($service->id) ? 'checked' : '' }}/>
                                                    </td>
                                                    <td>
                                                        <label for="service_{{ $service->id }}">{{ $service->name }}</label>
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
                                    <button type="submit" class="btn btn-primary waves-effect waves-light">
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
            @endif
        </div>--}}
@endsection
@push('scripts')
    <script>
        document.getElementById('select_all_services').addEventListener('change', function () {
            const checkboxes = document.querySelectorAll('.service-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    </script>
@endpush
