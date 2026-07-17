@extends('layouts.app')
@section('content')
    @if (session('success'))
        <div class="row">
            <div class="col-12">
                <div class="alert alert-solid-success alert-dismissible fade show d-flex align-items-center"
                     role="alert">
                    <span class="alert-icon rounded me-2">
                        <i class="icon-base ti tabler-check icon-md" aria-hidden="true"></i>
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
                        <i class="icon-base ti tabler-x" aria-hidden="true"></i>
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

    {{-- ===== Cabeçalho da página ===== --}}
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="d-flex align-items-center gap-2 mb-1">
                <i class="icon-base ti tabler-settings" aria-hidden="true"></i> Configurações
            </h4>
            <p class="text-body-secondary mb-0">Gere as definições gerais da aplicação.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            {{ html()->form('POST', route('settings.update'))->acceptsFiles()->class('card')->open() }}
            @method('PUT')
            @csrf

            <div class="card-header">
                <h5 class="mb-0">Definições Gerais</h5>
            </div>

            <div class="card-body">

                {{-- ===== Secção: Clientes ===== --}}
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="avatar avatar-sm">
                        <span class="avatar-initial bg-label-primary rounded">
                            <i class="icon-base ti tabler-users" aria-hidden="true"></i>
                        </span>
                    </span>
                    <h6 class="mb-0">Clientes</h6>
                </div>
                <p class="text-body-secondary small mb-3">Regras de identificação e validação de clientes.</p>

                <div class="row g-4">
                    <div class="col-md-3">
                        <label for="client_validation" class="form-label">Validação Cliente</label>
                        {{ html()->select('client_validation', ['' => 'Não Validar','email' => 'Email','phone_1' => 'Telemóvel','email_and_phone' => 'Email e Telemóvel'], $settings->client_validation?->value)
                            ->class('form-select')
                            ->id('client_validation') }}
                        <small class="text-body-secondary">Campo usado para verificar duplicados.</small>
                    </div>
                </div>

                <hr class="my-4">

                {{-- ===== Secção: Marcações ===== --}}
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="avatar avatar-sm">
                        <span class="avatar-initial bg-label-info rounded">
                            <i class="icon-base ti tabler-calendar" aria-hidden="true"></i>
                        </span>
                    </span>
                    <h6 class="mb-0">Marcações</h6>
                </div>
                <p class="text-body-secondary small mb-3">Comportamento da agenda e das marcações.</p>

                <div class="row g-4">
                    <div class="col-md-3">
                        <label for="booking_allow_overlap" class="form-label">Permitir Sobreposição</label>
                        {{ html()->select('booking_allow_overlap', ['0' => 'Não', '1' => 'Sim'], $settings->booking_allow_overlap ?? 0)
                            ->class('form-select')
                            ->id('booking_allow_overlap') }}
                        <small class="text-body-secondary">Permite marcações no mesmo horário.</small>
                    </div>
                    <div class="col-md-3">
                        <label for="color_in_agenda" class="form-label">Cor na Agenda</label>
                        {{ html()->select('color_in_agenda', ['professional' => 'Profissional', 'service' => 'Serviço'], $settings->color_in_agenda ?? 'professional')
                            ->class('form-select')
                            ->id('color_in_agenda') }}
                        <small class="text-body-secondary">Cor que aparece na agenda.</small>
                    </div>
                </div>

                <hr class="my-4">

                {{-- ===== Secção: SMS ===== --}}
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="avatar avatar-sm">
                        <span class="avatar-initial bg-label-success rounded">
                            <i class="icon-base ti tabler-device-mobile-message" aria-hidden="true"></i>
                        </span>
                    </span>
                    <h6 class="mb-0">SMS</h6>
                </div>
                <p class="text-body-secondary small mb-3">Lembretes automáticos enviados aos clientes.</p>

                <div class="row g-4">
                    <div class="col-md-3">
                        <label class="form-label" for="sms_advance_days">Dias de Antecedência</label>
                        {{ html()->select('sms_advance_days')
                            ->id('sms_advance_days')
                            ->class('form-select')
                            ->options([1 => "1 dia", 2 => "2 dias", 3 => "3 dias", 4 => "4 dias", 5 => "5 dias", 6 => "6 dias", 7 => "7 dias"])
                            ->value($settings->sms_advance_days ?? 1) }}
                        <small class="text-body-secondary">Quando enviar o lembrete antes da marcação.</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="sms_send_hour">Hora de Envio</label>
                        <input type="time" name="sms_send_hour" class="form-control" id="sms_send_hour"
                               value="{{ $settings->sms_send_hour ? \Carbon\Carbon::parse($settings->sms_send_hour)->format('H:i') : '' }}">
                        <small class="text-body-secondary">Hora do dia a que o lembrete é disparado.</small>
                    </div>
                </div>

            </div>

            <div class="card-footer d-flex gap-2">
                <button type="submit" class="btn btn-primary waves-effect waves-light">
                    <i class="icon-base ti tabler-device-floppy me-1" aria-hidden="true"></i> Gravar
                </button>
            </div>

            {{ html()->form()->close() }}
        </div>
    </div>
@endsection
