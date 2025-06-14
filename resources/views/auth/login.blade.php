<!doctype html>

<html
    lang="en"
    class="layout-wide customizer-hide"
    dir="ltr"
    data-skin="default"
    data-assets-path="/theme/assets/"
    data-template="vertical-menu-template-semi-dark"
    data-bs-theme="light">
<head>
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Agenda Online</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{asset('theme/assets/img/favicon/favicon.ico')}}"/>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link
            href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
            rel="stylesheet"/>

    <link rel="stylesheet" href="{{asset('theme/assets/vendor/fonts/iconify-icons.css')}}"/>

    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css  -->

    <link rel="stylesheet" href="{{asset('theme/assets/vendor/libs/node-waves/node-waves.css')}}"/>

    <link rel="stylesheet" href="{{asset('theme/assets/vendor/css/core.css')}}"/>
    <link rel="stylesheet" href="{{asset('theme/assets/css/demo.css')}}"/>

    <!-- Vendors CSS -->

    <link rel="stylesheet" href="{{asset('theme/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')}}"/>

    <!-- endbuild -->

    <!-- Vendor -->
    <link rel="stylesheet" href="{{asset('theme/assets/vendor/libs/@form-validation/form-validation.css')}}"/>

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="{{asset('theme/assets/vendor/css/pages/page-auth.css')}}"/>

    <!-- Helpers -->
    <script src="{{asset('theme/assets/vendor/js/helpers.js')}}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->

    <script src="{{asset('theme/assets/js/config.js')}}"></script>
</head>

<body>
<!-- Content -->

<div class="authentication-wrapper authentication-cover">
    <!-- Logo -->
    <a href="index.html" class="app-brand auth-cover-brand">
        <span class="app-brand-logo demo">
          <span class="text-primary">
            <svg width="32" height="22" viewBox="0 0 32 22" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                      fill-rule="evenodd"
                      clip-rule="evenodd"
                      d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z"
                      fill="currentColor"/>
              <path
                      opacity="0.06"
                      fill-rule="evenodd"
                      clip-rule="evenodd"
                      d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z"
                      fill="#161616"/>
              <path
                      opacity="0.06"
                      fill-rule="evenodd"
                      clip-rule="evenodd"
                      d="M8.07751 15.9175L13.9419 4.63989L16.5849 7.28475L8.07751 15.9175Z"
                      fill="#161616"/>
              <path
                      fill-rule="evenodd"
                      clip-rule="evenodd"
                      d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z"
                      fill="currentColor"/>
            </svg>
          </span>
        </span>
        <span class="app-brand-text demo text-heading fw-bold">Agenda Online</span>
    </a>
    <!-- /Logo -->
    <div class="authentication-inner row m-0">
        <!-- /Left Text -->
        <div class="d-none d-xl-flex col-xl-8 p-0">
            <div class="auth-cover-bg d-flex justify-content-center align-items-center">
                <img
                        src="{{asset('theme/assets/img/illustrations/auth-login-illustration-light.png')}}"
                        alt="auth-login-cover"
                        class="my-5 auth-illustration"
                        data-app-light-img="illustrations/auth-login-illustration-light.png"
                        data-app-dark-img="illustrations/auth-login-illustration-dark.png"/>
                <img
                        src="{{asset('theme/assets/img/illustrations/bg-shape-image-light.png')}}"
                        alt="auth-login-cover"
                        class="platform-bg"
                        data-app-light-img="illustrations/bg-shape-image-light.png"
                        data-app-dark-img="illustrations/bg-shape-image-dark.png"/>
            </div>
        </div>
        <!-- /Left Text -->

        <!-- Login -->
        <div class="d-flex col-12 col-xl-4 align-items-center authentication-bg p-sm-12 p-6">
            <div class="w-px-400 mx-auto mt-12 pt-5">
                <h4 class="mb-1">Bem vind@ à Agenda Online! 👋</h4>
                <p class="mb-6">Please sign-in to your account and start the adventure</p>

                <form id="formAuthentication" class="mb-6" action="#" method="GET">
                    <div class="mb-6 form-control-validation">
                        <label for="email" class="form-label">Email or Username</label>
                        <input
                                type="text"
                                class="form-control"
                                id="email"
                                name="email-username"
                                placeholder="Enter your email or username"
                                autofocus/>
                    </div>
                    <div class="mb-6 form-password-toggle form-control-validation">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-group input-group-merge">
                            <input
                                    type="password"
                                    id="password"
                                    class="form-control"
                                    name="password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="password"/>
                            <span class="input-group-text cursor-pointer"><i
                                        class="icon-base ti tabler-eye-off"></i></span>
                        </div>
                    </div>
                    <div class="my-8">
                        <div class="d-flex justify-content-between">
                            <div class="form-check mb-0 ms-2">
                                <input class="form-check-input" type="checkbox" id="remember-me"/>
                                <label class="form-check-label" for="remember-me"> Remember Me </label>
                            </div>
                            <a href="auth-forgot-password-cover.html">
                                <p class="mb-0">Forgot Password?</p>
                            </a>
                        </div>
                    </div>
                    <button class="btn btn-primary d-grid w-100">Sign in</button>
                </form>

                <p class="text-center">
                    <span>New on our platform?</span>
                    <a href="auth-register-cover.html">
                        <span>Create an account</span>
                    </a>
                </p>
            </div>
        </div>
        <!-- /Login -->
    </div>
</div>

<!-- / Content -->

<!-- Core JS -->
<!-- build:js assets/vendor/js/theme.js -->

<script src="{{asset('theme/assets/vendor/libs/jquery/jquery.js')}}"></script>

<script src="{{asset('theme/assets/vendor/libs/popper/popper.js')}}"></script>
<script src="{{asset('theme/assets/vendor/js/bootstrap.js')}}"></script>
<script src="{{asset('theme/assets/vendor/libs/node-waves/node-waves.js')}}"></script>

<script src="{{asset('theme/assets/vendor/libs/@algolia/autocomplete-js.js')}}"></script>

<script src="{{asset('theme/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')}}"></script>

<script src="{{asset('theme/assets/vendor/libs/hammer/hammer.js')}}"></script>

<script src="{{asset('theme/assets/vendor/libs/i18n/i18n.js')}}"></script>

<script src="{{asset('theme/assets/vendor/js/menu.js')}}"></script>

<!-- endbuild -->

<!-- Vendors JS -->
<script src="{{asset('theme/assets/vendor/libs/@form-validation/popular.js')}}"></script>
<script src="{{asset('theme/assets/vendor/libs/@form-validation/bootstrap5.js')}}"></script>
<script src="{{asset('theme/assets/vendor/libs/@form-validation/auto-focus.js')}}"></script>

<!-- Main JS -->

<script src="{{asset('theme/assets/js/main.js')}}"></script>

<!-- Page JS -->
<script src="{{asset('theme/assets/js/pages-auth.js')}}"></script>
</body>
</html>


{{--
<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
--}}
