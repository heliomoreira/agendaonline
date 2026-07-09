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

    @php
        // Data amigável + dia da semana (mesmo padrão da listagem)
        try {
            $date      = \Carbon\Carbon::parse($agenda->day)->locale('pt_PT');
            $dateLabel = $date->translatedFormat('d \d\e F \d\e Y');
            $weekday   = ucfirst($date->translatedFormat('l'));
        } catch (\Throwable $e) {
            $dateLabel = $agenda->day; $weekday = '';
        }

        // Iniciais do profissional
        $parts = array_values(array_filter(explode(' ', trim($agenda->professional->name ?? ''))));
        $proInitials = mb_strtoupper(
            mb_substr($parts[0] ?? '', 0, 1) .
            (count($parts) > 1 ? mb_substr(end($parts), 0, 1) : '')
        );

        // Valor da data de pagamento para o input date (Y-m-d)
        $paidValue = $agenda->paid_at
            ? \Carbon\Carbon::parse($agenda->paid_at)->format('Y-m-d')
            : '';
    @endphp

    {{-- ===== Cabeçalho da página ===== --}}
    <div class="row mb-2">
        <div class="col-md-12">
            <h5 class="d-flex align-items-center gap-2 m-0">
                <i class="icon-base ti tabler-calendar-event"></i>
                Detalhe da Marcação
            </h5>
            <hr class="my-2"/>
        </div>
    </div>

    <div class="row g-6">
        <div class="col-md-12">
            {{ html()->modelForm($agenda, 'PUT', route('agenda.update', $agenda->id))->open() }}
            {{ html()->token() }}

            <div class="card">
                <div class="card-header header-elements">
                    <h5 class="mb-0 me-2">
                        Detalhe da Marcação
                        {!! $agenda->id ? '| <span style="color:#2A7AD4">#' . $agenda->id . '</span>' : '' !!}
                    </h5>
                </div>

                <div class="card-body">
                    {{-- ===== Dados da marcação (só leitura) ===== --}}
                    <div class="row g-6">
                        {{-- Serviço --}}
                        <div class="col-sm-6 col-lg-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-3">
                <span class="avatar-initial bg-label-primary rounded">
                    <i class="icon-base ti tabler-briefcase"></i>
                </span>
                                </div>
                                <div>
                                    <small class="text-body-secondary d-block">Serviço</small>
                                    <span class="fw-medium text-heading">{{ $agenda->service->name }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Valor --}}
                        <div class="col-sm-6 col-lg-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-3">
                <span class="avatar-initial bg-label-success rounded">
                    <i class="icon-base ti tabler-currency-euro"></i>
                </span>
                                </div>
                                <div>
                                    <small class="text-body-secondary d-block">Valor</small>
                                    <span class="fw-medium text-heading">
                    @if(!is_null($agenda->service->price))
                                            {{ number_format($agenda->service->price, 2, ',', '.') }} €
                                        @else
                                            —
                                        @endif
                </span>
                                </div>
                            </div>
                        </div>

                        {{-- Profissional --}}
                        <div class="col-sm-6 col-lg-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-3">
                                    <span class="avatar-initial bg-label-secondary rounded-circle">{{ $proInitials }}</span>
                                </div>
                                <div>
                                    <small class="text-body-secondary d-block">Profissional</small>
                                    <span class="fw-medium text-heading">{{ $agenda->professional->name }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Data --}}
                        <div class="col-sm-6 col-lg-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-3">
                <span class="avatar-initial bg-label-warning rounded">
                    <i class="icon-base ti tabler-calendar-event"></i>
                </span>
                                </div>
                                <div>
                                    <small class="text-body-secondary d-block">Data</small>
                                    <span class="fw-medium text-heading">{{ $dateLabel }}</span>
                                    @if($weekday)<small class="text-body-secondary d-block">{{ $weekday }}</small>@endif
                                </div>
                            </div>
                        </div>

                        {{-- Horário --}}
                        <div class="col-sm-6 col-lg-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-3">
                <span class="avatar-initial bg-label-info rounded">
                    <i class="icon-base ti tabler-clock"></i>
                </span>
                                </div>
                                <div>
                                    <small class="text-body-secondary d-block">Horário</small>
                                    <span class="fw-medium text-heading">{{ $agenda->start_hour }}h – {{ $agenda->end_hour }}h</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4"/>

                    {{-- ===== Cliente (só leitura, antes do pagamento) ===== --}}
                    <h6 class="mb-3"><i class="icon-base ti tabler-user me-2"></i>Cliente</h6>
                    @if($agenda->client)
                        <div class="row g-6">
                            <div class="col-md-4">
                                <label class="form-label">Nome</label>
                                <div class="fw-medium text-heading">{{ $agenda->client->name }}</div>
                            </div>
                            @if(!empty($agenda->client->email))
                                <div class="col-md-4">
                                    <label class="form-label">Email</label>
                                    <div><a href="mailto:{{ $agenda->client->email }}" class="text-heading">{{ $agenda->client->email }}</a></div>
                                </div>
                            @endif
                            @if(!empty($agenda->client->phone_1))
                                <div class="col-md-4">
                                    <label class="form-label">Telefone</label>
                                    <div><a href="tel:{{ $agenda->client->phone_1 }}" class="text-heading">{{ $agenda->client->phone_1 }}</a></div>
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="mb-0 text-body-secondary">Marcação sem cliente associado.</p>
                    @endif

                    <hr class="my-4"/>

                    {{-- ===== Pagamento (editável) ===== --}}
                    <h6 class="mb-3"><i class="icon-base ti tabler-cash me-2"></i>Pagamento</h6>
                    <div class="row g-6">
                        <div class="col-md-3">
                            <label class="form-label" for="payment_status_id">Estado do pagamento</label>
                            {{ html()->select('payment_status_id', $paymentStatuses->pluck('name', 'id')->toArray())
                                    ->id('payment_status_id')
                                    ->class('form-select') }}
                        </div>

                        <div class="col-md-3">
                            <label class="form-label" for="paid_at">Data de pagamento</label>
                            {{ html()->date('paid_at')
                                    ->value(old('paid_at', $paidValue))
                                    ->id('paid_at')
                                    ->class('form-control') }}
                            <small class="text-body-secondary">
                                Deixe em branco para preencher automaticamente conforme o estado.
                            </small>
                        </div>
                    </div>

                    <hr class="my-4"/>

                    {{-- ===== Observações (editável) ===== --}}
                    <div class="row g-6">
                        <div class="col-md-12">
                            <label class="form-label" for="notes">Observações</label>
                            {{ html()->textarea('notes')
                                    ->id('notes')
                                    ->class('form-control')
                                    ->rows(4)
                                    ->placeholder('Notas internas sobre a marcação...') }}
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex flex-wrap justify-content-between gap-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                            <i class="icon-base ti tabler-device-floppy"></i> Gravar
                        </button>
                        <a href="{{ route('agenda.list') }}" class="btn btn-secondary waves-effect waves-light">
                            <i class="icon-base ti tabler-arrow-left"></i> Voltar
                        </a>
                    </div>

                    <button type="button" id="cancelEvent"
                            class="btn btn-outline-danger waves-effect"
                            data-url="{{ route('agenda.cancel-event', $agenda->id) }}"
                            data-redirect="{{ route('agenda.list') }}">
                        <i class="icon-base ti tabler-trash"></i> Cancelar marcação
                    </button>
                </div>
            </div>

            {{ html()->closeModelForm() }}
        </div>
    </div>

    <script>
        document.getElementById('cancelEvent')?.addEventListener('click', async function () {
            if (!confirm('Tem a certeza que quer cancelar esta marcação? Esta ação não pode ser revertida.')) return;

            const btn   = this;
            const token = document.querySelector('meta[name="csrf-token"]')?.content
                || document.querySelector('input[name="_token"]')?.value;

            btn.disabled = true;

            try {
                const res = await fetch(btn.dataset.url, {
                    method: 'DELETE', // ajustar ao verbo real da rota (ver php artisan route:list)
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();

                if (res.ok && data.success) {
                    window.location = btn.dataset.redirect;
                } else {
                    alert('Não foi possível cancelar a marcação.');
                    btn.disabled = false;
                }
            } catch (e) {
                alert('Ocorreu um erro ao cancelar.');
                btn.disabled = false;
            }
        });
    </script>
@endsection
