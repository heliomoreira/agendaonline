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
    <div class="row g-6">
        <div class="col-md-12">
            <div class="card">
                {{ html()->form('POST', route('account-settings.updateAccount'))->acceptsFiles()->class('modal-content')->open() }}
                @method('PUT')
                @csrf
                <div class="card-header header-elements">
                    <h5 class="mb-0 me-2">Dados de Conta</h5>
                </div>
                <div class="card-body">

                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label for="name" class="form-label">Nome</label>
                                <input type="text" class="form-control" name="name" id="name"
                                       value="{{$tenant->name}}">
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="form-group">
                                <label for="vat" class="form-label">NIF</label>
                                <input type="text" class="form-control" name="vat" id="vat"
                                       value="{{$tenant->vat}}">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-4">
                            <div class="form-group">
                                <label for="address" class="form-label">Morada</label>
                                <input type="text" class="form-control" name="address" id="address"
                                       value="{{$tenant->address}}">
                            </div>
                        </div>
                        <div class="col-2">
                            <label for="number_port" class="form-label">Nº/Porta/Andar</label>
                            <input type="text" class="form-control" name="number_port" id="number_port"
                                   value="{{$tenant->number_port}}">
                        </div>
                        <div class="col-2">
                            <label for="zip_code" class="form-label">Cod. Postal</label>
                            <input type="text" class="form-control" name="zip_code" id="zip_code"
                                   value="{{$tenant->zip_code}}">
                        </div>
                        <div class="col-4">
                            <label for="city" class="form-label">Localidade</label>
                            <input type="text" class="form-control" name="city" id="city"
                                   value="{{$tenant->city}}">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-2">
                            <label for="phone_1" class="form-label">Contacto</label>
                            <input type="text" class="form-control" name="phone_1" id="phone_1"
                                   value="{{$tenant->phone_1}}">
                        </div>
                        <div class="col-2">
                            <label for="phone_2" class="form-label">Contacto Alt.</label>
                            <input type="text" class="form-control" name="phone_2" id="phone_2"
                                   value="{{$tenant->phone_2}}">
                        </div>
                        <div class="col-4">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" class="form-control" name="email" id="email"
                                   value="{{$tenant->email}}">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-2">
                            <label for="sms_sender" class="form-label">Remetente SMS</label>
                            <input type="text" class="form-control" name="sms_sender" id="sms_sender"
                                   value="{{$tenant->sms_sender}}" style="background-color: #dadada" readonly>
                        </div>
                        <div class="col-2">
                            <label for="sms_credits" class="form-label">Saldo SMS</label>
                            <input type="text" class="form-control" name="sms_credits" id="sms_credits"
                                   value="{{$tenant->sms_credits}}" style="background-color: #dadada" readonly>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-2">
                            <label for="logo" class="form-label">Logo</label>
                            <input type="file" class="form-control" name="logo" id="logo"
                                   value="{{$tenant->logo}}">
                        </div>
                        <div class="col-2">
                            <label for="main_color" class="form-label">Cor Principal</label>
                            <input type="color" class="form-control" name="main_color" id="main_color" style="height: 39px"
                                   value="{{$tenant->main_color}}">
                        </div>
                        <div class="col-2">
                            <label for="secondary_color" class="form-label">Cor Secundária</label>
                            <input type="color" class="form-control" name="secondary_color" id="secondary_color" style="height: 39px"
                                   value="{{$tenant->secondary_color}}">
                        </div>
                        <div class="col-2">
                            <label for="booking_available" class="form-label">Portal do Cliente</label>
                            {{html()->select('booking_available')->options([true => 'Ativo', false => 'Inativo'])->class('form-select')->id('booking_available')->value($tenant->booking_available)}}
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
                {{html()->form()->close()}}
            </div>
        </div>
    </div>
@endsection
