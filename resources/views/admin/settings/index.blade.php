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

    <div class="row mb-2">
        <div class="col-md-12">
            <h5 class="d-flex align-items-center gap-2 m-0">
                <i class="icon-base ti tabler-settings"></i>
                Configurações
            </h5>
            <hr class="my-2"/>
        </div>
    </div>

    <div class="row g-6">
        <div class="col-12">
            {{ html()->form('POST', route('settings.update'))->acceptsFiles()->class('card')->open() }}
            @method('PUT')
            @csrf

            <div class="card-header header-elements">
                <h5 class="mb-0 me-2">Definições Gerais</h5>
            </div>

            <div class="card-body">

                {{-- ===== Clientes ===== --}}
                <h6 class="text-muted fw-semibold mb-3">
                    <i class="icon-base ti tabler-users me-1"></i> Clientes
                </h6>
                <div class="row g-4">
                    <div class="col-md-3">
                        <label for="client_validation" class="form-label">Validação Cliente</label>
                        {{ html()->select('client_validation', ['' => 'Não Validar','email' => 'Email','phone_1' => 'Telemóvel','email_and_phone' => 'Email e Telemóvel'], $settings->client_validation?->value)
                            ->class('form-select')
                            ->id('client_validation') }}
                        <small class="text-muted">Campo usado para verificar duplicados.</small>
                    </div>
                </div>

                {{-- ===== Marcações ===== --}}
                <h6 class="text-muted fw-semibold mb-3 mt-5">
                    <i class="icon-base ti tabler-calendar me-1"></i> Marcações
                </h6>
                <div class="row g-4">
                    <div class="col-md-3">
                        <label for="booking_allow_overlap" class="form-label">Permitir Sobreposição</label>
                        {{ html()->select('booking_allow_overlap', ['0' => 'Não', '1' => 'Sim'], $settings->booking_allow_overlap ?? 0)
                            ->class('form-select')
                            ->id('booking_allow_overlap') }}
                        <small class="text-muted">Permite marcações no mesmo horário.</small>
                    </div>
                </div>

                {{-- ===== SMS ===== --}}
                <h6 class="text-muted fw-semibold mb-3 mt-5">
                    <i class="icon-base ti tabler-device-mobile-message me-1"></i> SMS
                </h6>
                <div class="row g-4">
                    <div class="col-md-2">
                        <label class="form-label" for="sms_advance_days">Dias de Antecedência</label>
                        {{ html()->select('sms_advance_days')
                            ->id('sms_advance_days')
                            ->class('form-select')
                            ->options([1 => "1 dia", 2 => "2 dias", 3 => "3 dias", 4 => "4 dias", 5 => "5 dias", 6 => "6 dias", 7 => "7 dias"])
                            ->value($settings->sms_advance_days ?? 1) }}
                        <small class="text-muted">Quando enviar o lembrete antes da marcação.</small>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="sms_send_hour">Hora de Envio</label>
                        <input type="time" name="sms_send_hour" class="form-control" id="sms_send_hour"
                               value="{{ $settings->sms_send_hour ? \Carbon\Carbon::parse($settings->sms_send_hour)->format('H:i') : '' }}">
                    </div>
                </div>

            </div>

            <div class="card-footer d-flex gap-2">
                <button type="submit" class="btn btn-primary waves-effect waves-light">
                    <i class="icon-base ti tabler-device-floppy me-1"></i> Gravar
                </button>
            </div>

            {{ html()->form()->close() }}
        </div>
    </div>
@endsection
