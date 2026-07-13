@extends('layouts.app')
@section('content')
    @php($filtersApplied = request()->hasAny(['search', 'status']))

    <div class="row mb-2">
        <div class="col-md-12">
            <h5 class="d-flex align-items-center gap-2 m-0">
                <i class="icon-base ti tabler-briefcase"></i>
                Profissionais
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
                                           placeholder="Nome, telemóvel ou email...">
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
            @if($professionals->isNotEmpty())
                <div class="card">
                    <div class="card-header header-elements">
                        <h5 class="mb-0 me-2">Listagem de Profissionais</h5>
                        <div class="card-header-elements ms-auto">
                            <a href="{{ route('professionals.form') }}"
                               class="btn btn-primary waves-effect waves-light">
                                <span class="icon-base ti tabler-plus icon-xs me-1"></span>Novo Profissional
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
                                    <th>Estado</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                @foreach($professionals as $professional)
                                    @php($editUrl = route('professionals.edit', ['id' => $professional->id]))
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-3">
                                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                                        <i class="icon-base ti tabler-user"></i>
                                                    </span>
                                                </div>
                                                <a href="{{ $editUrl }}" class="fw-medium">{{ $professional->name }}</a>
                                            </div>
                                        </td>
                                        <td>
                                            @if($professional->phone_1)
                                                <a href="{{ $editUrl }}">{{ $professional->phone_1 }}</a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($professional->email)
                                                <a href="mailto:{{ $professional->email }}">{{ $professional->email }}</a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-label-{{ $professional->statusClass() }}">
                                                {{ $professional->statusLabel() }}
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

                    @if($professionals->total() > 0)
                        <div class="card-footer d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <small class="text-body-secondary">
                                A mostrar {{ $professionals->firstItem() }}–{{ $professionals->lastItem() }}
                                de {{ $professionals->total() }} profissiona{{ $professionals->total() === 1 ? 'l' : 'is' }}
                            </small>
                            @if($professionals->hasPages())
                                {{ $professionals->links() }}
                            @endif
                        </div>
                    @endif
                </div>
            @elseif($filtersApplied)
                <div class="card">
                    <div class="card-body text-center text-muted py-5">
                        <i class="icon-base ti tabler-search-off icon-lg d-block mb-2"></i>
                        <h6 class="mb-1">Nenhum profissional corresponde aos filtros</h6>
                        <p class="mb-3">Experimente ajustar ou limpar os critérios de pesquisa.</p>
                        <a href="{{ url()->current() }}" class="btn btn-outline-secondary waves-effect">
                            <i class="icon-base ti tabler-x me-1"></i> Limpar filtros
                        </a>
                    </div>
                </div>
            @else
                <x-empty-state
                    title="Nenhum profissional encontrado"
                    message="Ainda não foram adicionados registos à lista de profissionais."
                    button="Novo Profissional"
                    link="{{ route('professionals.form') }}"
                />
            @endif
        </div>
    </div>
@endsection
