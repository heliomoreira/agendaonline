@extends('layouts.app')
@section('content')
    @include('admin._partials.alerts')

    <div class="row mb-2">
        <div class="col-md-12">
            <h5 class="d-flex align-items-center gap-2 m-0">
                <i class="icon-base ti tabler-settings"></i>
                Dados de Conta
            </h5>
            <hr class="my-2"/>
        </div>
    </div>

    <div class="row g-6">
        <div class="col-md-12">
            <div class="card">
                {{ html()->form('POST', route('account-settings.updateAccount'))->acceptsFiles()->open() }}
                @method('PUT')
                @csrf
                <div class="card-header header-elements">
                    <h5 class="mb-0 me-2">Detalhe da Conta</h5>
                </div>
                <div class="card-body">

                    {{-- ===== Identificação ===== --}}
                    <h6 class="text-muted fw-semibold mb-3">Identificação</h6>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nome</label>
                            <input type="text" class="form-control" name="name" id="name" value="{{ $tenant->name }}">
                        </div>
                        <div class="col-md-3">
                            <label for="vat" class="form-label">NIF</label>
                            <input type="text" class="form-control" name="vat" id="vat" value="{{ $tenant->vat }}">
                        </div>
                    </div>

                    {{-- ===== Morada ===== --}}
                    <h6 class="text-muted fw-semibold mb-3 mt-5">Morada</h6>
                    <div class="row g-4">
                        <div class="col-md-5">
                            <label for="address" class="form-label">Morada</label>
                            <input type="text" class="form-control" name="address" id="address"
                                   value="{{ $tenant->address }}">
                        </div>
                        <div class="col-md-2">
                            <label for="number_port" class="form-label">Nº / Porta / Andar</label>
                            <input type="text" class="form-control" name="number_port" id="number_port"
                                   value="{{ $tenant->number_port }}">
                        </div>
                        <div class="col-md-2">
                            <label for="zip_code" class="form-label">Cód. Postal</label>
                            <input type="text" class="form-control" name="zip_code" id="zip_code"
                                   value="{{ $tenant->zip_code }}">
                        </div>
                        <div class="col-md-3">
                            <label for="city" class="form-label">Localidade</label>
                            <input type="text" class="form-control" name="city" id="city" value="{{ $tenant->city }}">
                        </div>
                    </div>

                    {{-- ===== Contactos ===== --}}
                    <h6 class="text-muted fw-semibold mb-3 mt-5">Contactos</h6>
                    <div class="row g-4">
                        <div class="col-md-3">
                            <label for="phone_1" class="form-label">Contacto</label>
                            <input type="text" class="form-control" name="phone_1" id="phone_1"
                                   value="{{ $tenant->phone_1 }}">
                        </div>
                        <div class="col-md-3">
                            <label for="phone_2" class="form-label">Contacto Alt.</label>
                            <input type="text" class="form-control" name="phone_2" id="phone_2"
                                   value="{{ $tenant->phone_2 }}">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="email"
                                   value="{{ $tenant->email }}">
                        </div>
                    </div>

                    {{-- ===== SMS ===== --}}
                    <h6 class="text-muted fw-semibold mb-3 mt-5">SMS</h6>
                    <div class="row g-4">
                        <div class="col-md-3">
                            <label for="sms_sender" class="form-label">Remetente SMS</label>
                            <input type="text" class="form-control" name="sms_sender" id="sms_sender"
                                   value="{{ $tenant->sms_sender }}" readonly disabled>
                        </div>
                        <div class="col-md-3">
                            <label for="sms_credits" class="form-label">Saldo SMS</label>
                            <input type="text" class="form-control" name="sms_credits" id="sms_credits"
                                   value="{{ $tenant->sms_credits }}" readonly disabled>
                        </div>
                    </div>

                    {{-- ===== Aparência do Portal ===== --}}
                    <h6 class="text-muted fw-semibold mb-3 mt-5">Aparência do Portal</h6>
                    <div class="row g-4">
                        <div class="col-md-3">
                            <label for="logo" class="form-label">Logo</label>
                            <input type="file" class="form-control" name="logo" id="logo" accept="image/*">
                            @if($tenant->logo)
                                <small class="text-muted d-block mt-1">Ficheiro atual: {{ $tenant->logo }}</small>
                            @endif
                        </div>
                        <div class="col-md-2">
                            <label for="main_color" class="form-label">Cor Principal</label>
                            <input type="color" class="form-control form-control-color w-100" name="main_color"
                                   id="main_color"
                                   value="{{ $tenant->main_color }}">
                        </div>
                        <div class="col-md-2">
                            <label for="secondary_color" class="form-label">Cor Secundária</label>
                            <input type="color" class="form-control form-control-color w-100" name="secondary_color"
                                   id="secondary_color"
                                   value="{{ $tenant->secondary_color }}">
                        </div>
                        <div class="col-md-3">
                            <label for="booking_available" class="form-label">Portal do Cliente</label>
                            {{ html()->select('booking_available')->options([true => 'Ativo', false => 'Inativo'])->class('form-select')->id('booking_available')->value($tenant->booking_available) }}
                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                            <i class="icon-base ti tabler-device-floppy"></i> Gravar
                        </button>
                        <a href="{{ route('account-settings.index') }}"
                           class="btn btn-secondary waves-effect waves-light">
                            <i class="icon-base ti tabler-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
                {{ html()->form()->close() }}
            </div>
        </div>
    </div>
@endsection
