@extends('client.layout')

@section('title', 'Criar Conta')

@section('content')
    <div class="container">
        <div class="auth-card">
            <div class="text-center mb-4">
                <i class="ri-user-add-line text-primary" style="font-size: 2.5rem;"></i>
                <h4 class="mt-2 mb-0">Criar conta</h4>
                <p class="text-muted">Registe-se para acompanhar as suas marcações.</p>
            </div>

            <div class="card">
                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('client.register') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Nome</label>
                            <input type="text" id="name" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" required autofocus>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone_1" class="form-label">Telefone</label>
                            <input type="text" id="phone_1" name="phone_1"
                                   class="form-control @error('phone_1') is-invalid @enderror"
                                   value="{{ old('phone_1') }}" required>
                            @error('phone_1')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror" required>
                            <div class="form-text">Mínimo de 8 caracteres.</div>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirmar Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ri-user-add-line"></i> Criar conta
                        </button>
                    </form>
                </div>
            </div>

            <p class="text-center mt-3 text-muted">
                Já tem conta?
                <a href="{{ route('client.login') }}" class="text-decoration-none">Entrar</a>
            </p>
        </div>
    </div>
@endsection
