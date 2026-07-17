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
    <div class="row">
        <div class="col-12">
            <h4 class="my-4">Gestão de SMS</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12 order-3 mt-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title"><i class="ti tabler-device-mobile-message"></i> Detalhe</h5>
                </div>
                {{ html()->form('PUT', route('sms.update'))->acceptsFiles()->class('modal-content')->open() }}
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label" for="sms_sender">Remetente SMS</label>
                            {{html()->text('sms_sender')->id('sms_sender')->class('form-control')->value($tenant->sms_sender)->disabled()}}
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="sms_credits">Saldo SMS</label>
                            {{html()->text('sms_credits')->id('sms_credits')->class('form-control')->value($tenant->sms_credits . " €")->disabled()}}
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="sms_status">Estado SMS</label>
                            {{ html()->select('sms_status', [0 => 'Inativo', 1 => 'Ativo'], $tenant->sms_status)->id('sms_status')->class('form-select') }}
                        </div>
                    </div>


                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary waves-effect waves-light">
                                <i class="icon-base ti tabler-device-floppy"></i> Gravar
                            </button>
                        </div>
                    </div>
                </div>
                {{html()->form()->close()}}
            </div>

        </div>
    </div>
@endsection
