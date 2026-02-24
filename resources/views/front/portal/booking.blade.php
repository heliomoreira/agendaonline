<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $portal->title ?? 'Jorge Nogueira' }} | Agenda Online</title>

    <link rel="icon" type="image/png" href="{{ global_asset('storage/' . ($portal->logo ?? 'default-logo.png')) }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">
    <link href="{{ global_asset('assets/css/booking-wizard.css') }}?v={{ time() }}" rel="stylesheet">

    <style>
        :root {
            --cor-primaria: {{ $portal->cor_primaria ?? '#2563eb' }};
            --cor-primaria-rgb: {{ $portal->cor_primaria_rgb ?? '37, 99, 235' }};
        }
        /* Todos os outros estilos agora estão em booking-wizard.css */
    </style>
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
                    <a class="btn btn-sm px-3 {{ $portal->button_background_color ? '' : 'btn-primary' }}"
                       href="/booking"
                       style="font-size:.8rem; border-radius:8px; @if($portal->button_background_color) background-color: {{ $portal->button_background_color }}; border-color: {{ $portal->button_background_color }}; {{ $portal->button_text_color ? 'color: ' . $portal->button_text_color . ';' : 'color: #fff;' }} @endif">
                        <i class="ri-calendar-check-line me-1"></i>Agendar
                    </a>
                </li>
             {{--   <li class="nav-item">
                    <a class="nav-link" href="/{{ $portal->slug ?? '' }}/cliente/login" style="font-size:.85rem">
                        <i class="ri-login-box-line me-1"></i>Entrar
                    </a>
                </li>--}}
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
             style="background-image: url('{{ global_asset(($portal->background_image ?? null) ? 'storage/' . $portal->background_image : 'assets/images/bg_massagens.jpeg') }}');">
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
                                               value="{{ old('client_name') }}" placeholder="O seu nome"
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
                                               value="{{ old('client_email') }}" placeholder="oseuemail@email.pt"
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
                                <p class="text-muted small mb-3">Escolha o método de pagamento para confirmar a marcação.</p>

                                <div class="alert alert-info border-0 mb-4 d-flex align-items-start gap-2" style="background: #eff6ff;">
                                    <i class="ri-information-line mt-1 flex-shrink-0 text-primary"></i>
                                    <div style="font-size: .82rem;">
                                        <strong>MB WAY:</strong> Selecione <strong>Multibanco</strong> e use a referência gerada para pagar através da app MB WAY.
                                    </div>
                                </div>
                                <div class="alert alert-light border mb-4">
                                    <div class="d-flex align-items-start gap-3">
                                        <i class="ri-price-tag-3-line fs-4 text-primary flex-shrink-0 mt-1"></i>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold mb-1" id="wizPaySvcName">—</div>
                                            <div id="wizPayAmount" class="text-muted small">—</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="alert alert-danger d-none align-items-start gap-2 mb-3"
                                     id="wizStripeErr">
                                    <i class="ri-error-warning-line mt-1 flex-shrink-0"></i>
                                    <span id="wizStripeErrMsg"></span>
                                </div>
                                <form id="wizPayForm">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Método de pagamento <span class="text-danger">*</span></label>
                                        <div id="wizCardElement"></div>
                                        <div id="wizCardErrors"></div>
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
                        <img src="{{ global_asset('storage/' . ($portal->logo ?? '')) }}" alt="" class="footer-logo">
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
                {{--<div class="col-lg-4">
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
                </div>--}}
            </div>
            <hr class="my-4 border-secondary">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 small text-white-50">
                <p class="mb-0">&copy; {{ date('Y') }} Agenda Online - Todos os direitos reservados.</p>
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
        paymentPercentage: {{ $paymentPercentage ?? 100 }},
        stripeKey:       '{{ $stripeKey ?? config('services.stripe.key') ?? env('STRIPE_KEY') ?? '' }}',
        csrfToken:       '{{ csrf_token() }}',
        routes: {
            professionals:  '/services/{id}/professionals',
            availableSlots: '{{ url('/available-slots') }}',
            bookSlot:       '{{ route('book.slot', ['admin' => false]) }}',
            @if($requiresPayment ?? false)
            paymentIntent:  '{{ url('/booking/payment-intent') }}',
            bookingSuccess: '{{ url('/booking/success') }}',
            @endif
        },
    };

    // Debug: verificar se Stripe key está configurada
    if (window.WizConfig.requiresPayment) {
        console.log('Payment required: Yes');
        console.log('Stripe key present:', window.WizConfig.stripeKey ? 'Yes (starts with ' + window.WizConfig.stripeKey.substring(0, 7) + '...)' : 'NO - MISSING!');
        if (!window.WizConfig.stripeKey) {
            console.error('❌ STRIPE KEY IS MISSING! Check your .env file and config/services.php');
            console.error('Expected: STRIPE_KEY=pk_test_... or STRIPE_KEY=pk_live_...');
        }
    }

</script>

@if($requiresPayment ?? false)
    <script src="https://js.stripe.com/v3/"></script>
@endif

<script src="{{ global_asset('assets/js/booking-wizard.js') }}?v={{ time() }}"></script>

@if($errors->any())
    <script>document.addEventListener('DOMContentLoaded', () => wizGo(4));</script>
@endif

</body>
</html>
