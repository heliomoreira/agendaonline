<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $portal->title ?? '' }} | Agenda Online</title>

    <link rel="icon" type="image/png" href="{{ global_asset('storage/' . ($portal->logo ?? 'default-logo.png')) }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">

    <style>
        :root {
            --cor-primaria: {{ $portal->cor_primaria ?? '#2563eb' }};
            --cor-primaria-rgb: {{ $portal->cor_primaria_rgb ?? '37, 99, 235' }};
            --wizard-radius: 16px;
            --card-radius: 12px;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            margin: 0;
        }

        /* ─── NAVBAR ─────────────────────────────── */
        .navbar {
            padding: .6rem 0;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
        }
        .navbar-logo {
            height: 34px;
            width: 34px;
            border-radius: 8px;
            object-fit: cover;
        }

        /* ─── HERO (mais compacto) ───────────────── */
        .hero-section {
            position: relative;
            min-height: 260px;
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            /* Espaço extra no fundo para o wizard sobrepor */
            padding-bottom: 80px;
        }
        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(15,23,42,.82) 0%, rgba(30,58,138,.72) 100%);
        }
        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 2rem 1rem 0;
        }
        .hero-logo {
            width: 72px;
            height: 72px;
            border-radius: 16px;
            overflow: hidden;
            margin: 0 auto 1rem;
            border: 3px solid rgba(255,255,255,.25);
            box-shadow: 0 8px 24px rgba(0,0,0,.25);
        }
        .hero-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .hero-title {
            color: #fff;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: -.02em;
        }

        /* ─── WIZARD SECTION ─────────────────────── */
        .wizard-section {
            /* Margem negativa para sobrepor ao hero */
            margin-top: -64px;
            position: relative;
            z-index: 10;
            padding-bottom: 3rem;
        }

        .wiz-card {
            background: #fff;
            border-radius: var(--wizard-radius);
            box-shadow:
                0 1px 3px rgba(0,0,0,.06),
                0 8px 32px rgba(0,0,0,.08);
            max-width: 820px;
            margin: 0 auto;
            overflow: hidden;
        }

        /* ─── STEPPER NAV ────────────────────────── */
        .wiz-nav {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .35rem;
            padding: 1rem 1.25rem;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            flex-wrap: wrap;
        }
        .wiz-nav-item {
            display: flex;
            align-items: center;
            gap: .4rem;
            font-size: .72rem;
            font-weight: 500;
            color: #94a3b8;
            transition: color .2s;
        }
        .wiz-nav-item.active {
            color: var(--cor-primaria);
        }
        .wiz-nav-item.done {
            color: #059669;
        }
        .wiz-nav-badge {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .65rem;
            font-weight: 700;
            background: #e2e8f0;
            color: #94a3b8;
            transition: all .2s;
        }
        .wiz-nav-item.active .wiz-nav-badge {
            background: var(--cor-primaria);
            color: #fff;
            box-shadow: 0 2px 8px rgba(var(--cor-primaria-rgb), .3);
        }
        .wiz-nav-item.done .wiz-nav-badge {
            background: #059669;
            color: #fff;
        }
        .wiz-nav-sep {
            color: #cbd5e1;
            font-size: .7rem;
            line-height: 1;
        }

        /* ─── PANELS ─────────────────────────────── */
        .wiz-panel {
            display: none;
            padding: 1.5rem;
        }
        .wiz-panel.active {
            display: block;
            animation: wizFadeIn .25s ease;
        }

        @keyframes wizFadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .wiz-panel h5 {
            font-size: .95rem;
            letter-spacing: -.01em;
        }
        .wiz-panel p.text-muted {
            font-size: .78rem;
        }

        /* ─── SERVICE CARDS (compactas) ──────────── */
        .service-card {
            position: relative;
            border: 2px solid #e2e8f0;
            border-radius: var(--card-radius);
            overflow: hidden;
            cursor: pointer;
            transition: all .2s;
            background: #fff;
        }
        .service-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(0,0,0,.06);
            transform: translateY(-2px);
        }
        .service-card.selected {
            border-color: var(--cor-primaria);
            box-shadow: 0 0 0 3px rgba(var(--cor-primaria-rgb), .12);
        }

        .wiz-sel-mark {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: var(--cor-primaria);
            color: #fff;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: .7rem;
            z-index: 3;
            box-shadow: 0 2px 6px rgba(var(--cor-primaria-rgb),.3);
        }
        .service-card.selected .wiz-sel-mark {
            display: flex;
        }

        .service-card-image {
            height: 110px;
            overflow: hidden;
            background: #f8fafc;
        }
        .service-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .3s;
        }
        .service-card:hover .service-card-image img {
            transform: scale(1.04);
        }

        .service-card-body {
            padding: .75rem .85rem;
        }
        .service-card-title {
            font-size: .82rem;
            font-weight: 600;
            margin-bottom: .25rem;
            color: #1e293b;
        }
        .service-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .5rem;
            margin-top: .35rem;
        }
        .service-card-price {
            font-size: .78rem;
            font-weight: 700;
            color: var(--cor-primaria);
        }
        .service-card-duration {
            font-size: .68rem;
            color: #94a3b8;
            display: flex;
            align-items: center;
        }

        /* ─── PROFESSIONAL CARDS (compactas) ─────── */
        .pro-card {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .75rem;
            border: 2px solid #e2e8f0;
            border-radius: var(--card-radius);
            cursor: pointer;
            transition: all .2s;
        }
        .pro-card:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
        }
        .pro-card.selected {
            border-color: var(--cor-primaria);
            background: rgba(var(--cor-primaria-rgb), .03);
        }
        .pro-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            object-fit: cover;
            flex-shrink: 0;
        }
        .pro-avatar-placeholder {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--cor-primaria);
            font-weight: 700;
            font-size: .75rem;
            flex-shrink: 0;
        }
        .pro-name {
            font-size: .82rem;
            font-weight: 600;
        }
        .pro-role {
            font-size: .68rem;
            color: #94a3b8;
        }

        /* ─── CALENDAR ───────────────────────────── */
        .wiz-cg {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 3px;
        }
        .wiz-cw {
            text-align: center;
            font-size: .6rem;
            font-weight: 600;
            text-transform: uppercase;
            color: #94a3b8;
            padding: .35rem 0;
        }
        .wiz-cd {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .72rem;
            font-weight: 500;
            border-radius: 8px;
            cursor: pointer;
            transition: all .15s;
        }
        .wiz-cd:hover:not(.wiz-cd-off) {
            background: rgba(var(--cor-primaria-rgb), .08);
        }
        .wiz-cd.wiz-cd-sel {
            background: var(--cor-primaria);
            color: #fff;
            font-weight: 700;
        }
        .wiz-cd.wiz-cd-off {
            color: #d1d5db;
            cursor: default;
        }

        .wiz-cal-nb {
            background: none;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #475569;
            transition: all .15s;
            font-size: .85rem;
        }
        .wiz-cal-nb:hover {
            background: #f1f5f9;
        }

        /* ─── LEGEND DOTS ────────────────────────── */
        .wiz-ldot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            border: 1.5px solid;
            display: inline-block;
        }

        /* ─── TIME SLOTS ─────────────────────────── */
        .wiz-slots-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(72px, 1fr));
            gap: 6px;
        }
        .wiz-slot {
            padding: .4rem .5rem;
            text-align: center;
            font-size: .72rem;
            font-weight: 500;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            transition: all .15s;
        }
        .wiz-slot:hover {
            border-color: var(--cor-primaria);
            background: rgba(var(--cor-primaria-rgb), .04);
        }
        .wiz-slot.selected {
            background: var(--cor-primaria);
            color: #fff;
            border-color: var(--cor-primaria);
        }

        /* ─── SUMMARY SIDEBAR ────────────────────── */
        .wiz-summary {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: var(--card-radius);
            padding: 1rem;
        }
        .wiz-summary h6 {
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #94a3b8;
            margin-bottom: .75rem;
        }
        .wiz-sum-row {
            display: flex;
            align-items: flex-start;
            gap: .5rem;
            font-size: .78rem;
            margin-bottom: .5rem;
        }
        .wiz-sum-row i {
            color: var(--cor-primaria);
            margin-top: 2px;
            flex-shrink: 0;
            font-size: .8rem;
        }
        .wiz-sum-row strong {
            font-weight: 600;
        }

        /* ─── ACTIONS BAR ────────────────────────── */
        .wiz-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid #f1f5f9;
        }
        .wiz-actions .btn {
            font-size: .8rem;
            border-radius: 10px;
        }
        .wiz-actions .btn-primary {
            background: var(--cor-primaria);
            border-color: var(--cor-primaria);
        }
        .wiz-actions .btn-primary:hover {
            filter: brightness(1.08);
        }
        .wiz-actions .btn-primary:disabled {
            opacity: .5;
        }

        /* ─── FORM STYLES ────────────────────────── */
        .form-control {
            font-size: .82rem;
            border-radius: 10px;
            border-color: #e2e8f0;
            padding: .5rem .75rem;
        }
        .form-control:focus {
            border-color: var(--cor-primaria);
            box-shadow: 0 0 0 3px rgba(var(--cor-primaria-rgb), .1);
        }
        .form-label {
            font-size: .75rem;
            margin-bottom: .3rem;
        }

        /* ─── SEARCH INPUT ───────────────────────── */
        .wiz-search {
            position: relative;
            max-width: 280px;
            margin-bottom: 1rem;
        }
        .wiz-search i {
            position: absolute;
            left: .7rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            pointer-events: none;
            font-size: .8rem;
        }
        .wiz-search input {
            padding-left: 2rem;
            font-size: .78rem;
        }

        /* ─── SPINNER ────────────────────────────── */
        .wiz-spin {
            width: 18px;
            height: 18px;
            border: 2px solid #e2e8f0;
            border-top-color: var(--cor-primaria);
            border-radius: 50%;
            animation: wizSpin .6s linear infinite;
        }
        .wiz-spin-dk {
            border-color: rgba(0,0,0,.1);
            border-top-color: var(--cor-primaria);
        }
        @keyframes wizSpin { to { transform: rotate(360deg); } }

        /* ─── FOOTER ─────────────────────────────── */
        .app-footer {
            background: #0f172a;
            color: #fff;
            padding: 2.5rem 0 1.5rem;
        }
        .footer-logo {
            height: 36px;
            width: 36px;
            border-radius: 10px;
            object-fit: cover;
        }
        .app-footer h6 {
            font-size: .68rem;
            letter-spacing: .06em;
            color: #94a3b8;
        }

        /* ─── MOBILE SUMMARY ─────────────────────── */
        .wiz-mob-sum {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: var(--card-radius);
            padding: .75rem;
            font-size: .75rem;
        }

        /* ─── RESPONSIVE ─────────────────────────── */
        @media (max-width: 768px) {
            .hero-section {
                min-height: 200px;
                padding-bottom: 60px;
            }
            .wizard-section {
                margin-top: -48px;
            }
            .wiz-card {
                border-radius: 14px;
                margin: 0 .5rem;
            }
            .wiz-panel {
                padding: 1.15rem;
            }
            .wiz-nav {
                padding: .75rem;
                gap: .2rem;
            }
            .wiz-nav-item span:not(.wiz-nav-badge) {
                display: none;
            }
            .wiz-nav-item.active span:not(.wiz-nav-badge) {
                display: inline;
            }
            .service-card-image {
                height: 90px;
            }
        }

        @media (max-width: 576px) {
            .wiz-nav-sep { font-size: .6rem; }
        }
    </style>
    <link href="{{global_asset('assets/css/booking-wizard.css')}}" rel="stylesheet">
    @php
        try {
            if (class_exists('Lunaweb\RecaptchaV3\RecaptchaV3')) {
                echo RecaptchaV3::initJs();
            }
        } catch (\Exception $e) {
            // RecaptchaV3 not available
        }
    @endphp
