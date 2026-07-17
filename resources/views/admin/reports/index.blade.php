@extends('layouts.app')
@section('content')
    @include('admin._partials.alerts')

    <div class="row mb-2">
        <div class="col-md-12">
            <h5 class="d-flex align-items-center gap-2 m-0">
                <i class="icon-base ti tabler-page-a" aria-hidden="true"></i> Relatórios
            </h5>
            <hr class="my-2"/>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="nav-align-top nav-tabs-shadow">
                <ul class="nav nav-tabs nav-fill" role="tablist">
                    <li class="nav-item">
                        <button
                            type="button"
                            class="nav-link active"
                            role="tab"
                            data-bs-toggle="tab"
                            data-bs-target="#navs-justified-finance"
                            aria-controls="navs-justified-finance"
                            aria-selected="true">
                          <span class="d-none d-sm-inline-flex align-items-center">
                            <i class="icon-base ti tabler-currency-euro icon-sm me-1_5" aria-hidden="true"></i>Financeiro
                          </span>
                            <i class="icon-base ti tabler-currency-euro icon-sm d-sm-none" aria-hidden="true"></i>
                        </button>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="navs-justified-finance" role="tabpanel">

                        {{-- Cabeçalho do separador: contagem + total em dívida --}}
                        @php
                            $totalUnpaid = $unpaid->sum(fn ($u) => optional($u->service)->price ?? 0);
                        @endphp
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                            <span class="text-body-secondary">
                                {{ $unpaid->count() }} marcaç{{ $unpaid->count() === 1 ? 'ão' : 'ões' }} por pagar
                            </span>
                            <span class="fw-semibold">
                                Total em dívida: {{ number_format($totalUnpaid, 2, ',', '.') }} &euro;
                            </span>
                        </div>

                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover align-middle">
                                <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Serviço</th>
                                    <th>Preço</th>
                                    <th>Data/hora</th>
                                    <th>Pagamento</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                @forelse($unpaid as $un)
                                    @php
                                        $service = $un->service;
                                        $editUrl = $service ? route('agenda.show', ['agenda' => $un]) : null;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-3">
                                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                                        <i class="icon-base ti tabler-briefcase" aria-hidden="true"></i>
                                                    </span>
                                                </div>
                                                @if($editUrl)
                                                    <a href="{{ $editUrl }}" class="fw-medium">{{ $un->client?->name ?? '—' }}</a>
                                                @else
                                                    <span class="fw-medium">{{ $un->client?->name ?? '—' }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $service?->name ?? '—' }}</td>
                                        <td>{{ number_format($service?->price ?? 0, 2, ',', '.') }} &euro;</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span>{{ $un->day ? \Carbon\Carbon::parse($un->day)->format('d-m-Y') : '—' }}</span>
                                                @if($un->start_hour)
                                                    <small class="text-body-secondary">
                                                        <i class="icon-base ti tabler-clock icon-xs me-1" aria-hidden="true"></i>{{ $un->start_hour }} - {{ $un->end_hour }}
                                                    </small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($un->paymentStatus)
                                                <span class="badge bg-label-{{ $un->paymentStatus->color }}">
                                                    {{ $un->paymentStatus->name }}
                                                </span>
                                            @else
                                                <span class="badge bg-label-secondary">—</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if($editUrl)
                                                <a href="{{ $editUrl }}"
                                                   class="btn btn-sm btn-icon btn-text-secondary waves-effect"
                                                   title="Editar">
                                                    <i class="icon-base ti tabler-edit" aria-hidden="true"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">
                                            <div class="text-center py-5">
                                                <i class="icon-base ti tabler-checks text-body-secondary mb-2" style="font-size:2.5rem;" aria-hidden="true"></i>
                                                <p class="mb-0 text-body-secondary">Não há marcações por pagar.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                                @if($unpaid->count())
                                    <tfoot>
                                    <tr>
                                        <td colspan="2" class="text-end fw-semibold">Total em dívida</td>
                                        <td class="fw-semibold">{{ number_format($totalUnpaid, 2, ',', '.') }} &euro;</td>
                                        <td colspan="3"></td>
                                    </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')

@endpush
