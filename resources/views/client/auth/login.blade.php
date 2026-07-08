@extends('client.layout')

@section('title', 'Entrar')

@section('content')
    <div class="container">
        <div class="auth-card">
            <div class="text-center mb-4">
                <i class="ri-calendar-check-line text-primary" style="font-size: 2.5rem;"></i>
                <h4 class="mt-2 mb-0">Bem-vindo(a) de volta</h4>
                <p class="text-muted">Entre na sua conta para gerir as suas marcações.</p>
            </div>

            <div class="card">
                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('client.login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" required autofocus>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Manter sessão iniciada</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ri-login-box-line"></i> Entrar
                        </button>
                    </form>
                </div>
            </div>

            <p class="text-center mt-3 text-muted">
                Ainda não tem conta?
                <a href="{{ route('client.register') }}" class="text-decoration-none">Criar conta</a>
            </p>
            <p class="text-center">
                <a href="{{ route('book.index') }}" class="text-muted text-decoration-none small">
                    <i class="ri-arrow-left-line"></i> Voltar às marcações
                </a>
            </p>
        </div>
    </div>
@endsection