</head>
<body>

<!-- ─── NAVBAR ──────────────────────────────────── -->
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="/{{ $portal->slug ?? '' }}">
            <img src="{{ global_asset('storage/' . ($portal->logo ?? '')) }}" alt="" class="navbar-logo">
            <span class="fw-semibold" style="font-size:.9rem">{{ $portal->title ?? '' }}</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNav">
            <i class="ri-menu-line fs-4"></i>
        </button>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto align-items-center gap-3">
                <li class="nav-item">
                    <a class="nav-link" href="/{{ $portal->slug ?? '' }}" style="font-size:.85rem">
                        <i class="ri-home-8-line me-1"></i>Início
                    </a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-primary btn-sm px-3" href="/{{ $portal->slug ?? '' }}/agendamento" style="font-size:.8rem; border-radius:8px">
                        <i class="ri-calendar-check-line me-1"></i>Agendar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/{{ $portal->slug ?? '' }}/cliente/login" style="font-size:.85rem">
                        <i class="ri-login-box-line me-1"></i>Entrar
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- ─── OFFCANVAS MOBILE ───────────────────────── -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="mobileNav">
    <div class="offcanvas-header border-bottom">
        <div class="d-flex align-items-center gap-2">
            <img src="{{ global_asset('storage/' . ($portal->logo ?? '')) }}" alt="" class="navbar-logo">
            <span class="fw-semibold">{{ $portal->title ?? '' }}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <nav class="nav flex-column gap-1">
            <a class="nav-link px-3 py-2 rounded" href="/{{ $portal->slug ?? '' }}">
                <i class="ri-home-line me-2"></i>Início
            </a>
            <a class="nav-link px-3 py-2 rounded text-primary fw-semibold" href="/{{ $portal->slug ?? '' }}/agendamento">
                <i class="ri-calendar-check-line me-2"></i>Agendar
            </a>
            <hr class="my-2">
            <a class="nav-link px-3 py-2 rounded" href="/{{ $portal->slug ?? '' }}/cliente/login">
                <i class="ri-login-box-line me-2"></i>Entrar
            </a>
        </nav>
    </div>
