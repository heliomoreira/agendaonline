@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="row">
            <div class="col-12">
                <div class="alert alert-solid-success alert-dismissible fade show d-flex align-items-center"
                     role="alert">
                    <span class="alert-icon rounded me-2">
                        <i class="icon-base ti tabler-check icon-md"></i>
                    </span>
                    <div class="flex-grow-1">{{ session('success') }}</div>
                    <button type="button" class="btn-close ms-2" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="row">
            <div class="col-12">
                <div class="alert alert-solid-danger d-flex align-items-center" role="alert">
                    <span class="alert-icon rounded me-2">
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

    <div class="row g-6">
        <div class="col-12">
            {{ html()->form('POST', route('settings.update'))->acceptsFiles()->class('card')->open() }}
            @method('PUT')
            @csrf

            <div class="card-header d-flex align-items-center">
                <i class="icon-base ti tabler-settings me-2"></i>
                <h5 class="mb-0">Configurações</h5>
            </div>

            <div class="card-body">
                {{-- Secção: Clientes --}}
                <div class="d-flex align-items-center mb-4">
                    <i class="icon-base ti tabler-users text-primary me-2"></i>
                    <h6 class="mb-0 text-primary">Clientes</h6>
                </div>

                <div class="row mb-5">
                    <div class="col-md-3">
                        <label for="client_validation" class="form-label">Validação Cliente</label>
                        {{ html()->select('client_validation', [null=>'Não Validar','email' => 'Email', 'phone_1' => 'Telemóvel','email_and_phone' => 'Email e Telemóvel'], $settings->client_validation ?? null)
                            ->class('form-select')
                            ->id('client_validation') }}
                        <small class="text-muted">Campo usado para verificar duplicados.</small>
                    </div>
                </div>

                <hr class="my-4">

                {{-- Secção: Marcações --}}
                <div class="d-flex align-items-center mb-4">
                    <i class="icon-base ti tabler-calendar text-primary me-2"></i>
                    <h6 class="mb-0 text-primary">Marcações</h6>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <label for="booking_allow_overlap" class="form-label">Permitir Sobreposição</label>
                        {{ html()->select('booking_allow_overlap', ['0' => 'Não', '1' => 'Sim'], $settings->booking_allow_overlap ?? 0)
                            ->class('form-select')
                            ->id('booking_allow_overlap') }}
                        <small class="text-muted">Permite marcações no mesmo horário.</small>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex align-items-center mb-4">
                    <i class="icon-base ti tabler-calendar text-primary me-2"></i>
                    <h6 class="mb-0 text-primary">SMS</h6>
                </div>

                <div class="row">
                    <div class="col-md-2">
                        <label class="form-label" for="sms_advance_days">Dias de Antecedência</label>
                        {{ html()->select('sms_advance_days')
                            ->id('sms_advance_days')
                            ->class('form-select')
                            ->options([1 => "1 dia", 2 => "2 dias", 3 => "3 dias", 4 => "4 dias", 5 => "5 dias", 6 => "6 dias", 7 => "7 dias"])
                            ->value($settings->sms_advance_days ?? 1) }}
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="sms_send_hour">Hora de Envio</label>
                        <input type="time" name="sms_send_hour" class="form-control" id="sms_send_hour"
                               value="{{$settings->sms_send_hour}}">
                    </div>
                </div>


            </div>

            <div class="card-footer d-flex gap-2">
                <button type="submit" class="btn btn-primary waves-effect waves-light">
                    <i class="icon-base ti tabler-device-floppy me-1"></i> Gravar
                </button>
                <a href="{{ route('professionals.index') }}" class="btn btn-outline-secondary waves-effect">
                    <i class="icon-base ti tabler-arrow-left me-1"></i> Voltar
                </a>
            </div>

            {{ html()->form()->close() }}
        </div>
    </div>
@endsection
