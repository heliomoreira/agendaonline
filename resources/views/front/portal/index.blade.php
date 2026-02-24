<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/images/favicon.png">
    <title>{{$portal->title}} | Agenda Online</title>

    <link rel="icon" type="image/png" href="">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://agende.meuhorario.ai/css/app.css" rel="stylesheet">

    <meta name="csrf-param" content="_csrf-frontend">
    <meta name="csrf-token"
          content="melCABZ3w7T0cGVMNxZDRsBI8DlkgRqiDkdvdC5uvnTQjDVNZD_2_pIIV3gCQgQShCyqV1PQL80-MR03SyHQBw==">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="/">
            <img src="storage/{{$portal->logo}}" alt=""
                 class="navbar-logo">
            <span class="fw-semibold">{{$portal->title}}</span>
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
                    <a class="btn btn-primary btn-sm px-3" href="/booking">
                        <i class="ri-calendar-check-line me-1"></i>
                        Agendar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="ri-login-box-line me-1"></i>Entrar
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="offcanvas offcanvas-end" tabindex="-1" id="mobileNav">
    <div class="offcanvas-header border-bottom">
        <div class="d-flex align-items-center gap-2">
            <img
                src="https://jorgenogueira.agendaonline.pt/storage/tenants/logos/e3ddd66b-6942-4578-8166-e2e888eba879.png"
                alt=""
                class="navbar-logo">
            <span class="fw-semibold">>{{$portal->title}}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <nav class="nav flex-column gap-1">
            <a class="nav-link px-3 py-2 rounded" href="/">
                <i class="ri-home-line me-2"></i>Início
            </a>
            <a class="nav-link px-3 py-2 rounded text-primary fw-semibold" href="/booking">
                <i class="ri-calendar-check-line me-2"></i>Agendar
            </a>
            <hr class="my-2">
            <a class="nav-link px-3 py-2 rounded" href="#">
                <i class="ri-login-box-line me-2"></i>Entrar
            </a>
        </nav>
    </div>
</div>

