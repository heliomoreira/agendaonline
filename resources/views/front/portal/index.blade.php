<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/images/favicon.png">
    <title>{{ $portal?->title ?? 'Agenda Online' }} | Agenda Online</title>

    <link rel="icon" type="image/png" href="">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="{{ global_asset('assets/css/app.css') }}" rel="stylesheet">
</head>
<body>

{{-- ======================================================
     NAVBAR
====================================================== --}}
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="/">
            @if($portal?->logo)
                <img src="{{ global_asset('storage/' . $portal->logo) }}" alt="{{ $portal->title ?? '' }}" class="navbar-logo">
            @endif
            <span class="fw-semibold">{{ $portal?->title ?? '' }}</span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNav">
            <i class="ri-menu-line fs-4"></i>
        </button>

        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto align-items-center gap-3">
                <li class="nav-item">
                    <a class="nav-link" href="/">
                        <i class="ri-home-8-line me-1"></i>Início
                    </a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-sm px-3 {{ $portal?->button_background_color ? '' : 'btn-primary' }}"
                       @if($portal?->button_background_color)
                           style="background-color: {{ $portal->button_background_color }}; border-color: {{ $portal->button_background_color }}; {{ $portal->button_text_color ? 'color: ' . $portal->button_text_color . ';' : '' }}"
                       @endif
                       href="/booking">
                        <i class="ri-calendar-check-line me-1"></i>
                        Agendar
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

