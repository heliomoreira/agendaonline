@extends('layouts.app')
@section('content')
    @php($filtersApplied = request()->hasAny(['search', 'city', 'is_minor']))
    <div class="row mb-2">
        <div class="col-md-12">
            <h5 class="d-flex align-items-center gap-2 m-0">
                <i class="icon-base ti tabler-users"></i>
                Clientes
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
                            <div class="col-md-5">
                                <label class="form-label" for="search">Pesquisar</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="icon-base ti tabler-search"></i></span>
                                    <input type="text" id="search" name="search" class="form-control"
                                           value="{{ request('search') }}"
                                           placeholder="Nome, telemóvel ou email...">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label" for="city">Localidade</label>
                                <input type="text" id="city" name="city" class="form-control"
                                       value="{{ request('city') }}" placeholder="Localidade...">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label" for="is_minor">Menor de idade</label>
                                <select id="is_minor" name="is_minor" class="form-select">
                                    <option value="">Todos</option>
                                    <option value="1" @selected(request('is_minor') === '1')>Sim</option>
                                    <option value="0" @selected(request('is_minor') === '0')>Não</option>
                                </select>
                            </div>

                            <div class="col-md-2 d-flex gap-2">
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
            @if($clients->isNotEmpty())
                <div class="card">
                    <div class="card-header header-elements">
                        <h5 class="mb-0 me-2">Listagem de Clientes</h5>
                        <div class="card-header-elements ms-auto">
                            <a href="{{ route('clients.form') }}"
                               class="btn btn-primary waves-effect waves-light">
                                <span class="icon-base ti tabler-plus icon-xs me-1"></span>Novo Cliente
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover align-middle">
                                <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Contacto</th>
                                    <th>Email</th>
                                    <th>Localidade</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                @foreach($clients as $client)
                                    @php($editUrl = route('clients.edit', ['id' => $client->id]))
                                    <tr>
                                        <td>
                                            <i class="icon-base ti tabler-user icon-md me-2"></i>
                                            <a href="{{ $editUrl }}" class="fw-medium">{{ $client->name }}</a>
                                            @if($client->is_minor)
                                                <span class="badge rounded-pill bg-label-warning fw-normal ms-1">Menor</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($client->phone_1)
                                                <a href="{{ $editUrl }}">{{ $client->phone_1 }}</a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($client->email)
                                                <a href="{{ $editUrl }}">{{ $client->email }}</a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>{{ $client->city ?: '—' }}</td>
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

                    @if($clients->total() > 0)
                        <div class="card-footer d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <small class="text-body-secondary">
                                A mostrar {{ $clients->firstItem() }}–{{ $clients->lastItem() }}
                                de {{ $clients->total() }} cliente{{ $clients->total() === 1 ? '' : 's' }}
                            </small>
                            @if($clients->hasPages())
                                {{ $clients->links() }}
                            @endif
                        </div>
                    @endif
                </div>
            @elseif($filtersApplied)
                <div class="card">
                    <div class="card-body text-center text-muted py-5">
                        <i class="icon-base ti tabler-search-off icon-lg d-block mb-2"></i>
                        <h6 class="mb-1">Nenhum cliente corresponde aos filtros</h6>
                        <p class="mb-3">Experimente ajustar ou limpar os critérios de pesquisa.</p>
                        <a href="{{ url()->current() }}" class="btn btn-outline-secondary waves-effect">
                            <i class="icon-base ti tabler-x me-1"></i> Limpar filtros
                        </a>
                    </div>
                </div>
            @else
                <x-empty-state
                    title="Nenhum cliente encontrado"
                    message="Ainda não foram adicionados registos à lista de clientes."
                    button="Novo Cliente"
                    link="{{ route('clients.form') }}"
                />
            @endif
        </div>
    </div>
@endsection
