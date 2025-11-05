<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marcação | Agenda Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .booking-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }

        .form-title {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 25px;
        }
    </style>
    {!! RecaptchaV3::initJs() !!}
</head>
<body>

<div class="container py-5">
    <div class="text-center">
        @if(isset($portal))
            <img src="{{global_asset('storage/'.$portal->logo)}}" alt="Logo" class=logo" width="300" height="150">
        @endif
    </div>

    <div class="booking-container mt-3">
        <h2 class="form-title text-center">{{$portal->title ?? "Agenda Online"}}</h2>

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