{{-- ======================================================
     OFFCANVAS MOBILE
====================================================== --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="mobileNav">
    <div class="offcanvas-header border-bottom">
        <div class="d-flex align-items-center gap-2">
            @if($portal?->logo)
                <img src="{{ global_asset('storage/' . $portal->logo) }}" alt="{{ $portal?->title ?? '' }}" class="navbar-logo">
            @endif
            <span class="fw-semibold">{{ $portal?->title ?? '' }}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <nav class="nav flex-column gap-1">
            <a class="nav-link px-3 py-2 rounded" href="/">
                <i class="ri-home-line me-2"></i>Início
            </a>
            <a class="nav-link px-3 py-2 rounded fw-semibold"
               @if($portal?->button_background_color)
                   style="background-color: {{ $portal->button_background_color }}; border-color: {{ $portal->button_background_color }}; {{ $portal->button_text_color ? 'color: ' . $portal->button_text_color . ';' : 'color: #fff;' }}"
               @else
                   style="color: var(--bs-primary);"
               @endif
               href="/booking">
                <i class="ri-calendar-check-line me-2"></i>Agendar
            </a>
        </nav>
    </div>
</div>

<main>

    {{-- ======================================================
         HERO
    ====================================================== --}}
    <section class="hero-section"
             style="background-image: url('{{ $portal?->background_image ? global_asset('storage/' . $portal->background_image) : global_asset('assets/images/bg_massagens.jpeg') }}');">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            @if($portal?->logo)
                <div class="hero-logo">
                    <img src="{{ global_asset('storage/' . $portal->logo) }}" alt="{{ $portal->title ?? '' }}">
                </div>
            @endif

            <h1 class="hero-title">{{ $portal?->title ?? '' }}</h1>

            <a href="/booking"
               class="btn btn-lg px-5 {{ $portal?->button_background_color ? '' : 'btn-primary' }}"
               @if($portal?->button_background_color)
                   style="background-color: {{ $portal->button_background_color }}; border-color: {{ $portal->button_background_color }}; {{ $portal->button_text_color ? 'color: ' . $portal->button_text_color . ';' : 'color: #fff;' }}"
                @endif>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="lucide lucide-calendar-plus">
                    <path d="M16 19h6"></path><path d="M16 2v4"></path><path d="M19 16v6"></path>
                    <path d="M21 12.598V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h8.5"></path>
                    <path d="M3 10h18"></path><path d="M8 2v4"></path>
                </svg>
                Agendar horário
            </a>

            <div class="hero-comodidades mt-4">
                <span class="comodidade-badge"><i class="ri-wifi-line"></i> Wi-Fi</span>
                <span class="comodidade-badge"><i class="ri-snowy-line"></i> Ar-condicionado</span>
                <span class="comodidade-badge"><i class="ri-cup-line"></i> Café</span>
                <span class="comodidade-badge"><i class="ri-function-line"></i> WC</span>
            </div>
        </div>
    </section>

    {{-- ======================================================
         INFO CARDS
    ====================================================== --}}
    <div class="container info-cards">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="info-card">
                    <div class="info-card-icon"><i class="ri-phone-line"></i></div>
                    <div class="info-card-content">
                        <div class="info-card-label">Telefone</div>
                        <div class="info-card-value">
                            <a href="tel:{{ $portal?->phone_1 ?? '' }}" class="text-decoration-none text-dark">
                                {{ $portal?->phone_1 ?? '—' }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-card">
                    <div class="info-card-icon"><i class="ri-map-pin-line"></i></div>
                    <div class="info-card-content">
                        <div class="info-card-label">Endereço</div>
                        <div class="info-card-value">{{ $portal?->address ?? '—' }}</div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-card">
                    <div class="info-card-icon"><i class="ri-time-line"></i></div>
                    <div class="info-card-content">
                        <div class="info-card-label">Horário | Hoje</div>
                        <div class="info-card-value" style="font-size: 0.9rem;">
                            {{ $todayHours ?? '—' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================
         SERVIÇOS
    ====================================================== --}}
    <section class="py-5">
        <div class="container">
            <h2 class="text-center fw-bold mb-2">Os nossos serviços</h2>
            <div class="row g-4">
                @forelse($services ?? [] as $service)
                    <div class="col-sm-6 col-lg-4">
                        <div class="service-card">
                            <div class="service-card-image">
                                <img src="storage/{{ $service->image }}" alt="{{ $service->name }}">
                            </div>
                            <div class="service-card-body">
                                <h5 class="service-card-title"
                                    @if($portal?->main_color) style="color: {{ $portal->main_color }};" @endif>
                                    {{ $service->name }}
                                </h5>
                                <div class="service-card-footer">
                                    <div class="service-card-price"
                                         @if($portal?->secondary_color) style="color: {{ $portal->secondary_color }};" @endif>
                                        {{ $service->price }}€
                                    </div>
                                    <div class="service-card-duration">
                                        <i class="ri-time-line me-1"></i>
                                        {{ $service->duration }} min
                                    </div>
                                </div>
                                <a href="/booking"
                                   class="btn btn-lg px-5 {{ $portal?->button_background_color ? '' : 'btn-primary' }}"
                                   @if($portal?->button_background_color)
                                       style="background-color: {{ $portal->button_background_color }}; border-color: {{ $portal->button_background_color }}; {{ $portal->button_text_color ? 'color: ' . $portal->button_text_color . ';' : 'color: #fff;' }}"
                                    @endif>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                         stroke-linejoin="round" class="lucide lucide-calendar-plus">
                                        <path d="M16 19h6"></path><path d="M16 2v4"></path><path d="M19 16v6"></path>
                                        <path d="M21 12.598V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h8.5"></path>
                                        <path d="M3 10h18"></path><path d="M8 2v4"></path>
                                    </svg>
                                    Agendar
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted">Nenhum serviço disponível de momento.</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ======================================================
         CTA
    ====================================================== --}}
    <section class="cta-section">
        <div class="container">
            <div class="text-center">
                <h2>Vamos agendar?</h2>
                <p>Escolha o melhor horário para si</p>
                <a href="/booking" class="btn btn-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="lucide lucide-calendar-plus">
                        <path d="M16 19h6"></path><path d="M16 2v4"></path><path d="M19 16v6"></path>
                        <path d="M21 12.598V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h8.5"></path>
                        <path d="M3 10h18"></path><path d="M8 2v4"></path>
                    </svg>
                    Agendar horário
                </a>
            </div>
        </div>
    </section>

    {{-- ======================================================
         SOBRE NÓS
    ====================================================== --}}
    @if($portal?->about_us)
        <section class="py-5 bg-white">
            <div class="container">
                <h2 class="text-center fw-bold mb-4">Um pouco sobre nós</h2>
                <div class="row g-4 justify-content-center">
                    {{ $portal->about_us }}
                </div>
            </div>
        </section>
    @endif

    {{-- ======================================================
         FOOTER
    ====================================================== --}}
    <footer class="app-footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        @if($portal?->logo)
                            <img src="{{ global_asset('storage/' . $portal->logo) }}" alt="" class="footer-logo">
                        @endif
                        <span class="h5 mb-0">{{ $portal?->title ?? '' }}</span>
                    </div>

                    @if($portal?->address)
                        <p class="text-white-50 small mb-2">
                            <i class="ri-map-pin-line me-2"></i>{{ $portal->address }}
                        </p>
                    @endif

                    @if($portal?->phone_1)
                        <p class="text-white-50 small mb-2">
                            <i class="ri-phone-line me-2"></i>{{ $portal->phone_1 }}
                        </p>
                    @endif
                </div>

                <div class="col-lg-4">
                    <h6 class="text-uppercase small mb-3">Horários</h6>
                    <ul class="list-unstyled small text-white-50">
                        @foreach([
                            'Seg' => $portal?->monday_hours,
                            'Ter' => $portal?->tuesday_hours,
                            'Qua' => $portal?->wednesday_hours,
                            'Qui' => $portal?->thursday_hours,
                            'Sex' => $portal?->friday_hours,
                            'Sáb' => $portal?->saturday_hours,
                            'Dom' => $portal?->sunday_hours,
                        ] as $day => $hours)
                            <li class="d-flex justify-content-between py-1 border-bottom border-secondary">
                                <span>{{ $day }}</span>
                                <span>
                                @if($hours)
                                        {{ $hours }}
                                    @else
                                        <span class="text-danger">Fechado</span>
                                    @endif
                            </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <hr class="my-4 border-secondary">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 small text-white-50">
                <p class="mb-0">&copy; {{ date('Y') }} Agenda Online - Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

</main>

<div class="toast-container position-fixed top-0 end-0 p-3" id="toastContainer"></div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ global_asset('assets/js/app.js') }}"></script>

</body>
</html>