<main>

    <section class="hero-section"
             style="background-image: url('{{global_asset('assets/images/bg_massagens.jpeg')}}');">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <div class="hero-logo">
                <img src="{{ global_asset('storage/' . ($portal->logo ?? '')) }}" alt="{{ $portal->title ?? '' }}">
            </div>

            <h1 class="hero-title">{{$portal->title}}</h1>
            <a href="/booking" class="btn btn-primary btn-lg px-5">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     data-lucide="calendar-plus" class="lucide lucide-calendar-plus">
                    <path d="M16 19h6"></path>
                    <path d="M16 2v4"></path>
                    <path d="M19 16v6"></path>
                    <path d="M21 12.598V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h8.5"></path>
                    <path d="M3 10h18"></path>
                    <path d="M8 2v4"></path>
                </svg>
                Agendar horário
            </a>

            <div class="hero-comodidades mt-4">
                                                            <span class="comodidade-badge">
                    <i class="ri-wifi-line"></i>
                    Wi-Fi                </span>
                {{--                <span class="comodidade-badge">
                                    <i class="ri-wheelchair-line"></i>
                                    Acessibilidade                </span>--}}
                {{--            <span class="comodidade-badge">
                                <i class="ri-parent-line"></i>
                                Crianças                </span>--}}
                <span class="comodidade-badge">
                    <i class="ri-snowy-line"></i>
                    Ar-condicionado                </span>
                <span class="comodidade-badge">
                    <i class="ri-cup-line"></i>
                    Café                </span>
                <span class="comodidade-badge">
                    <i class="ri-function-line"></i>
                    WC                </span>
            </div>
        </div>
    </section>

    <div class="container info-cards">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="ri-phone-line"></i>
                    </div>
                    <div class="info-card-content">
                        <div class="info-card-label">Telefone</div>
                        <div class="info-card-value">
                            <a href="" class="text-decoration-none text-dark">{{$portal->phone_1}}</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="ri-map-pin-line"></i>
                    </div>
                    <div class="info-card-content">
                        <div class="info-card-label">Endereço</div>
                        <div class="info-card-value">
                            {{$portal->address}}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="ri-time-line"></i>
                    </div>
                    <div class="info-card-content">
                        <div class="info-card-label">Horário</div>
                        <div class="info-card-value">
                            Hoje: 09:00 - 20:00
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <h2 class="text-center fw-bold mb-2">Os nossos serviços</h2>
            <div class="row g-4">
                @foreach($services as $service)
                    <div class="col-sm-6 col-lg-4">
                        <div class="service-card">
                            <div class="service-card-image">
                                <img src="storage/{{$service->image}}"
                                     alt="{{$service->name}}">
                            </div>
                            <div class="service-card-body">
                                <h5 class="service-card-title">{{$service->name}}</h5>
                                <div class="service-card-footer">
                                    <div class="service-card-price">
                                        {{$service->price}}€
                                    </div>
                                    <div class="service-card-duration">
                                        <i class="ri-time-line me-1"></i>
                                        {{$service->duration}} min
                                    </div>
                                </div>
                                <a href="/booking" class="btn btn-primary btn-lg px-5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                         stroke-linejoin="round" data-lucide="calendar-plus"
                                         class="lucide lucide-calendar-plus">
                                        <path d="M16 19h6"></path>
                                        <path d="M16 2v4"></path>
                                        <path d="M19 16v6"></path>
                                        <path
                                            d="M21 12.598V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h8.5"></path>
                                        <path d="M3 10h18"></path>
                                        <path d="M8 2v4"></path>
                                    </svg>
                                    Agendar
                                </a>
                            </div>

                        </div>
                    </div>
                @endforeach

                {{--<div class="col-sm-6 col-lg-4">
                    <div class="service-card">
                        <div class="service-card-image">
                            <img src=""
                                 alt="Corte + Barba">
                        </div>
                        <div class="service-card-body">
                            <h5 class="service-card-title">Corte + Barba</h5>
                            <div class="service-card-footer">
                                <div class="service-card-price">
                                    20.00€
                                </div>
                                <div class="service-card-duration">
                                    <i class="ri-time-line me-1"></i>
                                    45 min
                                </div>
                            </div>
                            <a href="/navalha-urbana/agendamento" class="btn btn-primary btn-lg px-5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" data-lucide="calendar-plus"
                                     class="lucide lucide-calendar-plus">
                                    <path d="M16 19h6"></path>
                                    <path d="M16 2v4"></path>
                                    <path d="M19 16v6"></path>
                                    <path d="M21 12.598V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h8.5"></path>
                                    <path d="M3 10h18"></path>
                                    <path d="M8 2v4"></path>
                                </svg>
                                Agendar
                            </a>
                        </div>

                    </div>
                </div>--}}
                {{--<div class="col-sm-6 col-lg-4">
                    <div class="service-card">
                        <div class="service-card-image">
                            <img src="https://app.meuhorario.ai//uploads/images/servico/2/1/servico_1769802490.png"
                                 alt="Corte de Cabelo">
                        </div>
                        <div class="service-card-body">
                            <h5 class="service-card-title">Corte de Cabelo</h5>
                            <div class="service-card-footer">
                                <div class="service-card-price">
                                    15.00€
                                </div>
                                <div class="service-card-duration">
                                    <i class="ri-time-line me-1"></i>
                                    30 min
                                </div>
                            </div>
                            <a href="/navalha-urbana/agendamento" class="btn btn-primary btn-lg px-5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" data-lucide="calendar-plus"
                                     class="lucide lucide-calendar-plus">
                                    <path d="M16 19h6"></path>
                                    <path d="M16 2v4"></path>
                                    <path d="M19 16v6"></path>
                                    <path d="M21 12.598V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h8.5"></path>
                                    <path d="M3 10h18"></path>
                                    <path d="M8 2v4"></path>
                                </svg>
                                Agendar
                            </a>
                        </div>

                    </div>
                </div>--}}
                {{-- <div class="col-sm-6 col-lg-4">
                     <div class="service-card">
                         <div class="service-card-image">
                             <img src="https://app.meuhorario.ai//uploads/images/servico/2/6/servico_1769802682.png"
                                  alt="Hidratação">
                         </div>
                         <div class="service-card-body">
                             <h5 class="service-card-title">Hidratação</h5>
                             <div class="service-card-footer">
                                 <div class="service-card-price">
                                     20.00€
                                 </div>
                                 <div class="service-card-duration">
                                     <i class="ri-time-line me-1"></i>
                                     30 min
                                 </div>
                             </div>
                             <a href="/navalha-urbana/agendamento" class="btn btn-primary btn-lg px-5">
                                 <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                      fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                      stroke-linejoin="round" data-lucide="calendar-plus"
                                      class="lucide lucide-calendar-plus">
                                     <path d="M16 19h6"></path>
                                     <path d="M16 2v4"></path>
                                     <path d="M19 16v6"></path>
                                     <path d="M21 12.598V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h8.5"></path>
                                     <path d="M3 10h18"></path>
                                     <path d="M8 2v4"></path>
                                 </svg>
                                 Agendar
                             </a>
                         </div>

                     </div>
                 </div>--}}
                {{--<div class="col-sm-6 col-lg-4">
                    <div class="service-card">
                        <div class="service-card-image">
                            <img src="https://app.meuhorario.ai//uploads/images/servico/2/5/servico_1769802742.png"
                                 alt="Pigmentação">
                        </div>
                        <div class="service-card-body">
                            <h5 class="service-card-title">Pigmentação</h5>
                            <div class="service-card-footer">
                                <div class="service-card-price">
                                    30.00€
                                </div>
                                <div class="service-card-duration">
                                    <i class="ri-time-line me-1"></i>
                                    60 min
                                </div>
                            </div>
                            <a href="/navalha-urbana/agendamento" class="btn btn-primary btn-lg px-5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" data-lucide="calendar-plus"
                                     class="lucide lucide-calendar-plus">
                                    <path d="M16 19h6"></path>
                                    <path d="M16 2v4"></path>
                                    <path d="M19 16v6"></path>
                                    <path d="M21 12.598V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h8.5"></path>
                                    <path d="M3 10h18"></path>
                                    <path d="M8 2v4"></path>
                                </svg>
                                Agendar
                            </a>
                        </div>

                    </div>
                </div>--}}
                {{--<div class="col-sm-6 col-lg-4">
                    <div class="service-card">
                        <div class="service-card-image">
                            <img src="https://app.meuhorario.ai//uploads/images/servico/2/4/servico_1769802810.png"
                                 alt="Sobrancelha">
                        </div>
                        <div class="service-card-body">
                            <h5 class="service-card-title">Sobrancelha</h5>
                            <div class="service-card-footer">
                                <div class="service-card-price">
                                    5.00€
                                </div>
                                <div class="service-card-duration">
                                    <i class="ri-time-line me-1"></i>
                                    10 min
                                </div>
                            </div>
                            <a href="/navalha-urbana/agendamento" class="btn btn-primary btn-lg px-5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" data-lucide="calendar-plus"
                                     class="lucide lucide-calendar-plus">
                                    <path d="M16 19h6"></path>
                                    <path d="M16 2v4"></path>
                                    <path d="M19 16v6"></path>
                                    <path d="M21 12.598V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h8.5"></path>
                                    <path d="M3 10h18"></path>
                                    <path d="M8 2v4"></path>
                                </svg>
                                Agendar
                            </a>
                        </div>

                    </div>
                </div>--}}
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="container">
            <div class="text-center">
                <h2>Vamos agendar?</h2>
                <p>Escolha o melhor horário para si</p>
                <a href="/booking" class="btn btn-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         data-lucide="calendar-plus" class="lucide lucide-calendar-plus">
                        <path d="M16 19h6"></path>
                        <path d="M16 2v4"></path>
                        <path d="M19 16v6"></path>
                        <path d="M21 12.598V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h8.5"></path>
                        <path d="M3 10h18"></path>
                        <path d="M8 2v4"></path>
                    </svg>
                    Agendar horário
                </a>
            </div>
        </div>
    </section>

    {{--<section class="py-5 bg-white">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold mb-2">Conheça a nossa equipa</h2>
                <p class="text-muted">Profissionais qualificados prontos para o atender</p>
            </div>
            <div class="row g-4 justify-content-center">
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 text-center p-4 card-profissional">
                        <div class="mb-3">
                            <img src="" alt="Bruno Inacio da Silva"
                                 class="rounded-circle border border-3"
                                 style="width: 110px; height: 110px; object-fit: cover;">
                        </div>
                        <h6 class="fw-bold mb-1">Bruno Inacio da Silva</h6>
                        <div class="mb-3"></div>
                        <a href="/profissional/26" class="btn btn-outline-dark btn-sm rounded-pill px-4">
                            Ver perfil
                        </a>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 text-center p-4 card-profissional">
                        <div class="mb-3">
                            <img src=""
                                 alt="Frederico Ricardo" class="rounded-circle border border-3"
                                 style="width: 110px; height: 110px; object-fit: cover;">
                        </div>
                        <h6 class="fw-bold mb-1">Frederico Ricardo</h6>
                        <div class="mb-3"></div>
                        <a href="profissional/3" class="btn btn-outline-dark btn-sm rounded-pill px-4">
                            Ver perfil
                        </a>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 text-center p-4 card-profissional">
                        <div class="mb-3">
                            <img src=""
                                 alt="Junior Maia" class="rounded-circle border border-3"
                                 style="width: 110px; height: 110px; object-fit: cover;">
                        </div>
                        <h6 class="fw-bold mb-1">Junior Maia</h6>
                        <div class="mb-3"></div>
                        <a href="/profissional/2" class="btn btn-outline-dark btn-sm rounded-pill px-4">
                            Ver perfil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>--}}

    <section class="py-5 bg-white">
        <div class="container">
            <h2 class="text-center fw-bold mb-4">Um pouco sobre nós</h2>
            <div class="row g-4 justify-content-center">
                {{$portal->about_us}}
            </div>
        </div>
    </section>

    <footer class="app-footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="{{ global_asset('storage/' . ($portal->logo ?? '')) }}" alt=""
                             class="footer-logo">
                        <span class="h5 mb-0">{{$portal->title}}</span>
                    </div>

                    <p class="text-white-50 small mb-2">
                        <i class="ri-map-pin-line me-2"></i>
                        {{$portal->address}} </p>

                    <p class="text-white-50 small mb-2">
                        <i class="ri-phone-line me-2"></i>
                        {{$portal->phone_1}} </p>
                </div>

                <div class="col-lg-4">
                    <h6 class="text-uppercase small mb-3">Horários</h6>
                    <ul class="list-unstyled small text-white-50">
                        <li class="d-flex justify-content-between py-1 border-bottom border-secondary">
                            <span>Dom</span>
                            <span>
                                                            <span class="text-danger">Fechado</span>
                                                    </span>
                        </li>
                        <li class="d-flex justify-content-between py-1 border-bottom border-secondary">
                            <span>Seg</span>
                            <span>
                                                            09:00-19:00                                                    </span>
                        </li>
                        <li class="d-flex justify-content-between py-1 border-bottom border-secondary">
                            <span>Ter</span>
                            <span>
                                                            08:00-19:00                                                    </span>
                        </li>
                        <li class="d-flex justify-content-between py-1 border-bottom border-secondary">
                            <span>Qua</span>
                            <span>
                                                            09:00-19:00                                                    </span>
                        </li>
                        <li class="d-flex justify-content-between py-1 border-bottom border-secondary">
                            <span>Qui</span>
                            <span>
                                                            09:00-19:00                                                    </span>
                        </li>
                        <li class="d-flex justify-content-between py-1 border-bottom border-secondary">
                            <span>Sex</span>
                            <span>
                                                            09:00-19:00                                                    </span>
                        </li>
                        <li class="d-flex justify-content-between py-1 border-bottom border-secondary">
                            <span>Sáb</span>
                            <span>
                                                            09:00-19:00                                                    </span>
                        </li>
                    </ul>
                </div>

                {{--<div class="col-lg-4">
                    <h6 class="text-uppercase small mb-3">Redes Sociais</h6>
                    <div class="d-flex gap-2 flex-wrap mb-4">
                        <a href="https://instagram.com/" target="_blank"
                           class="btn btn-outline-light btn-sm">
                            <i class="ri-instagram-line"></i>
                        </a>
                        <a href="" target="_blank" class="btn btn-outline-light btn-sm">
                            <i class="ri-facebook-line"></i>
                        </a>
                        <a href="https://wa.me/910000000" target="_blank" class="btn btn-outline-success btn-sm">
                            <i class="ri-whatsapp-line"></i>
                        </a>
                    </div>

                    <a href="/booking" class="btn btn-primary w-100">
                        <i class="ri-calendar-check-line me-2"></i>Agendar Agora
                    </a>
                </div>--}}
            </div>

            <hr class="my-4 border-secondary">

            <div
                class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 small text-white-50">
                <p class="mb-0">&copy; 2026 Agenda Online - Todos os direitos reservados.</p>
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


<script src="{{global_asset('assets/js/app.js')}}"></script>

</body>
</html>
