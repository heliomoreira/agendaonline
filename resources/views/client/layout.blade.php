<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Área de Cliente') | Agenda Online</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f6f7fb; }
        .navbar-brand { font-weight: 600; }
        .auth-card { max-width: 440px; margin: 5vh auto; }
        .card { border: none; border-radius: 1rem; box-shadow: 0 2px 12px rgba(0,0,0,.06); }
    </style>
    @stack('styles')
</head>
<body>

@auth('client')
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('client.dashboard') }}">
                <i class="ri-calendar-check-line text-primary fs-4"></i>
                <span>A minha conta</span>
            </a>
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('book.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="ri-add-line"></i> Nova Marcação
                </a>
                <form action="{{ route('client.logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-light">
                        <i class="ri-logout-box-r-line"></i> Sair
                    </button>
                </form>
            </div>
        </div>
    </nav>
@endauth

@if(session('success'))
    <div class="container mt-3">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
@endif
@if(session('error'))
    <div class="container mt-3">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
@endif

@yield('content')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
