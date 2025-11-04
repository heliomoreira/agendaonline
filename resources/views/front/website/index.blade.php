<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="stylesheet" href="{{global_asset('website/theme/assets/bootstrap/css/bootstrap.min.css')}}">
    <title>Agenda Online - Brevemente</title>
    <style>
        html,
        body {
            width: 100%;
            height: 100%;
        }

        html {
            display: table;
            font-family: Lato, "Segoe UI", Avenir, Ubuntu, Tahoma, Verdana, Helvetica, sans-serif;
        }

        body {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }

        h3 {
            font-size: 1.5em;
            font-family: Roboto, sans-serif;
            font-weight: normal;
        }
    </style>
</head>
<body>
<img src="{{global_asset('agenda_online.png')}}" alt="Agenda Online" title="Agenda Online" width="300" height="124">
<h3 class="mt-3">Brevemente</h3>
</body>
</html>
{{--
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description"
          content="O Assistgest é um software especializado na área de gestão de assistências técnicas. Com diversos módulos permite ter uma gestão simples mas organizada.">
    <meta name="keywords"
          content="software assistências, software de gestão de assistências técnicas, software gestão de reparações gratuito, software assistencia tecnica, software gestão assistencia tecnica">
    <meta name="author" content="">
    <title>Assistgest - Software de Gestão de Assistências Técnicas</title>
    <link rel="shortcut icon" type="image/x-icon"
          href="{{global_asset('website/media/img/favicon.png')}}">

    <link rel="stylesheet" href="{{global_asset('website/theme/assets/bootstrap/css/bootstrap.min.css')}}">
    <!-- Select2 -->
    <link href="{{global_asset('website/theme/assets/select2/css/select2.min.css')}}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{global_asset('website/theme/assets/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet">
    <!-- Iconmoon -->
    <link href="{{global_asset('website/theme/assets/iconmoon/css/iconmoon.css')}}" rel="stylesheet">
    <!-- Owl Carousel -->
    <link href="{{global_asset('website/theme/assets/owl-carousel/css/owl.carousel.min.css')}}" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="{{global_asset('website/theme/css/custom.css')}}" rel="stylesheet">
</head>
<body>
<!-- ==============================================
**Header**
=================================================== -->
<header class="opt5">
    <!-- Start Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container"><a class="navbar-brand" href="/"><img
                        src="{{global_asset('website/media/img/assistgest_black.png')}}" class="img-fluid logo1"
                        alt="Assistgest"><img src="{{global_asset('website/media/img/assistgest_light.png')}}"
                                              class="img-fluid logo2" alt="Assistgest"></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault"
                    aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation"><span
                        class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarsExampleDefault">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link" id="home" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="funcionalidades" href="/funcionalidades">Funcionalidades</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="precoseplanos" href="/planos-e-precos">Planos e Preços</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="suporte" href="https://suporte.assistgest.com"
                           target="_blank">Suporte</a>
                    </li>
                </ul>
                <ul class="navbar-right d-flex">
                    <li><a href="/signup" target="_blank">Registar</a></li>
                    <li><a href="#" target="_blank">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- End Navigation -->
</header>

<!-- ==============================================
**Banner opt5**
=================================================== -->
<div class="banner slide3 banner5">
    <div class="container">
        <div class="row cnt-block">
            <div class="col-md-12">
                <div class="left">
                    <h1>A sua agenda digital</h1>
                    <p>Organize marcações, reduza faltas e aumente a produtividade — tudo num só lugar.</p>
                    <a href="/signup" class="get-started" style="max-width:275px !important" target="_blank">Crie a sua
                        conta grátis</a></div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <figure><img src="{{global_asset('website/media/img/assistgest_multidevice.png')}}" class="img-fluid"
                             alt="Assistgest" title="Assistgest"></figure>
            </div>
        </div>
    </div>
</div>

<!-- ==============================================
**How it Works**
=================================================== -->
<section class="how-it-works-sec">
    <div class="sided-item-wrapper padding-lg">
        <div class="container">
            <h2>Quais são as vantagens ?</h2>
            <div class="row">
                <div class="col-md-9 content-area">
                    <ul class="row marketing-list3">
                        <li class="col-md-6 equal-hight">
                            <div class="ico-block"><img src="{{global_asset('website/media/img/multidevice.png')}}"
                                                        class="rounded-circle img-fluid" alt=""></div>
                            <div class="cnt-block">
                                <h3>Multi-dispositivo</h3>
                                <p>No computador, portátil, tablet ou smartphone, aceda ao software com facilidade onde
                                    quer que esteja.</p>
                            </div>
                        </li>
                        <li class="col-md-6 equal-hight">
                            <div class="ico-block"><img src="{{global_asset('website/media/img/simples.png')}}"
                                                        class="rounded-circle img-fluid" alt=""></div>
                            <div class="cnt-block">
                                <h3>Agenda inteligente</h3>
                                <p>Visualização diária/semanal/mensal com gestão de sobreposições</p>
                            </div>
                        </li>
                        <li class="col-md-6 equal-hight">
                            <div class="ico-block"><img src="{{global_asset('website/media/img/multiuser.png')}}"
                                                        class="rounded-circle img-fluid" alt=""></div>
                            <div class="cnt-block">
                                <h3>Gestão de funcionários</h3>
                                <p>Atribua horários, turnos e serviços por colaborador.</p>
                            </div>
                        </li>
                        <li class="col-md-6 equal-hight">
                            <div class="ico-block"><img src="{{global_asset('website/media/img/improve.png')}}"
                                                        class="rounded-circle img-fluid" alt=""></div>
                            <div class="cnt-block">
                                <h3>Marcações online</h3>
                                <p>Os seus clientes podem marcar diretamente através do telemóvel</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="right-sided-full-image">
            <figure><img src="{{global_asset('website/media/img/vantagens_assistgest.png')}}"
                         alt="Assistgest - Vantagens"
                         title="Assistgest - Vantagens"></figure>
        </div>
    </div>
</section>

<!-- ==============================================
**Create SEO Reports**
=================================================== -->
<section class="seo-reports">
    <div class="sided-item-wrapper">
        <div class="container">
            <div class="row justify-content-end">
                <div class="col-md-7">
                    <div class="content-area padding-lg">
                        <h2>Com as funcionalidades que realmente precisa!</h2>
                        <p>Sabia que num software cerca de 65% das funcionalidades <u>nunca</u> são utilizadas? Por esse
                            motivo, consideramos ter as <i>funcionalidades que realmente precisa!</i> Sem inúmeras
                            opções e configurações que não vai precisar. No entanto, ouvimos sempre os nossos clientes e
                            todas as funcionalidades que possam fazer sentido, nós implementámos.</p>
                        <ul class="icon-tik-list row">
                            <li class="col-md-6">
                                <p>Gestão de Clientes</p>
                            </li>
                            <li class="col-md-6">
                                <p>Gestão de Serviços</p>
                            </li>
                            <li class="col-md-6">
                                <p>Gestão de Orçamentos</p>
                            </li>
                            <li class="col-md-6">
                                <p>Módulo de Relatórios.</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="left-sided-full-image">
            <figure><img src="{{global_asset('website/media/img/funcionalidades.png')}}" alt=""></figure>
        </div>
    </div>
</section>

<!-- ==============================================
**Generate Forms**
=================================================== -->
<section class="generate-forms padding-lg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <h2>Múltiplos módulos</h2>
                <p>Com os módulos para gestão de clientes, assistências, orçamentos, fornecedores externos entre outros,
                    o Assistgest permite-lhe ter uma gestão mais completa e organizada do seu negócio.</p>
            </div>
        </div>
    </div>
    <div class="custom-opacity">
        <div id="owl-career" class="owl-carousel">
            <div class="item">
                <figure><img src="{{global_asset('website/media/img/modulo_assistencias.jpg')}}" alt=""></figure>
            </div>
            <div class="item">
                <figure><img src="{{global_asset('website/media/img/modulo_estatisticas.jpg')}}" alt=""></figure>
            </div>
            <div class="item">
                <figure><img src="{{global_asset('website/media/img/modulo_calendario.jpg')}}" alt=""></figure>
            </div>
        </div>
        <div class="keyboard">
            <figure><img src="{{global_asset('website/media/img/key-board-mouse.png')}}" class="img-fluid"
                         alt="Assistgest"
                         title="Assistgest"></figure>
        </div>
        <div class="frame">
            <figure><img src="{{global_asset('website/theme/images/slider-frame.png')}}" class="img-fluid" alt="">
            </figure>
        </div>
    </div>
</section>

<!-- ==============================================
**Provide Features**
=================================================== -->
<section class="provide-features">
    <div class="sided-item-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="content-area padding-lg">
                        <h2>Algumas das nossas funcionalidades!</h2>
                        <ul class="row features-listing">
                            <li class="col-md-6"><span class="icon"><img
                                            src="{{global_asset('website/media/img/assistgest-historico.png')}}"
                                            alt="Assistgest Histórico" title="Assistgest Histórico"></span>
                                <h3>Histórico</h3>
                                <p>Conseguirá rapidamente ter acesso ao histórico do cliente e do equipamento e, dessa
                                    forma, prestar um atendimento ao cliente de excelência!</p>
                            </li>
                            <li class="col-md-6"><span class="icon"><img
                                            src="{{global_asset('website/media/img/assistgest-clientes.png')}}"
                                            alt="Assistgest Clientes" title="Assistgest Clientes"></span>
                                <h3>Gestão de Clientes</h3>
                                <p>Efectue a gestão dos seus clientes de forma informada. Obtenha de forma rápida e
                                    simples todas as assistências que determinado cliente teve como também as
                                    informações relevantes sobre o mesmo.</p>
                            </li>
                            <li class="col-md-6"><span class="icon"><img
                                            src="{{global_asset('website/media/img/assistgest-estatisticas.png')}}"
                                            alt="Assistgest Estatísticas" title="Assistgest Estatísticas"></span>
                                <h3>Estatísticas</h3>
                                <p>Verifique em tempo real a progressão que tem nas assistências, melhores clientes e
                                    produtos / serviços mais vendidos. Tome decisões baseadas em números e não em
                                    percepções.</p>
                            </li>
                            <li class="col-md-6"><span class="icon"><img
                                            src="{{global_asset('website/media/img/assistgest-calendario.png')}}"
                                            alt="Assisgest Calendário" title="Assisgest Calendário"></span>
                                <h3>Calendário</h3>
                                <p>Com o módulo calendário será possível agendar eventos ou assistências que podem (ou
                                    não) estar associadas a um técnico.</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="right-sided-full-image">
            <figure><img src="{{global_asset('website/media/img/calendario_assistgest.png')}}"
                         alt="Assistgest - Calendário"
                         title="Assistgest - Calendário"></figure>
        </div>
    </div>
</section>

<!-- ==============================================
**Latest Stories**
=================================================== -->
<section class="latest-stories faq-sec padding-lg white-bg">
    <div class="container">
        <div class="row justify-content-center head-block">
            <div class="col-md-10"><span>FAQ</span>
                <h2>Perguntas frequentes</h2>
            </div>
        </div>
        <ul class="row">
            <li class="col-md-6">
                <h3>Têm algum plano gratuito?</h3>
                <p>Sim. Temos um plano gratuito que permite a utilização do software na sua plenitude, no entanto este
                    plano apenas permite criar 10 assistências por mês.</p>
            </li>
            <li class="col-md-6">
                <h3>Qual é a fidelização obrigatória?</h3>
                <p>Não temos fidelização obrigatória, pode alterar ou cancelar a sua subscrição a qualquer momento.</p>
            </li>
            <li class="col-md-6">
                <h3>Tenho que instalar no meu computador?</h3>
                <p>O Assistgest é um software SaaS, isto é, é um software que está alojado em servidores web e que não
                    necessita de instalação num computador. </p>
            </li>
            <li class="col-md-6">
                <h3>Posso utilizar no meu telemóvel?</h3>
                <p>Sim, como o software é executado num servidor web, desde que tenha ligação à Internet conseguirá
                    aceder ao software e efectuar a gestão de todos os módulos.</p>
            </li>
        </ul>
    </div>
</section>
--}}
{{--
<!-- ==============================================
**Customers reviews**
=================================================== -->
<section class="client-speak carousel3 padding-lg">
    <div class="container">
        <div class="row justify-content-center head-block">
            <div class="col-md-10"><span>Testemunhos</span>
                <h2>Melhor do que as nossas palavras,<br>
                    mostramos as opiniões dos nossos clientes</h2>
                <p class="hidden-xs">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem
                    Ipsum has been the industry's standard dummy text ever since beenLorem Ipsum is simply dummy</p>
            </div>
        </div>
        <ul class="speak-listing opt3 owl-carousel">
            <li>
                <div class="inner">
                    <figure><img src="{{global_asset('website/theme/images/testimonial-thumb1.jpg')}}"
                                 class="img-fluid rounded-circle" alt=""> <span
                            class="icon-quote"></span></figure>
                    <div class="cnt-right">
                        <div class="client-detail">
                            <h4>Marco</h4>
                            <span class="designation">iMuda</span></div>
                        <p>Lorem Ipsum is simply dummy text of the printing and simply dummy text of the typesetting
                            industry. simply dummy text of theLorem Ipsum hasdummy text of the typesetting.</p>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </li>
            <li>
                <div class="inner">
                    <figure><img src="{{global_asset('website/theme/images/testimonial-thumb2.jpg')}}"
                                 class="img-fluid rounded-circle" alt=""> <span
                            class="icon-quote"></span></figure>
                    <div class="cnt-right">
                        <div class="client-detail">
                            <h4>Aleksandar</h4>
                            <span class="designation">Art Director</span></div>
                        <p>Lorem Ipsum is simply dummy text of the printing and simply dummy text of the typesetting
                            industry. simply dummy text of theLorem Ipsum hasdummy text of the typesetting.</p>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </li>
            <li>
                <div class="inner">
                    <figure><img src="{{global_asset('website/theme/images/testimonial-thumb3.jpg')}}"
                                 class="img-fluid rounded-circle" alt=""> <span
                            class="icon-quote"></span></figure>
                    <div class="cnt-right">
                        <div class="client-detail">
                            <h4>John Nikcevic</h4>
                            <span class="designation">Creative Director</span></div>
                        <p>Lorem Ipsum is simply dummy text of the printing and simply dummy text of the typesetting
                            industry. simply dummy text of theLorem Ipsum hasdummy text of the typesetting.</p>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </li>
            <li>
                <div class="inner">
                    <figure><img src="{{global_asset('website/theme/images/testimonial-thumb1.jpg')}}"
                                 class="img-fluid rounded-circle" alt=""> <span
                            class="icon-quote"></span></figure>
                    <div class="cnt-right">
                        <div class="client-detail">
                            <h4>Julia Renvoye</h4>
                            <span class="designation">Softwear Engineer</span></div>
                        <p>Lorem Ipsum is simply dummy text of the printing and simply dummy text of the typesetting
                            industry. simply dummy text of theLorem Ipsum hasdummy text of the typesetting.</p>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </li>
            <li>
                <div class="inner">
                    <figure><img src="{{global_asset('website/theme/images/testimonial-thumb2.jpg')}}"
                                 class="img-fluid rounded-circle" alt=""> <span
                            class="icon-quote"></span></figure>
                    <div class="cnt-right">
                        <div class="client-detail">
                            <h4>Aleksandar</h4>
                            <span class="designation">Art Director</span></div>
                        <p>Lorem Ipsum is simply dummy text of the printing and simply dummy text of the typesetting
                            industry. simply dummy text of theLorem Ipsum hasdummy text of the typesetting.</p>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </li>
            <li>
                <div class="inner">
                    <figure><img src="{{global_asset('website/theme/images/testimonial-thumb3.jpg')}}"
                                 class="img-fluid rounded-circle" alt=""> <span
                            class="icon-quote"></span></figure>
                    <div class="cnt-right">
                        <div class="client-detail">
                            <h4>John Nikcevic</h4>
                            <span class="designation">Creative Director</span></div>
                        <p>Lorem Ipsum is simply dummy text of the printing and simply dummy text of the typesetting
                            industry. simply dummy text of theLorem Ipsum hasdummy text of the typesetting.</p>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </li>
        </ul>
    </div>
</section>
--}}{{--


@include('front.website.includes.footer')
</body>
</html>
--}}
