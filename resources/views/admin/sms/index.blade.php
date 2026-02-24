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
                        <div class="col-md-2">
                            <label class="form-label" for="sms_send_hour">Hora de Envio</label>
                            <input type="time" name="sms_send_hour" class="form-control"
                                   value="{{$tenant->sms_send_hour}}">
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
    <div class="row g-6 mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title"><i class="ti tabler-bell-ringing"></i> Listagem de Envios ({{$notifications->total()}})</h5>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table border-top">
                            <thead class="border-bottom">
                            <tr style="background-color: #f1f1f1">
                                <th>Destinatário</th>
                                <th>Data do Serviço</th>
                                <th>Hora de Início</th>
                                <th>Hora de Fim</th>
                                <th>Hora de Envio SMS</th>
                                <th>Estado</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($notifications as $notification)
                                <tr>
                                    <td class="pt-5">
                                        <div class="d-flex justify-content-start align-items-center">
                                            <div class="d-flex flex-column">
                                                <p class="mb-0 text-heading">{{ $notification->destinatary }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="pt-5">
                                        <p class="mb-0 text-heading">{{ $notification->service_day }}</p>
                                    </td>
                                    <td class="pt-5">
                                        <p class="mb-0 text-heading">{{ $notification->service_start_hour }}h</p>
                                    </td>
                                    <td class="pt-5">
                                        <p class="mb-0 text-heading">{{ $notification->service_end_hour }}h</p>
                                    </td>
                                    <td class="pt-5">
                                        <p class="mb-0 text-heading">{{ $notification->send_hour }}h</p>
                                    </td>
                                    <td class="pt-5">
                                        <p class="mb-0 text-heading">{!! \App\Helpers\Helper::smsStatus($notification->status) !!}</p>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-12 text-end">
                            {{ $notifications->appends(\Request::except('page'))->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
