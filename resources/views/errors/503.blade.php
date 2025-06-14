<!doctype html>

<html
    lang="en"
    class="layout-wide customizer-hide"
    dir="ltr"
    data-skin="default"
    data-assets-path="/theme/assets/"
    data-template="vertical-menu-template-no-customizer"
    data-bs-theme="light">
<head>
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Demo: Coming Soon - Pages | Vuexy - Bootstrap Dashboard PRO</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{asset('theme/assets/img/favicon/favicon.ico')}}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="{{asset('theme/assets/vendor/fonts/iconify-icons.css')}}" />

    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css  -->

    <link rel="stylesheet" href="{{asset('theme/assets/vendor/libs/node-waves/node-waves.css')}}" />

    <link rel="stylesheet" href="{{asset('theme/assets/vendor/css/core.css')}}" />
    <link rel="stylesheet" href="{{asset('theme/assets/css/demo.css')}}" />

    <!-- Vendors CSS -->

    <link rel="stylesheet" href="{{asset('theme/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')}}" />

    <!-- endbuild -->

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="{{asset('theme/assets/vendor/css/pages/page-misc.css')}}" />

    <!-- Helpers -->
    <script src="{{asset('theme/assets/vendor/js/helpers.js')}}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->

    <script src="{{asset('theme/assets/js/config.js')}}"></script>
</head>

<body>
<!-- Content -->

<!-- Coming Soon -->
<div class="container-xxl container-p-y py-4">
    <div class="misc-wrapper">
        <h4 class="mb-2 mx-2">Brevemente 🚀</h4>
        <p class="mb-6 mx-2">Our platform is opening soon!</p>
      {{--  <form onsubmit="return false">
            <div class="mb-0">
                <div class="mb-0 d-flex gap-4">
                    <input type="text" class="form-control" placeholder="Enter your email" autofocus />
                    <button type="submit" class="btn btn-primary">Notify</button>
                </div>
            </div>
        </form>--}}
        <div class="mt-12">
            <img
                src="{{asset('theme/assets/img/illustrations/page-misc-launching-soon.png')}}"
                alt="page-misc-launching-soon"
                width="263"
                class="img-fluid" />
        </div>
    </div>
</div>
<div class="container-fluid misc-bg-wrapper">
    <img
        src="{{asset('theme/assets/img/illustrations/bg-shape-image-light.png')}}"
        height="355"
        alt="page-misc-coming-soon"
        data-app-light-img="illustrations/bg-shape-image-light.png"
        data-app-dark-img="illustrations/bg-shape-image-dark.png" />
</div>
<!-- /Coming Soon -->

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

<!-- Main JS -->

<script src="{{asset('theme/assets/js/main.js')}}"></script>

<!-- Page JS -->
</body>
</html>
