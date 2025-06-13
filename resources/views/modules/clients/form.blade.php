@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-solid-success d-flex align-items-center" role="alert">
            <span class="alert-icon rounded">
              <i class="icon-base ti tabler-check icon-md"></i>
            </span>
                Cliente adicionado com sucesso!
            </div>
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
                    <h5 class="mb-0 me-2">Detalhe de Cliente</h5>
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
                            <input type="text" id="phone_2" class="form-control" placeholder="">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="email">Email</label>
                            <input type="text" id="email" class="form-control" placeholder="">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <label class="form-label" for="name">Morada</label>
                            <input type="text" id="name" class="form-control" placeholder="">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="phone_1">Nº / Porta</label>
                            <input type="text" id="phone_1" class="form-control" placeholder="">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="phone_2">Localidade</label>
                            <input type="text" id="phone_2" class="form-control" placeholder="">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-2">
                            <label class="form-label" for="birthdate">Data Aniversário</label>
                            <input type="date" id="birthdate" class="form-control" placeholder="">
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
                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                        <i class="icon-base ti tabler-device-floppy"></i> Gravar
                    </button>
                </div>
            </div>
            {{html()->closeModelForm()}}
        </div>
    </div>
@endsection