</div>

<main>

    <!-- ─── HERO ───────────────────────────────── -->
    <section class="hero-section"
             style="background-image: url('{{ global_asset('assets/images/default-background-empresa.webp') }}');">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <div class="hero-logo">
                <img src="{{ global_asset('storage/' . ($portal->logo ?? '')) }}" alt="{{ $portal->title ?? '' }}">
            </div>
            <h1 class="hero-title">{{ $portal->title ?? '' }}</h1>
        </div>
    </section>

    <!-- ─── WIZARD ─────────────────────────────── -->
    <section class="wizard-section">
        <div class="container">
            <div class="wiz-card">

                {{-- Stepper --}}
                <div class="wiz-nav">
                    <div class="wiz-nav-item active" id="wizT1">
                        <div class="wiz-nav-badge">1</div><span>Serviço</span>
                    </div>
                    <span class="wiz-nav-sep"><i class="ri-arrow-right-s-line"></i></span>
                    <div class="wiz-nav-item" id="wizT2">
                        <div class="wiz-nav-badge">2</div><span>Profissional</span>
                    </div>
                    <span class="wiz-nav-sep"><i class="ri-arrow-right-s-line"></i></span>
                    <div class="wiz-nav-item" id="wizT3">
                        <div class="wiz-nav-badge">3</div><span>Data &amp; Hora</span>
                    </div>
                    <span class="wiz-nav-sep"><i class="ri-arrow-right-s-line"></i></span>
                    <div class="wiz-nav-item" id="wizT4">
                        <div class="wiz-nav-badge">4</div><span>Os seus dados</span>
                    </div>
                    @if($requiresPayment ?? false)
                        <span class="wiz-nav-sep"><i class="ri-arrow-right-s-line"></i></span>
                        <div class="wiz-nav-item" id="wizT5">
                            <div class="wiz-nav-badge">5</div><span>Pagamento</span>
                        </div>
                    @endif
                </div>

                {{-- ── P1: Serviço ─────────────────────────── --}}
                <div class="wiz-panel active" id="wizP1">
                    <h5 class="fw-bold mb-1">Que serviço pretende?</h5>
                    <p class="text-muted small mb-4">Clique no serviço que deseja agendar.</p>

                    @if(count($services) >= 7)
                        <div class="position-relative mb-4" style="max-width:320px">
                            <i class="ri-search-line position-absolute"
                               style="left:.75rem;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none"></i>
                            <input type="text" id="wizQ" class="form-control ps-4"
                                   placeholder="Pesquisar serviço…" oninput="wizFilter(this.value)">
                        </div>
                    @endif

                    <div class="row g-4" id="wizSvcGrid">
                        @foreach($services as $svc)
                            <div class="col-sm-6 col-lg-4 wiz-col"
                                 data-q="{{ strtolower($svc->name . ' ' . ($svc->description ?? '')) }}">
                                <div class="service-card"
                                     data-id="{{ $svc->id }}"
                                     data-name="{{ $svc->name }}"
                                     data-duration="{{ $svc->duration ?? 30 }}"
                                     data-price="{{ isset($svc->price) ? number_format($svc->price, 2, ',', '.') . ' €' : '' }}"
                                     onclick="wizSelSvc(this)">
                                    <div class="wiz-sel-mark"><i class="ri-check-line"></i></div>
                                    <div class="service-card-image">
                                        @if(isset($svc->image) && $svc->image)
                                            <img src="{{ global_asset('storage/' . $svc->image) }}" alt="{{ $svc->name }}">
                                        @else
                                            <div class="d-flex align-items-center justify-content-center bg-light"
                                                 style="height:150px;color:#d1d5db;font-size:2.5rem">
                                                <i class="ri-scissors-line"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="service-card-body">
                                        <h5 class="service-card-title">{{ $svc->name }}</h5>
                                        @if($svc->description ?? false)
                                            <p class="text-muted mb-2" style="font-size:.78rem;line-height:1.4">
                                                {{ Str::limit($svc->description, 60) }}
                                            </p>
                                        @endif
                                        <div class="service-card-footer">
                                            @if(isset($svc->price))
                                                <div class="service-card-price">{{ number_format($svc->price, 2, ',', '.') }}€</div>
                                            @endif
                                            @if(isset($svc->duration))
                                                <div class="service-card-duration">
                                                    <i class="ri-time-line me-1"></i>
                                                    @php $h = floor($svc->duration/60); $m = $svc->duration%60; @endphp
                                                    {{ $h > 0 ? $h.'h' : '' }}{{ $m > 0 ? $m.'min' : '' }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="wiz-actions">
                        <span></span>
                        <button class="btn btn-primary px-4" id="wizBtn1" disabled onclick="wizGo(2)">
                            Continuar <i class="ri-arrow-right-line ms-1"></i>
                        </button>
                    </div>
                </div>

                {{-- ── P2: Profissional ────────────────────── --}}
                <div class="wiz-panel" id="wizP2">
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <h5 class="fw-bold mb-1">Com quem?</h5>
                            <p class="text-muted small mb-4">Selecione o profissional ou opte por "sem preferência".</p>
                            <div id="wizProList">
                                <div class="d-flex align-items-center gap-2 py-4 text-muted">
                                    <div class="wiz-spin wiz-spin-dk"></div> A carregar profissionais…
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 d-none d-lg-block"><div id="wizSum2"></div></div>
                    </div>
                    <div class="wiz-actions">
                        <button class="btn btn-outline-secondary" onclick="wizGo(1)">
                            <i class="ri-arrow-left-line me-1"></i> Voltar
                        </button>
                        <button class="btn btn-primary px-4" id="wizBtn2" disabled onclick="wizGo(3)">
                            Continuar <i class="ri-arrow-right-line ms-1"></i>
                        </button>
                    </div>
                </div>

                {{-- ── P3: Data & Hora ─────────────────────── --}}
                <div class="wiz-panel" id="wizP3">
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <h5 class="fw-bold mb-1">Quando?</h5>
                            <p class="text-muted small mb-4">Clique num dia disponível para ver os horários livres.</p>
                            <div class="border rounded-3 p-3 mb-3">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <button class="wiz-cal-nb" id="wizPrev" disabled><i class="ri-arrow-left-s-line"></i></button>
                                    <span class="fw-bold text-capitalize" id="wizCalLbl"></span>
                                    <button class="wiz-cal-nb" id="wizNext"><i class="ri-arrow-right-s-line"></i></button>
                                </div>
                                <div class="wiz-cg" id="wizCal">
                                    @foreach(['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'] as $day)
                                        <div class="wiz-cw">{{ $day }}</div>
                                    @endforeach
                                </div>
                                <div class="d-flex flex-wrap gap-3 mt-3 pt-2 border-top" style="font-size:.67rem;color:#6b7280">
                            <span class="d-flex align-items-center gap-1">
                                <span class="wiz-ldot" style="background:#ecfdf5;border-color:#059669"></span>Muita disponibilidade
                            </span>
                                    <span class="d-flex align-items-center gap-1">
                                <span class="wiz-ldot" style="background:#fffbeb;border-color:#d97706"></span>Disponibilidade média
                            </span>
                                    <span class="d-flex align-items-center gap-1">
                                <span class="wiz-ldot" style="background:#fef2f2;border-color:#dc2626"></span>Últimas vagas
                            </span>
                                    <span class="d-flex align-items-center gap-1">
                                <span class="wiz-ldot" style="background:#f8fafc;border-color:#e5e7eb"></span>Sem disponibilidade
                            </span>
                                </div>
                            </div>
                            <div id="wizSlots" style="display:none"></div>
                        </div>
                        <div class="col-lg-4 d-none d-lg-block"><div id="wizSum3"></div></div>
                    </div>
                    <div class="wiz-actions">
                        <button class="btn btn-outline-secondary" onclick="wizGo(2)">
                            <i class="ri-arrow-left-line me-1"></i> Voltar
                        </button>
                        <button class="btn btn-primary px-4" id="wizBtn3" disabled onclick="wizGo(4)">
                            Continuar <i class="ri-arrow-right-line ms-1"></i>
                        </button>
                    </div>
                </div>

                {{-- ── P4: Dados do cliente ────────────────── --}}
                <div class="wiz-panel" id="wizP4">
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <h5 class="fw-bold mb-1">Os seus dados</h5>
                            <p class="text-muted small mb-4">Preencha os seus contactos para confirmar a marcação.</p>

                            <div class="wiz-mob-sum d-lg-none mb-4" id="wizMobSum" style="display:none!important"></div>

                            @if($errors->any())
                                <div class="alert alert-danger d-flex align-items-start gap-2 mb-4">
                                    <i class="ri-error-warning-line mt-1 flex-shrink-0"></i>
                                    <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
                                </div>
                            @endif

                            <form id="wizForm" action="{{ route('book.slot', ['admin' => false]) }}" method="POST">
                                @csrf
                                @php
                                    try {
                                        if (class_exists('Lunaweb\RecaptchaV3\RecaptchaV3')) {
                                            echo RecaptchaV3::field('register');
                                        }
                                    } catch (\Exception $e) {
                                        // RecaptchaV3 not available, skip
                                    }
                                @endphp
                                <input type="hidden" name="service_id"      id="wizFS">
                                <input type="hidden" name="professional_id" id="wizFP">
                                <input type="hidden" name="day"             id="wizFD">
                                <input type="hidden" name="start_hour"      id="wizFT">

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Nome completo <span class="text-danger">*</span></label>
                                        <input type="text" name="client_name" id="wizFN"
                                               class="form-control {{ $errors->has('client_name') ? 'is-invalid' : '' }}"
                                               value="{{ old('client_name') }}" placeholder="João Silva"
                                               autocomplete="name" required>
                                        <div class="invalid-feedback">Campo obrigatório</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Telemóvel <span class="text-danger">*</span></label>
                                        <input type="tel" name="client_phone_1" id="wizFPh"
                                               class="form-control {{ $errors->has('client_phone_1') ? 'is-invalid' : '' }}"
                                               value="{{ old('client_phone_1') }}" placeholder="912 345 678"
                                               autocomplete="tel" required>
                                        <div class="invalid-feedback">Número obrigatório</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">E-mail</label>
                                        <input type="email" name="client_email" id="wizFE"
                                               class="form-control {{ $errors->has('client_email') ? 'is-invalid' : '' }}"
                                               value="{{ old('client_email') }}" placeholder="joao@email.pt"
                                               autocomplete="email">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Notas</label>
                                        <textarea name="notes" class="form-control" rows="2"
                                                  placeholder="Alguma preferência ou informação relevante…"></textarea>
                                    </div>
                                </div>

                                <div class="wiz-actions">
                                    <button type="button" class="btn btn-outline-secondary" onclick="wizGo(3)">
                                        <i class="ri-arrow-left-line me-1"></i> Voltar
                                    </button>
                                    @if($requiresPayment ?? false)
                                        <button type="button" class="btn btn-primary px-4" id="wizBtn4" onclick="wizGoPayment()">
                                            <i class="ri-bank-card-line me-1"></i> Continuar para pagamento
                                            <i class="ri-arrow-right-line ms-1"></i>
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-primary px-4" id="wizSub">
                                            <i class="ri-check-line me-1"></i> Confirmar Marcação
                                        </button>
                                    @endif
                                </div>
                            </form>
                        </div>
                        <div class="col-lg-4 d-none d-lg-block"><div id="wizSum4"></div></div>
                    </div>
                </div>

                {{-- ── P5: Pagamento (opcional) ────────────── --}}
                @if($requiresPayment ?? false)
                    <div class="wiz-panel" id="wizP5">
                        <div class="row g-4">
                            <div class="col-lg-8">
                                <h5 class="fw-bold mb-1">Pagamento</h5>
                                <p class="text-muted small mb-4">Introduza os dados do cartão para confirmar a marcação.</p>
                                <div class="alert alert-light border mb-4 d-flex align-items-center gap-3">
                                    <i class="ri-price-tag-3-line fs-4 text-primary flex-shrink-0"></i>
                                    <div>
                                        <div class="fw-bold" id="wizPaySvcName">—</div>
                                        <div class="text-muted small" id="wizPayAmount">—</div>
                                    </div>
                                </div>
                                <div class="alert alert-danger d-flex align-items-start gap-2 mb-3"
                                     id="wizStripeErr" style="display:none">
                                    <i class="ri-error-warning-line mt-1 flex-shrink-0"></i>
                                    <span id="wizStripeErrMsg"></span>
                                </div>
                                <form id="wizPayForm">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Dados do cartão <span class="text-danger">*</span></label>
                                        <div id="wizCardElement" class="form-control d-flex align-items-center"
                                             style="height:44px;padding:.55rem .85rem"></div>
                                        <div id="wizCardErrors" class="text-danger mt-1" style="font-size:.78rem"></div>
                                    </div>
                                    <div class="wiz-actions">
                                        <button type="button" class="btn btn-outline-secondary" onclick="wizGo(4)">
                                            <i class="ri-arrow-left-line me-1"></i> Voltar
                                        </button>
                                        <button type="submit" class="btn btn-primary px-4" id="wizPayBtn">
                                            <i class="ri-secure-payment-line me-1"></i> Pagar e Confirmar
                                        </button>
                                    </div>
                                </form>
                                <p class="text-muted mt-3 mb-0" style="font-size:.72rem">
                                    <i class="ri-shield-check-line me-1"></i>
                                    Pagamento seguro processado por Stripe. Os seus dados não são armazenados.
                                </p>
                            </div>
                            <div class="col-lg-4 d-none d-lg-block"><div id="wizSum5"></div></div>
                        </div>
                    </div>
                @endif

            </div>{{-- /wiz-card --}}
        </div>{{-- /container --}}
    </section>

    <!-- ─── FOOTER ─────────────────────────────── -->
    <footer class="app-footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="{{global_asset('storage/' . ($portal->logo ?? ''))}}" alt="" class="footer-logo">
                        <span class="h6 mb-0">{{ $portal->title ?? '' }}</span>
                    </div>
                    @if($portal->address ?? null)
                        <p class="text-white-50 small mb-2">
                            <i class="ri-map-pin-line me-2"></i>{{ $portal->address }}
                        </p>
                    @endif
                    @if($portal->phone ?? null)
                        <p class="text-white-50 small mb-2">
                            <i class="ri-phone-line me-2"></i>{{ $portal->phone }}
                        </p>
                    @endif
                </div>
                <div class="col-lg-4">
                    @if(isset($portal->schedule) && count($portal->schedule))
                        <h6 class="text-uppercase small mb-3">Horários</h6>
                        <ul class="list-unstyled small text-white-50">
                            @foreach($portal->schedule as $dayName => $hours)
                                <li class="d-flex justify-content-between py-1 border-bottom border-secondary">
                                    <span>{{ $dayName }}</span>
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
                    @endif
                </div>
                <div class="col-lg-4">
                    <h6 class="text-uppercase small mb-3">Redes Sociais</h6>
                    <div class="d-flex gap-2 flex-wrap mb-4">
                        @if($portal->instagram ?? null)
                            <a href="{{ $portal->instagram }}" target="_blank" class="btn btn-outline-light btn-sm"><i class="ri-instagram-line"></i></a>
                        @endif
                        @if($portal->facebook ?? null)
                            <a href="{{ $portal->facebook }}" target="_blank" class="btn btn-outline-light btn-sm"><i class="ri-facebook-line"></i></a>
                        @endif
                        @if($portal->whatsapp ?? null)
                            <a href="https://wa.me/{{ preg_replace('/\D/', '', $portal->whatsapp) }}" target="_blank" class="btn btn-outline-success btn-sm"><i class="ri-whatsapp-line"></i></a>
                        @endif
                    </div>
                    <a href="/{{ $portal->slug ?? '' }}/agendamento" class="btn btn-primary w-100" style="font-size:.82rem;border-radius:10px">
                        <i class="ri-calendar-check-line me-2"></i>Agendar Agora
                    </a>
                </div>
            </div>
            <hr class="my-4 border-secondary">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 small text-white-50">
                <p class="mb-0">&copy; {{ date('Y') }} meuhorario.ai - Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

{{-- RecaptchaV3 stub para evitar erros se não estiver carregado --}}
<script>
    if (typeof grecaptcha === 'undefined') {
        console.warn('RecaptchaV3 not loaded, using stub');
        window.grecaptcha = {
            ready: function(cb) { cb(); },
            execute: function() { return Promise.resolve('no-recaptcha-token'); }
        };
    }
</script>

{{-- Wizard Config --}}
<script>
    window.WizConfig = {
        corPrimaria:     '{{ $portal->cor_primaria ?? '#2563eb' }}',
        requiresPayment: {{ ($requiresPayment ?? false) ? 'true' : 'false' }},
        stripeKey:       '{{ ($requiresPayment ?? false) ? config('services.stripe.key') : '' }}',
        csrfToken:       '{{ csrf_token() }}',
        routes: {
            professionals:  '/services/{id}/professionals',
            availableSlots: '{{ url('/available-slots') }}',
            bookSlot:       '{{ route('book.slot', ['admin' => false]) }}',
            @if($requiresPayment ?? false)
            paymentIntent:  '{{ route('book.payment.intent') }}',
            @endif
        },
    };
</script>

@if($requiresPayment ?? false)
    <script src="https://js.stripe.com/v3/"></script>
@endif

<script src="{{ global_asset('assets/js/booking-wizard.js') }}"></script>

@if($errors->any())
    <script>document.addEventListener('DOMContentLoaded', () => wizGo(4));</script>
@endif

</body>
</html>
