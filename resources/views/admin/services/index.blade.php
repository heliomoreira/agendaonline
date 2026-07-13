@extends('layouts.app')
@section('content')
    @php($filtersApplied = request()->hasAny(['search', 'status']))

    <div class="row mb-2">
        <div class="col-md-12">
            <h5 class="d-flex align-items-center gap-2 m-0">
                <i class="icon-base ti tabler-briefcase"></i>
                Serviços
            </h5>
            <hr class="my-2"/>
        </div>
    </div>

    <div class="row g-6">
        <div class="col-md-12">

            {{-- ===== Filtros ===== --}}
            <div class="card mb-6">
                <div class="card-body">
                    <form method="GET" action="{{ url()->current() }}">
                        <div class="row g-4 align-items-end">
                            <div class="col-md-6">
                                <label class="form-label" for="search">Pesquisar</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="icon-base ti tabler-search"></i></span>
                                    <input type="text" id="search" name="search" class="form-control"
                                           value="{{ request('search') }}"
                                           placeholder="Nome do serviço...">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label" for="status">Estado</label>
                                <select id="status" name="status" class="form-select">
                                    <option value="">Todos</option>
                                    <option value="1" @selected(request('status') === '1')>Ativo</option>
                                    <option value="0" @selected(request('status') === '0')>Inativo</option>
                                </select>
                            </div>

                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-primary waves-effect waves-light">
                                    <i class="icon-base ti tabler-filter me-1"></i> Filtrar
                                </button>
                                @if($filtersApplied)
                                    <a href="{{ url()->current() }}" class="btn btn-outline-secondary waves-effect" title="Limpar filtros">
                                        <i class="icon-base ti tabler-x"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ===== Tabela ===== --}}
            @if($services->isNotEmpty())
                <div class="card">
                    <div class="card-header header-elements">
                        <h5 class="mb-0 me-2">Listagem de Serviços</h5>
                        <div class="card-header-elements ms-auto">
                            <a href="{{ route('services.form') }}"
                               class="btn btn-primary waves-effect waves-light">
                                <span class="icon-base ti tabler-plus icon-xs me-1"></span>Novo Serviço
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover align-middle">
                                <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Duração</th>
                                    <th>Preço</th>
                                    <th>Ordem</th>
                                    <th>Estado</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                @foreach($services as $service)
                                    @php($editUrl = route('services.edit', ['id' => $service->id]))
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-3">
                                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                                        <i class="icon-base ti tabler-briefcase"></i>
                                                    </span>
                                                </div>
                                                <a href="{{ $editUrl }}" class="fw-medium">{{ $service->name }}</a>
                                            </div>
                                        </td>
                                        <td>{{ $service->duration }} min</td>
                                        <td>{{ number_format($service->price, 2, ',', '.') }} &euro;</td>
                                        <td>{{ $service->order }}</td>
                                        <td>
                                            <span class="badge bg-label-{{ $service->statusClass() }}">
                                                {{ $service->statusLabel() }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ $editUrl }}"
                                               class="btn btn-sm btn-icon btn-text-secondary waves-effect"
                                               title="Editar">
                                                <i class="icon-base ti tabler-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($services->total() > 0)
                        <div class="card-footer d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <small class="text-body-secondary">
                                A mostrar {{ $services->firstItem() }}–{{ $services->lastItem() }}
                                de {{ $services->total() }} serviço{{ $services->total() === 1 ? '' : 's' }}
                            </small>
                            @if($services->hasPages())
                                {{ $services->links() }}
                            @endif
                        </div>
                    @endif
                </div>
            @elseif($filtersApplied)
                <div class="card">
                    <div class="card-body text-center text-muted py-5">
                        <i class="icon-base ti tabler-search-off icon-lg d-block mb-2"></i>
                        <h6 class="mb-1">Nenhum serviço corresponde aos filtros</h6>
                        <p class="mb-3">Experimente ajustar ou limpar os critérios de pesquisa.</p>
                        <a href="{{ url()->current() }}" class="btn btn-outline-secondary waves-effect">
                            <i class="icon-base ti tabler-x me-1"></i> Limpar filtros
                        </a>
                    </div>
                </div>
            @else
                <x-empty-state
                    title="Nenhum serviço encontrado"
                    message="Ainda não foram adicionados registos à lista de serviços."
                    button="Novo Serviço"
                    link="{{ route('services.form') }}"
                />
            @endif
        </div>
    </div>
@endsection
