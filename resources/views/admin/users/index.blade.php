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
                    <div class="flex-grow-1">{{ session('success') }}</div>
                    <button type="button" class="btn-close ms-2" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            </div>
        </div>
    @endif
    @if ($errors->any())
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-solid-danger d-flex align-items-center" role="alert">
                    <span class="alert-icon rounded"><i class="icon-base ti tabler-x"></i></span>
                    <div>{{ $errors->first() }}</div>
                </div>
            </div>
        </div>
    @endif

    <div class="row mb-2">
        <div class="col-md-12">
            <h5 class="d-flex align-items-center gap-2 m-0">
                <i class="icon-base ti tabler-users"></i>
                Utilizadores
            </h5>
            <hr class="my-2"/>
        </div>
    </div>

    <div class="row g-6">
        <div class="col-md-12">
            @if(count($users) > 0)
                <div class="card">
                    <div class="card-header header-elements">
                        <h5 class="mb-0 me-2">Listagem de Utilizadores</h5>
                        <div class="card-header-elements ms-auto">
                            <a href="{{ route('users.form') }}"
                               class="btn btn-primary waves-effect waves-light">
                                <span class="icon-base ti tabler-plus icon-xs me-1"></span>Novo Utilizador
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover align-middle">
                                <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Utilizador</th>
                                    <th>Email</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                @foreach($users as $user)
                                    @php($editUrl = route('users.edit', ['id' => $user->id]))
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-3">
                                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                                        <i class="icon-base ti tabler-user"></i>
                                                    </span>
                                                </div>
                                                <a href="{{ $editUrl }}" class="fw-medium">{{ $user->name }}</a>
                                            </div>
                                        </td>
                                        <td>{{ $user->username }}</td>
                                        <td>
                                            @if($user->email)
                                                <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ $editUrl }}"
                                               class="btn btn-sm btn-icon btn-text-secondary waves-effect"
                                               title="Editar">
                                                <i class="icon-base ti tabler-edit"></i>
                                            </a>
                                            @if(auth()->id() !== $user->id)
                                                <form action="{{ route('users.destroy', ['id' => $user->id]) }}"
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Eliminar este utilizador?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="btn btn-sm btn-icon btn-text-secondary waves-effect text-danger"
                                                            title="Eliminar">
                                                        <i class="icon-base ti tabler-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <x-empty-state
                    title="Nenhum utilizador encontrado"
                    message="Ainda não foram adicionados utilizadores administrativos."
                    button="Novo Utilizador"
                    link="{{ route('users.form') }}"
                />
            @endif
        </div>
    </div>
@endsection
