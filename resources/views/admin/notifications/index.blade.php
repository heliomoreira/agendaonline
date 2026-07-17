@extends('layouts.app')
@section('content')
    <div class="row mb-4">
        <div class="col-md-12">
            <h5 class="d-flex align-items-center gap-2 m-0">
                <i class="icon-base ti tabler-bell-ringing"></i>
                Notificações
            </h5>
            <hr class="my-2"/>
        </div>
    </div>

    {{-- ===== Filtros ===== --}}
    <div class="row g-6 mb-2">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ url()->current() }}">
                        <div class="row g-4 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label" for="search">Pesquisar</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="icon-base ti tabler-search"></i></span>
                                    <input type="text" id="search" name="search" class="form-control"
                                           value="{{ request('search') }}"
                                           placeholder="Destinatário ou mensagem...">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label" for="recipient_type">Destinatário</label>
                                <select id="recipient_type" name="recipient_type" class="form-select">
                                    <option value="">Todos</option>
                                    <option value="client" @selected(request('recipient_type') === 'client')>Cliente</option>
                                    <option value="parent" @selected(request('recipient_type') === 'parent')>Encarregado</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label" for="status">Estado</label>
                                <select id="status" name="status" class="form-select">
                                    <option value="">Todos</option>
                                    <option value="scheduled" @selected(request('status') === 'scheduled')>Agendado</option>
                                    <option value="sent" @selected(request('status') === 'sent')>Enviado</option>
                                    <option value="failed" @selected(request('status') === 'failed')>Falhado</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label" for="date_from">Serviço de</label>
                                <input type="date" id="date_from" name="date_from" class="form-control"
                                       value="{{ request('date_from') }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label" for="date_to">Serviço até</label>
                                <input type="date" id="date_to" name="date_to" class="form-control"
                                       value="{{ request('date_to') }}">
                            </div>

                            <div class="col-12 d-flex gap-2 align-items-center">
                                <button type="submit" class="btn btn-primary waves-effect waves-light">
                                    <i class="icon-base ti tabler-filter me-1"></i> Filtrar
                                </button>
                                @if(request()->hasAny(['search', 'recipient_type', 'status', 'date_from', 'date_to', 'show_all']))
                                    <a href="{{ url()->current() }}" class="btn btn-outline-secondary waves-effect">
                                        <i class="icon-base ti tabler-x me-1"></i> Limpar
                                    </a>
                                @endif

                                <div class="form-check form-switch ms-auto mb-0">
                                    <input class="form-check-input" type="checkbox" id="show_all" name="show_all" value="1"
                                        @checked(request('show_all'))>
                                    <label class="form-check-label" for="show_all">Incluir notificações passadas</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Tabela ===== --}}
    <div class="row g-6">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header header-elements">
                    <h5 class="mb-0 me-2"><i class="ti tabler-bell-ringing"></i> Listagem de Notificações</h5>
                    <div class="card-header-elements ms-auto">
                        <span class="text-muted small">{{ $notifications->total() }} registo(s)</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover align-middle">
                            <thead>
                            <tr>
                                <th>Destinatário</th>
                                <th class="text-center">Mensagem</th>
                                <th>Data do Serviço</th>
                                <th>Horário</th>
                                <th>Envio</th>
                                <th>Estado</th>
                            </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                            @forelse($notifications as $notification)
                                @php
                                    $isParent = $notification->recipient_type === 'parent';
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3">
                                                <span class="avatar-initial rounded-circle {{ $isParent ? 'bg-label-warning' : 'bg-label-primary' }}">
                                                    <i class="icon-base ti {{ $isParent ? 'tabler-shield-half-filled' : 'tabler-user' }}"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <span class="fw-medium">{{ $notification->destinatary }}</span>
                                                @if($isParent)
                                                    <span class="badge rounded-pill bg-label-warning fw-normal ms-1">Encarregado</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button type="button"
                                                class="btn btn-sm btn-icon btn-text-secondary rounded-pill"
                                                data-bs-toggle="modal"
                                                data-bs-target="#smsTextModal"
                                                data-text="{{ $notification->text }}"
                                                title="Ver mensagem">
                                            <i class="icon-base ti tabler-message"></i>
                                        </button>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($notification->service_day)->format('d/m/Y') }}</td>
                                    <td>{{ $notification->service_start_hour }} – {{ $notification->service_end_hour }}</td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span>{{ $notification->send_day }}</span>
                                            <small class="text-muted">às {{ $notification->send_hour }}h</small>
                                        </div>
                                    </td>
                                    <td>{!! \App\Helpers\Helper::smsStatus($notification->status) !!}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="icon-base ti tabler-inbox icon-lg d-block mb-2"></i>
                                        Nenhuma notificação corresponde aos filtros.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($notifications->hasPages())
                    <div class="card-footer">
                        {{ $notifications->links() }}
                    </div>
                @endif
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
