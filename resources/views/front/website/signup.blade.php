<!doctype html>
<html
    lang="pt"
    class="layout-wide customizer-hide"
    dir="ltr"
    data-skin="default"
    data-assets-path="/theme/assets/"
    data-template="vertical-menu-template-semi-dark"
    data-bs-theme="light">
<head>
    <meta charset="utf-8"/>
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>

    <title>Agenda Online</title>
    <meta name="description" content=""/>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ global_asset('theme/assets/img/favicon/favicon.ico') }}"/>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet"/>

    <link rel="stylesheet" href="{{ global_asset('theme/assets/vendor/fonts/iconify-icons.css') }}"/>

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ global_asset('theme/assets/vendor/libs/node-waves/node-waves.css') }}"/>
    <link rel="stylesheet" href="{{ global_asset('theme/assets/vendor/css/core.css') }}"/>
    <link rel="stylesheet" href="{{ global_asset('theme/assets/css/demo.css') }}"/>

    <!-- Vendors CSS -->
    <link rel="stylesheet"
          href="{{ global_asset('theme/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}"/>

    <!-- Vendor -->
    <link rel="stylesheet" href="{{ global_asset('theme/assets/vendor/libs/@form-validation/form-validation.css') }}"/>

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ global_asset('theme/assets/vendor/css/pages/page-auth.css') }}"/>

    <!-- Helpers -->
    <script src="{{ global_asset('theme/assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ global_asset('theme/assets/js/config.js') }}"></script>

    {!! RecaptchaV3::initJs() !!}
</head>

<body>
<!-- Content -->
<div class="authentication-wrapper authentication-cover">

    <!-- Logo -->
    <a href="/" class="app-brand auth-cover-brand">
        <span class="app-brand-logo demo">
            <span class="text-primary">
                <svg width="32" height="22" viewBox="0 0 32 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z"
                          fill="currentColor"/>
                    <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd"
                          d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z"
                          fill="#161616"/>
                    <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd"
                          d="M8.07751 15.9175L13.9419 4.63989L16.5849 7.28475L8.07751 15.9175Z"
                          fill="#161616"/>
                    <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z"
                          fill="currentColor"/>
                </svg>
            </span>
        </span>
        <span class="app-brand-text demo text-heading fw-bold">Agenda Online</span>
    </a>
    <!-- /Logo -->

    <div class="authentication-inner row m-0">

        <!-- Left illustration -->
        <div class="d-none d-xl-flex col-xl-8 p-0">
            <div class="auth-cover-bg d-flex justify-content-center align-items-center">
                <img
                    src="{{ global_asset('theme/assets/img/illustrations/auth-register-illustration-light.png') }}"
                    alt="auth-register-cover"
                    class="my-5 auth-illustration"
                    data-app-light-img="illustrations/auth-register-illustration-light.png"
                    data-app-dark-img="illustrations/auth-register-illustration-dark.png"/>
                <img
                    src="{{ global_asset('theme/assets/img/illustrations/bg-shape-image-light.png') }}"
                    alt="auth-register-cover"
                    class="platform-bg"
                    data-app-light-img="illustrations/bg-shape-image-light.png"
                    data-app-dark-img="illustrations/bg-shape-image-dark.png"/>
            </div>
        </div>
        <!-- /Left illustration -->

        <!-- Register Form -->
        <div class="d-flex col-12 col-xl-4 align-items-center authentication-bg p-sm-12 p-6">
            <div class="w-px-400 mx-auto mt-12 pt-5">
                <h4 class="mb-1">Criar conta 🚀</h4>
                <p class="mb-6 text-muted">Preencha os campos abaixo para criar a sua conta.</p>

                {{-- Erros globais (caso existam) --}}
         {{--       @if ($errors->any())
                    <div class="alert alert-danger mb-4" role="alert">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif--}}

                <form id="formAuthentication" class="mb-6" action="/signup/create-tenant" method="POST"
                      novalidate>
                    @csrf
                    {!! RecaptchaV3::field('register') !!}

                    {{-- Nome --}}
                    <div class="mb-4">
                        <label for="name" class="form-label">Nome</label>
                        <input
                            type="text"
                            class="form-control @error('name') is-invalid @enderror"
                            id="name"
                            name="name"
                            placeholder="O seu nome completo"
                            value="{{ old('name') }}"
                            autofocus/>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Nome de Utilizador --}}
                    <div class="mb-4">
                        <label for="username" class="form-label">Endereço da sua agenda</label>
                        <input
                            type="text"
                            class="form-control @error('username') is-invalid @enderror"
                            id="username"
                            name="username"
                            placeholder="ex: clinica-joao"
                            value="{{ old('username') }}"/>
                        @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            A sua agenda ficará disponível em: <strong>{{ old('username') ?: 'o-seu-nome' }}
                                .agendaonline.pt</strong>
                            Apenas letras, números e hífens.
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="mb-4">
                        <label for="email" class="form-label">Email</label>
                        <input
                            type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            id="email"
                            name="email"
                            placeholder="email@exemplo.com"
                            value="{{ old('email') }}"/>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-4">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-group input-group-merge">
                            <input
                                type="password"
                                id="password"
                                class="form-control @error('password') is-invalid @enderror"
                                name="password"
                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                aria-describedby="password"/>
                            <span class="input-group-text cursor-pointer">
                                <i class="icon-base ti tabler-eye-off"></i>
                            </span>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-text">Mínimo de 8 caracteres.</div>
                    </div>

                    {{-- Confirmação de Password --}}
                    <div class="mb-6">
                        <label class="form-label" for="password_confirmation">Confirmar Password</label>
                        <div class="input-group input-group-merge">
                            <input
                                type="password"
                                id="password_confirmation"
                                class="form-control"
                                name="password_confirmation"
                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"/>
                            <span class="input-group-text cursor-pointer">
                                <i class="icon-base ti tabler-eye-off"></i>
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary d-grid w-100">Registar</button>
                </form>
            </div>
        </div>
        <!-- /Register Form -->

    </div>
</div>
<!-- /Content -->

<!-- Core JS -->
<script src="{{ global_asset('theme/assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ global_asset('theme/assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ global_asset('theme/assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ global_asset('theme/assets/vendor/libs/node-waves/node-waves.js') }}"></script>
<script src="{{ global_asset('theme/assets/vendor/libs/@algolia/autocomplete-js.js') }}"></script>
<script src="{{ global_asset('theme/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ global_asset('theme/assets/vendor/libs/hammer/hammer.js') }}"></script>
<script src="{{ global_asset('theme/assets/vendor/libs/i18n/i18n.js') }}"></script>
<script src="{{ global_asset('theme/assets/vendor/js/menu.js') }}"></script>

<!-- Main JS -->
<script src="{{ global_asset('theme/assets/js/main.js') }}"></script>
<script src="{{ global_asset('theme/assets/js/pages-auth.js') }}"></script>

</body>
</html>
