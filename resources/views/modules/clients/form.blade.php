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
                <i class="icon-base ti tabler-users"></i>
                {{ $client->id ? 'Editar Cliente' : 'Novo Cliente' }}
            </h5>
            <hr class="my-2"/>
        </div>
    </div>
    <div class="row g-6">
        <div class="col-md-12">
            @if(!$client->id)
                {{ html()->modelForm($client, 'POST', route('clients.store'))->open() }}
            @else
                {{ html()->modelForm($client, 'PUT', route('clients.update', $client->id))->open() }}
            @endif
            {{ html()->token() }}
            <div class="card">
                <div class="card-header header-elements">
                    <h5 class="mb-0 me-2">Detalhe de
                        Cliente {!!  $client->name ? '| <span style="color:#2A7AD4">' . $client->name . '</span>': ''  !!}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-6">
                        <div class="col-md-4">
                            <label class="form-label" for="name">Nome</label>
                            {{html()->text('name')->id('name')->class('form-control')->placeholder('')}}
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="phone_1">Contacto</label>
                            {{html()->text('phone_1')->id('phone_1')->class('form-control')->placeholder('')}}
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="phone_2">Contacto 2</label>
                            {{html()->text('phone_2')->id('phone_2')->class('form-control')->placeholder('')}}
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="email">Email</label>
                            {{html()->text('email')->id('email')->class('form-control')->placeholder('')}}
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <label class="form-label" for="address">Morada</label>
                            {{html()->text('address')->id('address')->class('form-control')->placeholder('')}}
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="number_port">Nº / Porta</label>
                            {{html()->text('number_port')->id('number_port')->class('form-control')->placeholder('')}}
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="zip_code">Cód. Postal</label>
                            {{html()->text('zip_code')->id('zip_code')->class('form-control')->placeholder('')}}
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="phone_2">Localidade</label>
                            {{html()->text('city')->id('city')->class('form-control')->placeholder('')}}
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-2">
                            <label class="form-label" for="birthdate">Data Aniversário</label>
                            {{html()->date('birthdate')->id('birthdate')->class('form-control')->placeholder('')}}
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <label class="form-label" for="name">Marketing</label><br>
                            <label class="switch">
                                <input type="checkbox" class="switch-input">
                                <span class="switch-toggle-slider">
                                <span class="switch-on"></span>
                                <span class="switch-off"></span>
                              </span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                            <i class="icon-base ti tabler-device-floppy"></i> Gravar
                        </button>
                        <a href="{{ route('clients.index') }}" class="btn btn-secondary waves-effect waves-light">
                            <i class="icon-base ti tabler-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
            </div>
            {{html()->closeModelForm()}}
        </div>
    </div>
@endsection
