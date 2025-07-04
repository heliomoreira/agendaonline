@extends('layouts.app')
@section('content')
    <div class="row g-6">
        <div class="col-md-12">
            <div class="card">
                <form action="#">
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
@endsection
