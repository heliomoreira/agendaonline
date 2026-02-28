<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marcação Confirmada | {{ $portal->name ?? 'Agenda Online' }}</title>

    <link rel="icon" type="image/png" href="{{ global_asset('storage/' . ($portal->logo ?? 'default-logo.png')) }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css" rel="stylesheet">

    <style>
        :root {
            --cor-primaria: {{ $portal->cor_primaria ?? '#2563eb' }};
            --cor-primaria-rgb: {{ $portal->cor_primaria_rgb ?? '37, 99, 235' }};
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            margin: 0;
        }

        .confirmation-card {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,.3);
            max-width: 580px;
            width: 100%;
            overflow: hidden;
            animation: slideUp .5s ease;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .confirmation-header {
            background: linear-gradient(135deg, var(--cor-primaria) 0%, #1d4ed8 100%);
            padding: 3rem 2rem 2.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .confirmation-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,.1) 0%, transparent 70%);
            animation: pulse 3s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: .8; }
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 8px 24px rgba(0,0,0,.15);
            position: relative;
            z-index: 1;
            animation: scaleIn .6s cubic-bezier(0.68, -0.55, 0.265, 1.55) .2s backwards;
        }

        @keyframes scaleIn {
            from { transform: scale(0) rotate(-180deg); }
            to { transform: scale(1) rotate(0); }
        }

        .success-icon i {
            font-size: 2.5rem;
            color: #10b981;
        }

        .confirmation-header h1 {
            color: #fff;
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0 0 .5rem;
            position: relative;
            z-index: 1;
        }

        .confirmation-header p {
            color: rgba(255,255,255,.9);
            font-size: .95rem;
            margin: 0;
            position: relative;
            z-index: 1;
        }

        .confirmation-body {
            padding: 2rem;
        }

        .booking-details {
            background: #f8fafc;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .booking-details h2 {
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #64748b;
            margin: 0 0 1.25rem;
            font-weight: 700;
        }

        .detail-row {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: .875rem 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .detail-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .detail-row:first-child {
            padding-top: 0;
        }

        .detail-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(var(--cor-primaria-rgb), .1);
            color: var(--cor-primaria);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: .95rem;
        }

        .detail-content {
            flex: 1;
        }

        .detail-label {
            font-size: .7rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: .2rem;
            font-weight: 600;
        }

        .detail-value {
            font-size: .95rem;
            color: #0f172a;
            font-weight: 600;
            line-height: 1.4;
        }

        .info-box {
            background: #fef3c7;
            border: 1px solid #fcd34d;
            border-radius: 12px;
            padding: 1rem 1.25rem;
            display: flex;
            gap: .875rem;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }

        .info-box i {
            color: #d97706;
            font-size: 1.25rem;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .info-box-content {
            flex: 1;
        }

        .info-box-title {
            font-size: .8rem;
            font-weight: 700;
            color: #92400e;
            margin-bottom: .25rem;
        }

        .info-box-text {
            font-size: .75rem;
            color: #78350f;
            line-height: 1.5;
            margin: 0;
        }

        .actions {
            display: grid;
            gap: .75rem;
        }

        .btn {
            padding: .875rem 1.5rem;
            border-radius: 12px;
            font-size: .875rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all .2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
        }

        .btn-primary {
            background: var(--cor-primaria);
            color: #fff;
        }

        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(var(--cor-primaria-rgb), .3);
        }

        .btn-outline {
            background: #fff;
            color: #475569;
            border: 1.5px solid #e2e8f0;
        }

        .btn-outline:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .btn i {
            font-size: 1rem;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 1.5rem 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }

        .divider span {
            font-size: .7rem;
            color: #94a3af;
            text-transform: uppercase;
            letter-spacing: .06em;
            font-weight: 600;
        }

        .footer-text {
            text-align: center;
            font-size: .75rem;
            color: #64748b;
            margin-top: 1.5rem;
        }

        .footer-text a {
            color: var(--cor-primaria);
            text-decoration: none;
            font-weight: 600;
        }

        .footer-text a:hover {
            text-decoration: underline;
        }

        @media (max-width: 576px) {
            body {
                padding: 1rem;
            }

            .confirmation-card {
                border-radius: 20px;
            }

            .confirmation-header {
                padding: 2.5rem 1.5rem 2rem;
            }

            .confirmation-header h1 {
                font-size: 1.5rem;
            }

            .confirmation-body {
                padding: 1.5rem;
            }

            .booking-details {
                padding: 1.25rem;
            }

            .detail-row {
                gap: .75rem;
            }

            .detail-icon {
                width: 32px;
                height: 32px;
                font-size: .875rem;
            }
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .confirmation-card {
                box-shadow: none;
                max-width: 100%;
            }

            .actions,
            .footer-text {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="confirmation-card">
    <div class="confirmation-header">
        <div class="success-icon">
            <i class="ri-check-line"></i>
        </div>
        <h1>Marcação Confirmada!</h1>
        <p>Recebemos a sua marcação com sucesso</p>
    </div>

    <div class="confirmation-body">
        <div class="booking-details">
            <h2><i class="ri-calendar-check-line me-1"></i> Detalhes da Marcação</h2>

            <div class="detail-row">
                <div class="detail-icon">
                    <i class="ri-scissors-line"></i>
                </div>
                <div class="detail-content">
                    <div class="detail-label">Serviço</div>
                    <div class="detail-value">{{ $booking->service->name ?? '—' }}</div>
                </div>
            </div>

            @if(isset($booking->professional))
                <div class="detail-row">
                    <div class="detail-icon">
                        <i class="ri-user-line"></i>
                    </div>
                    <div class="detail-content">
                        <div class="detail-label">Profissional</div>
                        <div class="detail-value">{{ $booking->professional->name ?? '—' }}</div>
                    </div>
                </div>
            @endif

            <div class="detail-row">
                <div class="detail-icon">
                    <i class="ri-calendar-line"></i>
                </div>
                <div class="detail-content">
                    <div class="detail-label">Data</div>
                    <div class="detail-value">
                        {{ \Carbon\Carbon::parse($booking->day)->locale('pt')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                    </div>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-icon">
                    <i class="ri-time-line"></i>
                </div>
                <div class="detail-content">
                    <div class="detail-label">Horário</div>
                    <div class="detail-value">{{ $booking->start_hour ?? '—' }}</div>
                </div>
            </div>

            @if(isset($booking->service->duration))
                <div class="detail-row">
                    <div class="detail-icon">
                        <i class="ri-timer-line"></i>
                    </div>
                    <div class="detail-content">
                        <div class="detail-label">Duração</div>
                        <div class="detail-value">
                            @php
                                $duration = $booking->service->duration;
                                $hours = floor($duration / 60);
                                $minutes = $duration % 60;
                                echo ($hours > 0 ? $hours . 'h' : '') . ($minutes > 0 ? $minutes . 'min' : '');
                            @endphp
                        </div>
                    </div>
                </div>
            @endif

            @if(isset($booking->service->price))
                <div class="detail-row">
                    <div class="detail-icon">
                        <i class="ri-price-tag-3-line"></i>
                    </div>
                    <div class="detail-content">
                        <div class="detail-label">Valor</div>
                        <div class="detail-value">{{ number_format($booking->service->price, 2, ',', '.') }}€</div>
                    </div>
                </div>
            @endif

            @if(isset($booking->client_name))
                <div class="detail-row">
                    <div class="detail-icon">
                        <i class="ri-user-3-line"></i>
                    </div>
                    <div class="detail-content">
                        <div class="detail-label">Nome</div>
                        <div class="detail-value">{{ $booking->client_name }}</div>
                    </div>
                </div>
            @endif

            @if(isset($portal->address))
                <div class="detail-row">
                    <div class="detail-icon">
                        <i class="ri-map-pin-line"></i>
                    </div>
                    <div class="detail-content">
                        <div class="detail-label">Morada</div>
                        <div class="detail-value">{{ $portal->address }}</div>
                    </div>
                </div>
            @endif
        </div>

        <div class="info-box">
            <i class="ri-information-line"></i>
            <div class="info-box-content">
                <div class="info-box-title">Informação Importante</div>
                <p class="info-box-text">
                    Enviámos um email de confirmação para <strong>{{ $booking->client_email ?? 'o seu email' }}</strong>.
                    Por favor, chegue 5 minutos antes da hora marcada.
                </p>
            </div>
        </div>

        <div class="actions">
            <a href="/{{ $portal->slug ?? '' }}/cliente/login" class="btn btn-primary">
                <i class="ri-calendar-line"></i>
                Ver Minhas Marcações
            </a>

            <a href="/{{ $portal->slug ?? '' }}/agendamento" class="btn btn-outline">
                <i class="ri-add-line"></i>
                Fazer Nova Marcação
            </a>

            <div class="divider">
                <span>ou</span>
            </div>

            <a href="/{{ $portal->slug ?? '' }}" class="btn btn-outline">
                <i class="ri-home-line"></i>
                Voltar ao Início
            </a>
        </div>

        <div class="footer-text">
            <p>
                Precisa de ajuda?
                @if(isset($portal->phone))
                    <a href="tel:{{ preg_replace('/\D/', '', $portal->phone) }}">{{ $portal->phone }}</a>
                @endif
                @if(isset($portal->phone) && isset($portal->whatsapp))
                    ou
                @endif
                @if(isset($portal->whatsapp))
                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $portal->whatsapp) }}" target="_blank">WhatsApp</a>
                @endif
            </p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
