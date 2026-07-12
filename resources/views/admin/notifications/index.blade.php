@extends('layouts.app')
@section('content')
    <div class="row g-6">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title"><i class="ti tabler-bell-ringing"></i> Listagem de Notificações</h5>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table border-top">
                            <thead class="border-bottom">
                            <tr style="background-color: #f1f1f1">
                                <th>Destinatário</th>
                                <th>Texto</th>
                                <th>Data do Serviço</th>
                                <th>Hora de Início</th>
                                <th>Hora de Fim</th>
                                <th>Dia de Envio</th>
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
                                        <button type="button"
                                                class="btn btn-sm btn-icon btn-text-secondary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#smsTextModal"
                                                data-text="{{ $notification->text }}"
                                                title="Ver mensagem">
                                            <i class="icon-base ti tabler-message"></i>
                                        </button>
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
                                        <p class="mb-0 text-heading">{{ $notification->send_day }}</p>
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
                           {{-- <a href="{{ route('agenda.index') }}">Ver Agenda</a>--}}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="modal fade" id="smsTextModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Mensagem</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <p id="smsTextContent" class="mb-0" style="white-space: pre-wrap;"></p>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        const smsModal = document.getElementById('smsTextModal');
        smsModal.addEventListener('show.bs.modal', function (event) {
            const text = event.relatedTarget.getAttribute('data-text');
            document.getElementById('smsTextContent').textContent = text;
        });
    </script>
@endpush
