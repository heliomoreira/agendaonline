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

    <div class="row mb-2">
        <div class="col-md-12">
            <h5 class="d-flex align-items-center gap-2 m-0">
                <i class="icon-base ti tabler-user-shield"></i>
                {{ $user->id ? 'Editar Utilizador' : 'Novo Utilizador' }}
            </h5>
            <hr class="my-2"/>
        </div>
    </div>

    <div class="row g-6">
        <div class="col-md-12">
            <form method="POST"
                  action="{{ $user->id ? route('users.update', $user->id) : route('users.store') }}">
                @csrf
                @if($user->id)
                    @method('PUT')
                @endif

                <div class="card">
                    <div class="card-header header-elements">
                        <h5 class="mb-0 me-2">
                            Detalhe de Utilizador
                            {!! $user->name ? '| <span style="color:#2A7AD4">' . e($user->name) . '</span>' : '' !!}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-6">
                            <div class="col-md-6">
                                <label class="form-label" for="name">Nome</label>
                                <input type="text" class="form-control" id="name" name="name"
                                       value="{{ old('name', $user->name) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="username">Nome de Utilizador</label>
                                <input type="text" class="form-control" id="username" name="username"
                                       value="{{ old('username', $user->username) }}">
                            </div>
                        </div>
                        <div class="row g-6 mt-1">
                            <div class="col-md-12">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="{{ old('email', $user->email) }}">
                            </div>
                        </div>
                        <div class="row g-6 mt-1">
                            <div class="col-md-6">
                                <label class="form-label d-block" for="only_own_agenda">Acesso à agenda</label>
                                <label class="switch">
                                    <input type="hidden" name="only_own_agenda" value="0">
                                    <input type="checkbox" class="switch-input" id="only_own_agenda"
                                           name="only_own_agenda" value="1"
                                           {{ old('only_own_agenda', $user->only_own_agenda) ? 'checked' : '' }}
                                           onchange="document.getElementById('professionalWrapper').style.display = this.checked ? 'block' : 'none'">
                                    <span class="switch-toggle-slider">
                                        <span class="switch-on"></span>
                                        <span class="switch-off"></span>
                                    </span>
                                    <span class="switch-label">Ver apenas os próprios agendamentos</span>
                                </label>
                            </div>
                            <div class="col-md-6" id="professionalWrapper"
                                 style="display: {{ old('only_own_agenda', $user->only_own_agenda) ? 'block' : 'none' }};">
                                <label class="form-label" for="professional_id">Profissional associado</label>
                                <select class="form-select" id="professional_id" name="professional_id">
                                    <option value="">— Selecione —</option>
                                    @foreach($professionals as $id => $name)
                                        <option value="{{ $id }}"
                                            {{ (string) old('professional_id', $user->professional_id) === (string) $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row g-6 mt-1">
                            <div class="col-md-6 form-password-toggle">
                                <label class="form-label" for="password">
                                    Password {{ $user->id ? '(deixe em branco para manter)' : '' }}
                                </label>
                                <div class="input-group input-group-merge">
                                    <input type="password" class="form-control" id="password" name="password"
                                           placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;">
                                    <span class="input-group-text cursor-pointer">
                                        <i class="icon-base ti tabler-eye-off"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="password_confirmation">Confirmar Password</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                       name="password_confirmation"
                                       placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary waves-effect waves-light">
                                <i class="icon-base ti tabler-device-floppy"></i> Gravar
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-secondary waves-effect waves-light">
                                <i class="icon-base ti tabler-arrow-left"></i> Voltar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
