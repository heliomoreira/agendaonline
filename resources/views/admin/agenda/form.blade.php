@extends('layouts.app')

@php
    /*
     * Formulário de marcação (criar / editar) — admin do tenant.
     * Ajuste os nomes marcados com "ajuste" aos nomes reais do seu model.
     */
    $isEdit = (bool) $agenda->id;
    $client = $agenda->client ?? null;                    // ajuste: relação Agenda -> Client
    $reminder = $agenda->reminderNotification ?? null;    // ajuste: relação Agenda -> lembrete SMS
    $fmt = fn ($value, string $format) => $value ? \Carbon\Carbon::parse($value)->format($format) : null;

    $initial = [
        'is_edit' => $isEdit,
        'service_id' => old('service_id', $agenda->service_id),
        'professional_id' => old('professional_id', $agenda->professional_id),
        'day' => old('day', $fmt($agenda->day, 'Y-m-d')),                 // ajuste: coluna do dia
        'start_hour' => old('start_hour', $fmt($agenda->start_hour, 'H:i')), // ajuste: coluna da hora
        'client_id' => old('client_id', $client?->id),
        'client_name' => old('client_name', $client?->name),
        'client_email' => old('client_email', $client?->email),
        'client_phone_1' => old('client_phone_1', $client?->phone_1),
        'client_phone_cc' => $client?->phone_1_country_code,
        'client_is_minor' => (bool) ($client?->is_minor ?? false),
    ];

    // Lembrete ligado por defeito numa marcação nova; na edição, só se já existir um.
    $smsEnabled = session()->hasOldInput()
        ? (bool) old('sms_enabled')
        : ($isEdit ? (bool) $reminder : true);
@endphp

@section('content')
    <style>
        .step-badge {
            width: 28px; height: 28px; min-width: 28px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 50%; font-weight: 600; font-size: .85rem;
        }

        /* Horários */
        .slot-group + .slot-group { margin-top: 1.25rem; }
        .slot-group-title { font-size: .8125rem; font-weight: 600; color: var(--bs-secondary-color); margin-bottom: .5rem; }
        .slot-grid { display: flex; flex-wrap: wrap; gap: .5rem; }
        .slot-chip {
            min-width: 4.75rem; padding: .5rem .75rem;
            border: 1px solid var(--bs-border-color); border-radius: .375rem;
            background: var(--bs-paper-bg, var(--bs-body-bg)); color: var(--bs-body-color);
            font-weight: 500; font-variant-numeric: tabular-nums; line-height: 1.2;
            transition: border-color .15s, background-color .15s, color .15s;
        }
        .slot-chip:hover { border-color: var(--bs-primary); color: var(--bs-primary); }
        .slot-chip:focus-visible { outline: 2px solid var(--bs-primary); outline-offset: 2px; }
        .slot-chip[aria-pressed="true"] { background: var(--bs-primary); border-color: var(--bs-primary); color: #fff; }
        .slot-chip small { display: block; font-weight: 400; font-size: .7rem; opacity: .8; }
        .slots-placeholder {
            padding: 1.25rem; border: 1px dashed var(--bs-border-color); border-radius: .375rem;
            color: var(--bs-secondary-color); text-align: center;
        }
        .is-invalid-box { border: 1px solid var(--bs-danger); border-radius: .375rem; padding: .75rem; }

        /* Cliente */
        #client_results {
            z-index: 1050; display: none; max-height: 300px; overflow-y: auto;
            background-color: var(--bs-paper-bg, var(--bs-body-bg, #fff));
            border: 1px solid var(--bs-border-color, #d9dee3); border-radius: .375rem;
        }
        #client_results .list-group-item-action:hover,
        #client_results .list-group-item-action.is-active { background-color: var(--bs-primary-bg-subtle, #e7e7ff); }
        .client-picked { border-left: 3px solid var(--bs-success, #71dd37); }

        /* SMS */
        .sms-preview {
            background: var(--bs-tertiary-bg, #f5f5f9); border-radius: .5rem;
            padding: .75rem 1rem; white-space: pre-wrap; font-size: .9rem;
        }

        /* Resumo */
        .booking-summary { position: sticky; top: 6.5rem; } /* ajuste à altura da navbar do layout */
        .summary-list { margin: 0; }
        .summary-list > div {
            display: flex; justify-content: space-between; gap: 1rem;
            padding: .55rem 0; border-bottom: 1px dashed var(--bs-border-color);
        }
        .summary-list > div:last-child { border-bottom: 0; }
        .summary-list dt { font-weight: 400; color: var(--bs-secondary-color); white-space: nowrap; }
        .summary-list dd { margin: 0; text-align: right; font-weight: 500; }
        .summary-list dd.is-empty { color: var(--bs-secondary-color); font-weight: 400; }

        @media (max-width: 991.98px) {
            .booking-summary { position: static; }
        }
    </style>

    @if (session('success'))
        <div class="alert alert-solid-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <span class="alert-icon rounded me-2"><i class="icon-base ti tabler-check icon-md"></i></span>
            <div class="flex-grow-1">{{ session('success') }}</div>
            <button type="button" class="btn-close ms-2" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <i class="icon-base ti tabler-alert-circle me-2"></i>
            <div>Não foi possível gravar. Corrija os campos assinalados abaixo.</div>
        </div>
    @endif

    <div id="session_alert" class="alert alert-danger d-none align-items-center" role="alert">
        <i class="icon-base ti tabler-lock me-2"></i>
        <div class="flex-grow-1">A sua sessão expirou. Inicie sessão novamente para continuar.</div>
        <button type="button" class="btn btn-sm btn-danger ms-2" onclick="window.location.reload()">Iniciar sessão</button>
    </div>

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
        <h5 class="d-flex align-items-center gap-2 m-0">
            <i class="icon-base ti tabler-calendar"></i>
            {{ $isEdit ? 'Editar marcação' : 'Nova marcação' }}
        </h5>
        <a href="{{ route('agenda.index') }}" class="btn btn-sm btn-label-secondary">
            <i class="icon-base ti tabler-arrow-left me-1"></i> Voltar à agenda
        </a>
    </div>

    <form id="booking_form" method="POST"
          action="{{ $isEdit ? route('agenda.update', $agenda->id) : route('book.slot', ['admin' => true]) }}">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <input type="hidden" id="start_hour" name="start_hour" value="{{ $initial['start_hour'] }}">
        <input type="hidden" id="professional_id" name="professional_id" value="{{ $initial['professional_id'] }}">
        <input type="hidden" id="client_id" name="client_id" value="{{ $initial['client_id'] }}">

        <div class="row g-6">
            {{-- ===================== Coluna principal ===================== --}}
            <div class="col-lg-8">

                {{-- ===== Passo 1 — Serviço e horário ===== --}}
                <div class="card mb-6">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-4">
                            <span class="step-badge bg-primary text-white">1</span>
                            <h6 class="mb-0 fw-semibold">Serviço e horário</h6>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label" for="service_id">Serviço</label>
                                <select id="service_id" name="service_id" class="form-select @error('service_id') is-invalid @enderror" required>
                                    <option value="" @selected(! $initial['service_id'])>Selecionar serviço…</option>
                                    @foreach ($services as $id => $name)
                                        <option value="{{ $id }}" @selected((string) $initial['service_id'] === (string) $id)>{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('service_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="professional_filter">Profissional</label>
                                <select id="professional_filter" class="form-select @error('professional_id') is-invalid @enderror" disabled>
                                    <option value="">Qualquer profissional</option>
                                    @foreach ($professionals as $pro)
                                        <option value="{{ $pro->id }}" @selected((string) $initial['professional_id'] === (string) $pro->id)>{{ $pro->name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted step-hint" data-hint="professional">Escolha primeiro o serviço.</small>
                                @error('professional_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="day" class="form-label">Dia</label>
                                <div class="input-group">
                                    <button type="button" class="btn btn-outline-secondary" id="day_prev" aria-label="Dia anterior" disabled>
                                        <i class="icon-base ti tabler-chevron-left"></i>
                                    </button>
                                    <input type="date" id="day" name="day" class="form-control @error('day') is-invalid @enderror"
                                           value="{{ $initial['day'] }}"
                                           @unless ($isEdit) min="{{ now()->format('Y-m-d') }}" @endunless
                                           disabled required>
                                    <button type="button" class="btn btn-outline-secondary" id="day_next" aria-label="Dia seguinte" disabled>
                                        <i class="icon-base ti tabler-chevron-right"></i>
                                    </button>
                                </div>
                                @error('day') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 d-flex align-items-end gap-2">
                                <button type="button" class="btn btn-label-primary" data-day-offset="0" disabled>Hoje</button>
                                <button type="button" class="btn btn-label-primary" data-day-offset="1" disabled>Amanhã</button>
                                <small class="text-muted ms-1 step-hint" data-hint="day"></small>
                            </div>

                            <div class="col-12">
                                <div class="d-flex align-items-baseline justify-content-between mb-2">
                                    <span class="form-label mb-0">Horários disponíveis</span>
                                    <small class="text-muted" id="slots_status" aria-live="polite"></small>
                                </div>
                                <div id="slots_box" class="@error('start_hour') is-invalid-box @enderror">
                                    <div class="slots-placeholder">Escolha o serviço e o dia para ver os horários livres.</div>
                                </div>
                                @error('start_hour') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== Passo 2 — Cliente ===== --}}
                <div class="card mb-6">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-4">
                            <span class="step-badge bg-primary text-white">2</span>
                            <h6 class="mb-0 fw-semibold">Cliente</h6>
                        </div>

                        {{-- Pesquisa --}}
                        <div id="client_search_wrap">
                            <label for="client_search" class="form-label">Procurar cliente</label>
                            <div class="position-relative">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="icon-base ti tabler-search"></i></span>
                                    <input type="text" id="client_search" class="form-control"
                                           placeholder="Nome, telemóvel ou email" autocomplete="off"
                                           role="combobox" aria-expanded="false" aria-controls="client_results" aria-autocomplete="list">
                                    <span class="input-group-text d-none" id="client_spinner">
                                        <span class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">A procurar…</span></span>
                                    </span>
                                </div>
                                <div id="client_results" class="list-group position-absolute w-100 shadow mt-1" role="listbox"></div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-2">
                                <small class="text-muted">Use as setas e Enter para escolher.</small>
                                <button type="button" class="btn btn-sm btn-link px-0" id="client_new">
                                    <i class="icon-base ti tabler-user-plus me-1"></i> Novo cliente
                                </button>
                            </div>
                            @error('client_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        {{-- Cliente selecionado --}}
                        <div id="selected_client_card" class="card client-picked bg-light-subtle d-none">
                            <div class="card-body py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="badge bg-label-success rounded-pill p-2">
                                        <i class="icon-base ti tabler-user-check"></i>
                                    </span>
                                    <div>
                                        <div class="fw-semibold">
                                            <span id="sc_name"></span>
                                            <span class="badge bg-warning text-dark ms-1 d-none" id="sc_minor">Menor de idade</span>
                                        </div>
                                        <small class="text-muted d-block" id="sc_meta"></small>
                                        <small class="text-warning d-none" id="sc_minor_note">Se a opção estiver ativa, o lembrete também é enviado ao encarregado de educação.</small>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="client_edit">
                                        <i class="icon-base ti tabler-pencil me-1"></i> Editar dados
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="client_clear">
                                        <i class="icon-base ti tabler-switch-horizontal me-1"></i> Trocar
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Dados do cliente (novo ou em edição) --}}
                        <div id="client_fields" class="mt-4 d-none">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="fw-semibold" id="client_fields_title">Novo cliente</span>
                                <button type="button" class="btn btn-sm btn-link px-0" id="client_cancel">Procurar cliente existente</button>
                            </div>
                            <div class="alert alert-info py-2 small d-none" id="client_fields_note"></div>
                            <div class="row g-4">
                                <div class="col-md-5">
                                    <label for="client_name" class="form-label">Nome</label>
                                    <input type="text" id="client_name" name="client_name" autocomplete="off"
                                           class="form-control @error('client_name') is-invalid @enderror"
                                           value="{{ $initial['client_name'] }}">
                                    @error('client_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="client_email" class="form-label">Email <span class="text-muted fw-normal">(opcional)</span></label>
                                    <input type="email" id="client_email" name="client_email" autocomplete="off"
                                           class="form-control @error('client_email') is-invalid @enderror"
                                           value="{{ $initial['client_email'] }}">
                                    @error('client_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-3">
                                    <label for="client_phone_1" class="form-label">Telemóvel</label>
                                    <input type="tel" inputmode="tel" id="client_phone_1" name="client_phone_1" autocomplete="off"
                                           class="form-control @error('client_phone_1') is-invalid @enderror"
                                           value="{{ $initial['client_phone_1'] }}">
                                    @error('client_phone_1') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== Passo 3 — Observações ===== --}}
                <div class="card mb-6">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-4">
                            <span class="step-badge bg-primary text-white">3</span>
                            <h6 class="mb-0 fw-semibold">Observações <span class="text-muted fw-normal">(opcional)</span></h6>
                        </div>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3"
                                  placeholder="Notas internas, só visíveis para a equipa">{{ old('notes', $agenda->notes) }}</textarea>
                        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- ===== Passo 4 — Lembrete SMS ===== --}}
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                            <div class="d-flex align-items-center gap-2">
                                <span class="step-badge bg-primary text-white">4</span>
                                <h6 class="mb-0 fw-semibold">Lembrete por SMS</h6>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" role="switch" id="sms_enabled" name="sms_enabled" value="1" @checked($smsEnabled)>
                                <label class="form-check-label" for="sms_enabled">Enviar lembrete</label>
                            </div>
                        </div>

                        <div id="sms_fields">
                            <div class="row g-4">
                                <div class="col-md-8">
                                    <label for="sms_text" class="form-label">Mensagem</label>
                                    <textarea class="form-control @error('sms_text') is-invalid @enderror" id="sms_text" name="sms_text" rows="4"
                                              placeholder="Texto do lembrete">{{ old('sms_text', $reminder?->text) }}</textarea>
                                    @error('sms_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted d-block mt-1">
                                        <code>[NOME_CLIENTE]</code>, <code>[SERVICO]</code>, <code>[DATA]</code> e <code>[HORA]</code> são substituídos ao enviar.
                                    </small>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-4">
                                        <label for="sms_send_date" class="form-label">Data de envio</label>
                                        <input type="date" id="sms_send_date" name="sms_send_date"
                                               class="form-control @error('sms_send_date') is-invalid @enderror"
                                               value="{{ old('sms_send_date', $fmt($reminder?->send_day, 'Y-m-d')) }}">
                                        @error('sms_send_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div>
                                        <label for="sms_send_hour" class="form-label">Hora de envio</label>
                                        <input type="time" id="sms_send_hour" name="sms_send_hour"
                                               class="form-control @error('sms_send_hour') is-invalid @enderror"
                                               value="{{ old('sms_send_hour', $fmt($reminder?->send_hour, 'H:i')) }}">
                                        @error('sms_send_hour') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="d-flex align-items-baseline justify-content-between mb-1">
                                        <span class="small fw-semibold">Como o cliente vai receber</span>
                                        <small class="text-muted"><span id="sms_chars">0</span> caracteres, <span id="sms_segs">0</span> SMS</small>
                                    </div>
                                    <div class="sms-preview" id="sms_preview"></div>
                                    <small class="text-muted d-block mt-1" id="sms_encoding"></small>
                                    <div class="alert alert-warning py-2 small mt-3 mb-0 d-none" id="sms_warn" role="status"></div>
                                </div>
                            </div>
                        </div>
                        <p class="text-muted small mb-0 d-none" id="sms_off_note">O cliente não vai receber lembrete desta marcação.</p>
                    </div>
                </div>
            </div>

            {{-- ===================== Resumo ===================== --}}
            <div class="col-lg-4">
                <div class="card booking-summary">
                    <div class="card-header">
                        <h5 class="mb-0">Resumo</h5>
                    </div>
                    <div class="card-body">
                        <dl class="summary-list">
                            <div><dt>Serviço</dt><dd id="sum_service"></dd></div>
                            <div><dt>Profissional</dt><dd id="sum_pro"></dd></div>
                            <div><dt>Dia</dt><dd id="sum_day"></dd></div>
                            <div><dt>Hora</dt><dd id="sum_time"></dd></div>
                            <div><dt>Cliente</dt><dd id="sum_client"></dd></div>
                            <div><dt>Lembrete</dt><dd id="sum_sms"></dd></div>
                        </dl>
                    </div>
                    <div class="card-footer border-top pt-4">
                        <p class="small mb-3" id="missing_hint" aria-live="polite"></p>
                        <button type="submit" class="btn btn-primary w-100 waves-effect waves-light" id="submitBtn" disabled>
                            <i class="icon-base ti tabler-device-floppy me-1"></i> {{ $isEdit ? 'Gravar alterações' : 'Gravar marcação' }}
                        </button>
                        <a href="{{ route('agenda.index') }}" class="btn btn-label-secondary w-100 mt-2">Cancelar</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    @php $settings = \App\Models\Setting::current(); @endphp
    <script>
        window.SMS_CFG = {
            advanceDays: {{ (int) ($settings->sms_advance_days ?? 1) }},
            sendHour: @json($settings->sms_send_hour ? \Carbon\Carbon::parse($settings->sms_send_hour)->format('H:i') : '09:00'),
            defaultText: "Olá [NOME_CLIENTE], lembramos a sua marcação de [SERVICO] em [DATA] às [HORA].",
        };
        // Mapa service_id -> texto do template, vindo do controller
        window.SMS_TEMPLATES = @json($smsTemplates ?? []);
        window.BOOKING_INIT = @json($initial);
    </script>
    @verbatim
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const INIT = window.BOOKING_INIT || {};
                const CFG = window.SMS_CFG;
                const TEMPLATES = window.SMS_TEMPLATES || {};
                const $ = (id) => document.getElementById(id);

                // ---------- Elementos ----------
                const form = $('booking_form');
                const serviceSelect = $('service_id');
                const proFilter = $('professional_filter');
                const proInput = $('professional_id');
                const dayInput = $('day');
                const dayPrev = $('day_prev');
                const dayNext = $('day_next');
                const dayQuick = document.querySelectorAll('[data-day-offset]');
                const timeInput = $('start_hour');
                const slotsBox = $('slots_box');
                const slotsStatus = $('slots_status');
                const submitBtn = $('submitBtn');
                const missingHint = $('missing_hint');
                const sessionAlert = $('session_alert');

                const clientSearchWrap = $('client_search_wrap');
                const clientSearch = $('client_search');
                const clientResults = $('client_results');
                const clientSpinner = $('client_spinner');
                const clientNewBtn = $('client_new');
                const selectedCard = $('selected_client_card');
                const scName = $('sc_name');
                const scMeta = $('sc_meta');
                const scMinor = $('sc_minor');
                const scMinorNote = $('sc_minor_note');
                const clientEditBtn = $('client_edit');
                const clientClearBtn = $('client_clear');
                const clientFields = $('client_fields');
                const clientFieldsTitle = $('client_fields_title');
                const clientFieldsNote = $('client_fields_note');
                const clientCancel = $('client_cancel');
                const clientIdInput = $('client_id');
                const clientName = $('client_name');
                const clientEmail = $('client_email');
                const clientPhone = $('client_phone_1');

                const smsToggle = $('sms_enabled');
                const smsFields = $('sms_fields');
                const smsOffNote = $('sms_off_note');
                const smsText = $('sms_text');
                const smsDate = $('sms_send_date');
                const smsHour = $('sms_send_hour');
                const smsChars = $('sms_chars');
                const smsSegs = $('sms_segs');
                const smsEncoding = $('sms_encoding');
                const smsPreview = $('sms_preview');
                const smsWarn = $('sms_warn');

                // ---------- Estado ----------
                let selected = INIT.start_hour
                    ? { time: INIT.start_hour, proId: INIT.professional_id ? String(INIT.professional_id) : '', proName: '' }
                    : null;
                const keepInitialSlot = !!(INIT.is_edit && INIT.start_hour);
                let slotsRequest = 0;
                let clientState = 'search';
                let currentClient = null;
                let smsManualText = smsText.value.trim() !== '';
                let smsManualDate = smsDate.value !== '';
                let dirty = false;
                let submitting = false;

                // ---------- Utilitários ----------
                const pad = (n) => String(n).padStart(2, '0');
                const toISO = (d) => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
                const parseISO = (s) => { const [y, m, d] = s.split('-').map(Number); return new Date(y, m - 1, d); };
                const addDays = (s, n) => { const d = parseISO(s); d.setDate(d.getDate() + n); return toISO(d); };
                const todayISO = toISO(new Date());
                const fmtDayLong = (s) => {
                    const t = parseISO(s).toLocaleDateString('pt-PT', { weekday: 'long', day: 'numeric', month: 'long' });
                    return t.charAt(0).toUpperCase() + t.slice(1);
                };
                const fmtDayShort = (s) => { const [y, m, d] = s.split('-'); return `${d}/${m}/${y}`; };
                const listPT = (arr) => arr.length < 2 ? (arr[0] || '') : arr.slice(0, -1).join(', ') + ' e ' + arr[arr.length - 1];

                function el(tag, cls, text) {
                    const e = document.createElement(tag);
                    if (cls) e.className = cls;
                    if (text != null) e.textContent = text;
                    return e;
                }

                function setHint(name, text) {
                    const h = document.querySelector(`.step-hint[data-hint="${name}"]`);
                    if (h) h.textContent = text;
                }

                function serviceName() {
                    return serviceSelect.value ? serviceSelect.selectedOptions[0].text : '';
                }

                function optionText(select, value) {
                    const o = [...select.options].find((opt) => opt.value === String(value));
                    return o ? o.text : '';
                }

                function markDirty() { dirty = true; }

                // Pedidos com sessão: Accept JSON faz o Laravel devolver 401/419 em vez de redirecionar para o login
                async function fetchJson(url, signal) {
                    const res = await fetch(url, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin',
                        signal,
                    });
                    if (res.status === 401 || res.status === 419) {
                        sessionAlert.classList.remove('d-none');
                        sessionAlert.classList.add('d-flex');
                        throw new Error('session');
                    }
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    return res.json();
                }

                // =====================================================
                // Passo 1 — Serviço, profissional, dia e horário
                // =====================================================
                function loadProfessionals(serviceId, preselect) {
                    proFilter.disabled = true;
                    return fetchJson(`/services/${encodeURIComponent(serviceId)}/professionals`)
                        .then((list) => {
                            proFilter.innerHTML = '';
                            proFilter.appendChild(new Option('Qualquer profissional', ''));
                            (Array.isArray(list) ? list : []).forEach((p) => proFilter.appendChild(new Option(p.name, p.id)));
                            if (preselect && optionText(proFilter, preselect)) proFilter.value = String(preselect);
                            proFilter.disabled = false;
                            setHint('professional', 'Opcional. Deixe em «Qualquer profissional» para ver todos.');
                        })
                        .catch((err) => {
                            if (err.message !== 'session') console.error('Erro ao carregar profissionais:', err);
                            setHint('professional', 'Não foi possível carregar os profissionais.');
                        });
                }

                function enableDay(on) {
                    dayInput.disabled = !on;
                    dayQuick.forEach((b) => { b.disabled = !on; });
                    updateDayNav();
                    setHint('day', on ? '' : 'Disponível depois de escolher o serviço.');
                }

                function updateDayNav() {
                    const off = dayInput.disabled;
                    const atMin = !INIT.is_edit && (!dayInput.value || dayInput.value <= todayISO);
                    dayPrev.disabled = off || !dayInput.value || atMin;
                    dayNext.disabled = off;
                }

                function setDay(iso) {
                    if (!INIT.is_edit && iso < todayISO) iso = todayISO;
                    if (dayInput.value === iso) return;
                    dayInput.value = iso;
                    onDayChange();
                }

                function onDayChange() {
                    if (!INIT.is_edit && dayInput.value && dayInput.value < todayISO) dayInput.value = todayISO;
                    clearSelection();
                    updateDayNav();
                    markDirty();
                    smsRecalcDate();
                    loadSlots();
                }

                function showSlotsPlaceholder(text, opts = {}) {
                    slotsBox.innerHTML = '';
                    const box = el('div', 'slots-placeholder');
                    if (opts.spinner) {
                        box.appendChild(el('span', 'spinner-border spinner-border-sm me-2'));
                    }
                    box.appendChild(document.createTextNode(text));
                    if (opts.action) {
                        const b = el('button', 'btn btn-sm btn-label-primary ms-2', opts.action.label);
                        b.type = 'button';
                        b.addEventListener('click', opts.action.onClick);
                        box.appendChild(b);
                    }
                    slotsBox.appendChild(box);
                    slotsStatus.textContent = '';
                }

                function clearSelection() {
                    selected = null;
                    timeInput.value = '';
                    proInput.value = '';
                }

                function loadSlots() {
                    const serviceId = serviceSelect.value;
                    const day = dayInput.value;
                    if (!serviceId) { showSlotsPlaceholder('Escolha o serviço para ver os horários livres.'); refresh(); return; }
                    if (!day) { showSlotsPlaceholder('Escolha o dia para ver os horários livres.'); refresh(); return; }

                    const req = ++slotsRequest;
                    slotsBox.setAttribute('aria-busy', 'true');
                    showSlotsPlaceholder('A carregar horários…', { spinner: true });

                    const params = new URLSearchParams({ service_id: serviceId, start_date: day, end_date: day });
                    if (proFilter.value) params.append('professional_id', proFilter.value);

                    fetchJson(`/available-slots?${params.toString()}`)
                        .then((data) => {
                            if (req !== slotsRequest) return;
                            renderSlots(Array.isArray(data) ? data : []);
                        })
                        .catch((err) => {
                            if (req !== slotsRequest) return;
                            if (err.message !== 'session') console.error('Erro ao carregar horários:', err);
                            clearSelection();
                            showSlotsPlaceholder('Não foi possível carregar os horários.', {
                                action: { label: 'Tentar de novo', onClick: loadSlots },
                            });
                        })
                        .finally(() => {
                            if (req !== slotsRequest) return;
                            slotsBox.removeAttribute('aria-busy');
                            refresh();
                        });
                }

                function renderSlots(data) {
                    const groups = data
                        .map((e) => ({
                            id: String(e.professional_id ?? ''),
                            name: e.professional_name || optionText(proFilter, e.professional_id),
                            slots: [...new Set(e.available_slots || [])],
                            current: null,
                        }))
                        .filter((g) => g.slots.length);

                    // Na edição, o horário atual da marcação não vem como livre: acrescenta-o
                    if (keepInitialSlot && dayInput.value === INIT.day && serviceSelect.value === String(INIT.service_id)) {
                        const pid = INIT.professional_id ? String(INIT.professional_id) : '';
                        if (!proFilter.value || proFilter.value === pid) {
                            let g = groups.find((x) => x.id === pid);
                            if (!g) {
                                g = { id: pid, name: optionText(proFilter, pid), slots: [], current: null };
                                groups.push(g);
                            }
                            if (!g.slots.includes(INIT.start_hour)) g.slots.push(INIT.start_hour);
                            g.current = INIT.start_hour;
                        }
                    }

                    groups.forEach((g) => g.slots.sort());

                    // A seleção anterior só se mantém se ainda existir
                    if (selected) {
                        const g = groups.find((x) => x.slots.includes(selected.time) && (!selected.proId || x.id === selected.proId));
                        if (g) {
                            selected.proId = g.id;
                            selected.proName = g.name;
                            proInput.value = g.id;
                        } else {
                            clearSelection();
                        }
                    }

                    slotsBox.innerHTML = '';
                    if (!groups.length) {
                        showSlotsPlaceholder('Sem horários livres neste dia.', {
                            action: { label: 'Ver dia seguinte', onClick: () => setDay(addDays(dayInput.value, 1)) },
                        });
                        return;
                    }

                    if (groups.length === 1) {
                        const g = groups[0];
                        const morning = g.slots.filter((t) => t < '13:00');
                        const afternoon = g.slots.filter((t) => t >= '13:00');
                        if (morning.length) slotsBox.appendChild(slotGroup('Manhã', morning, g, false));
                        if (afternoon.length) slotsBox.appendChild(slotGroup('Tarde', afternoon, g, false));
                    } else {
                        groups.forEach((g) => slotsBox.appendChild(slotGroup(g.name || 'Profissional', g.slots, g, true)));
                    }

                    const total = groups.reduce((n, g) => n + g.slots.length, 0);
                    slotsStatus.textContent = total === 1 ? '1 horário livre' : `${total} horários livres`;
                }

                function slotGroup(title, slots, g, perProfessional) {
                    const wrap = el('div', 'slot-group');
                    wrap.appendChild(el('div', 'slot-group-title', title));
                    const grid = el('div', 'slot-grid');
                    grid.setAttribute('role', 'group');
                    grid.setAttribute('aria-label', title);
                    slots.forEach((t) => {
                        const b = el('button', 'slot-chip', t);
                        b.type = 'button';
                        b.dataset.time = t;
                        b.dataset.pro = g.id;
                        if (g.current === t) {
                            b.appendChild(el('small', null, 'atual'));
                            b.title = 'Horário atual desta marcação';
                        }
                        if (perProfessional) b.setAttribute('aria-label', `${t} com ${g.name}`);
                        b.setAttribute('aria-pressed', String(!!selected && selected.time === t && selected.proId === g.id));
                        b.addEventListener('click', () => selectSlot(t, g));
                        grid.appendChild(b);
                    });
                    wrap.appendChild(grid);
                    return wrap;
                }

                function selectSlot(time, g) {
                    selected = { time, proId: g.id, proName: g.name };
                    timeInput.value = time;
                    proInput.value = g.id;
                    slotsBox.classList.remove('is-invalid-box');
                    slotsBox.querySelectorAll('.slot-chip').forEach((b) => {
                        b.setAttribute('aria-pressed', String(b.dataset.time === time && b.dataset.pro === g.id));
                    });
                    markDirty();
                    refresh();
                }

                serviceSelect.addEventListener('change', () => {
                    clearSelection();
                    markDirty();
                    const id = serviceSelect.value;
                    if (!id) {
                        proFilter.disabled = true;
                        setHint('professional', 'Escolha primeiro o serviço.');
                        enableDay(false);
                        showSlotsPlaceholder('Escolha o serviço e o dia para ver os horários livres.');
                    } else {
                        enableDay(true);
                        showSlotsPlaceholder('A carregar…', { spinner: true });
                        loadProfessionals(id, null).then(loadSlots);
                    }
                    applyTemplate();
                    refresh();
                });

                proFilter.addEventListener('change', () => {
                    if (selected && proFilter.value && selected.proId !== proFilter.value) clearSelection();
                    markDirty();
                    loadSlots();
                });

                dayInput.addEventListener('change', onDayChange);
                dayPrev.addEventListener('click', () => { if (dayInput.value) setDay(addDays(dayInput.value, -1)); });
                dayNext.addEventListener('click', () => setDay(dayInput.value ? addDays(dayInput.value, 1) : todayISO));
                dayQuick.forEach((b) => b.addEventListener('click', () => setDay(addDays(todayISO, Number(b.dataset.dayOffset)))));

                // =====================================================
                // Passo 2 — Cliente
                // =====================================================
                function phoneLabel(c) {
                    return (c.phone_1_country_code ? '+' + c.phone_1_country_code + ' ' : '') + (c.phone_1 || '');
                }

                function setClientState(state, note) {
                    clientState = state;
                    const showCard = state === 'selected' || state === 'editing';
                    const showFields = state === 'new' || state === 'editing';

                    clientSearchWrap.classList.toggle('d-none', state !== 'search');
                    selectedCard.classList.toggle('d-none', !showCard);
                    clientFields.classList.toggle('d-none', !showFields);
                    clientEditBtn.classList.toggle('d-none', state === 'editing');

                    clientName.required = showFields;
                    clientPhone.required = showFields;

                    if (state === 'new') {
                        clientFieldsTitle.textContent = 'Novo cliente';
                        clientCancel.textContent = 'Procurar cliente existente';
                    } else if (state === 'editing') {
                        clientFieldsTitle.textContent = 'Editar dados do cliente';
                        clientCancel.textContent = 'Cancelar edição';
                    }

                    const noteText = note || (state === 'editing' ? 'As alterações ficam guardadas na ficha deste cliente.' : '');
                    clientFieldsNote.textContent = noteText;
                    clientFieldsNote.classList.toggle('d-none', !noteText);
                    refresh();
                }

                function fillCard(c) {
                    scName.textContent = c.name || '(sem nome)';
                    scMeta.textContent = [phoneLabel(c), c.email].filter(Boolean).join(' · ');
                    scMinor.classList.toggle('d-none', !c.is_minor);
                    scMinorNote.classList.toggle('d-none', !c.is_minor);
                }

                function selectClient(c, silent) {
                    currentClient = c;
                    clientIdInput.value = c.id;
                    clientName.value = c.name || '';
                    clientEmail.value = c.email || '';
                    clientPhone.value = c.phone_1 || '';
                    clientSearch.value = '';
                    hideResults();
                    fillCard(c);
                    if (!silent) markDirty();
                    if (!c.phone_1) {
                        setClientState('editing', 'Este cliente não tem telemóvel. Indique um para gravar a marcação.');
                        clientPhone.focus();
                    } else {
                        setClientState('selected');
                    }
                }

                function startNewClient(prefill, silent) {
                    currentClient = null;
                    clientIdInput.value = '';
                    clientName.value = prefill.name || '';
                    clientEmail.value = prefill.email || '';
                    clientPhone.value = prefill.phone || '';
                    hideResults();
                    if (!silent) markDirty();
                    setClientState('new');
                    if (!silent) ([clientName, clientPhone].find((i) => !i.value) || clientEmail).focus();
                }

                function clearClient() {
                    currentClient = null;
                    clientIdInput.value = '';
                    clientName.value = '';
                    clientEmail.value = '';
                    clientPhone.value = '';
                    clientSearch.value = '';
                    markDirty();
                    setClientState('search');
                    clientSearch.focus();
                }

                function prefillFromQuery(q) {
                    if (q.includes('@')) return { email: q };
                    if (/^[+\d][\d\s().-]{5,}$/.test(q)) return { phone: q.replace(/[^\d+]/g, '') };
                    return { name: q };
                }

                clientNewBtn.addEventListener('click', () => startNewClient(prefillFromQuery(clientSearch.value.trim() || '')));
                clientClearBtn.addEventListener('click', clearClient);
                clientEditBtn.addEventListener('click', () => { setClientState('editing'); clientName.focus(); });
                clientCancel.addEventListener('click', () => {
                    if (clientState === 'editing' && currentClient && currentClient.phone_1) {
                        clientName.value = currentClient.name || '';
                        clientEmail.value = currentClient.email || '';
                        clientPhone.value = currentClient.phone_1 || '';
                        setClientState('selected');
                    } else {
                        clearClient();
                    }
                });

                [clientName, clientEmail, clientPhone].forEach((i) => i.addEventListener('input', refresh));

                // ----- Pesquisa -----
                let searchTimer = null;
                let searchCtrl = null;
                let resultItems = [];
                let activeIndex = -1;

                clientSearch.addEventListener('input', () => {
                    const q = clientSearch.value.trim();
                    clearTimeout(searchTimer);
                    if (q.length < 2) { hideResults(); return; }
                    searchTimer = setTimeout(() => runSearch(q), 250);
                });

                clientSearch.addEventListener('keydown', (e) => {
                    const open = clientResults.style.display === 'block';
                    if (e.key === 'ArrowDown' && open) { e.preventDefault(); setActive(Math.min(activeIndex + 1, resultItems.length - 1)); }
                    else if (e.key === 'ArrowUp' && open) { e.preventDefault(); setActive(Math.max(activeIndex - 1, 0)); }
                    else if (e.key === 'Enter') {
                        e.preventDefault(); // nunca submeter o formulário a partir da pesquisa
                        if (open && activeIndex >= 0) resultItems[activeIndex].click();
                    } else if (e.key === 'Escape') hideResults();
                });

                function runSearch(q) {
                    if (searchCtrl) searchCtrl.abort();
                    const ctrl = new AbortController();
                    searchCtrl = ctrl;
                    clientSpinner.classList.remove('d-none');
                    fetchJson(`/admin/clients/search?q=${encodeURIComponent(q)}`, ctrl.signal)
                        .then((list) => renderResults(Array.isArray(list) ? list : [], q))
                        .catch((err) => { if (err.name !== 'AbortError' && err.message !== 'session') hideResults(); })
                        .finally(() => { if (ctrl === searchCtrl) clientSpinner.classList.add('d-none'); });
                }

                function renderResults(list, q) {
                    clientResults.innerHTML = '';
                    resultItems = [];
                    activeIndex = -1;

                    if (!list.length) {
                        clientResults.appendChild(el('div', 'list-group-item text-muted small', 'Nenhum cliente encontrado.'));
                    }

                    list.forEach((c) => {
                        const item = el('button', 'list-group-item list-group-item-action');
                        item.type = 'button';
                        item.setAttribute('role', 'option');
                        item.appendChild(el('strong', null, c.name || '(sem nome)'));
                        if (c.is_minor) item.appendChild(el('span', 'badge bg-warning text-dark ms-1', 'Menor'));
                        item.appendChild(el('small', 'text-muted d-block', [phoneLabel(c), c.email].filter(Boolean).join(' · ')));
                        item.addEventListener('click', () => selectClient(c));
                        clientResults.appendChild(item);
                        resultItems.push(item);
                    });

                    const create = el('button', 'list-group-item list-group-item-action text-primary');
                    create.type = 'button';
                    create.setAttribute('role', 'option');
                    create.textContent = `+ Criar cliente «${q}»`;
                    create.addEventListener('click', () => startNewClient(prefillFromQuery(q)));
                    clientResults.appendChild(create);
                    resultItems.push(create);

                    resultItems.forEach((it, i) => { it.id = `client_opt_${i}`; });
                    clientResults.style.display = 'block';
                    clientSearch.setAttribute('aria-expanded', 'true');
                    setActive(list.length ? 0 : resultItems.length - 1);
                }

                function setActive(i) {
                    activeIndex = i;
                    resultItems.forEach((it, idx) => {
                        it.classList.toggle('is-active', idx === i);
                        it.setAttribute('aria-selected', String(idx === i));
                    });
                    if (resultItems[i]) {
                        clientSearch.setAttribute('aria-activedescendant', resultItems[i].id);
                        resultItems[i].scrollIntoView({ block: 'nearest' });
                    }
                }

                function hideResults() {
                    clientResults.style.display = 'none';
                    clientSearch.setAttribute('aria-expanded', 'false');
                    clientSearch.removeAttribute('aria-activedescendant');
                }

                document.addEventListener('click', (e) => {
                    if (!clientResults.contains(e.target) && e.target !== clientSearch) hideResults();
                });

                // =====================================================
                // Passo 4 — Lembrete SMS
                // =====================================================
                const GSM_BASIC = '@£$¥èéùìòÇ\nØø\rÅåΔ_ΦΓΛΩΠΨΣΘΞÆæßÉ !"#¤%&\'()*+,-./0123456789:;<=>?¡ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÑÜ§¿abcdefghijklmnopqrstuvwxyzäöñüà';
                const GSM_EXT = '^{}\\[~]|€\f';

                // Acentos como á, ã, ç, õ, ê não existem em GSM-7: a mensagem passa a Unicode (70 car./SMS)
                function smsInfo(text) {
                    let gsm = true;
                    let len = 0;
                    for (const ch of text) {
                        if (GSM_BASIC.includes(ch)) len += 1;
                        else if (GSM_EXT.includes(ch)) len += 2;
                        else { gsm = false; break; }
                    }
                    if (!gsm) len = text.length;
                    const single = gsm ? 160 : 70;
                    const multi = gsm ? 153 : 67;
                    return { gsm, len, segs: len === 0 ? 0 : (len <= single ? 1 : Math.ceil(len / multi)) };
                }

                function previewText() {
                    return (smsText.value || '')
                        .split('[NOME_CLIENTE]').join(clientName.value.trim() || 'Nome do cliente')
                        .split('[SERVICO]').join(serviceName() || 'serviço')
                        .split('[DATA]').join(dayInput.value ? fmtDayShort(dayInput.value) : 'dd/mm/aaaa')
                        .split('[HORA]').join(timeInput.value || 'hh:mm');
                }

                function applyTemplate() {
                    if (smsManualText || !serviceSelect.value) return;
                    const tpl = String(TEMPLATES[serviceSelect.value] || '').trim();
                    smsText.value = tpl || CFG.defaultText;
                }

                function smsRecalcDate() {
                    if (smsManualDate || !dayInput.value) return;
                    smsDate.value = addDays(dayInput.value, -CFG.advanceDays);
                    if (!smsHour.value) smsHour.value = CFG.sendHour;
                }

                function setSmsEnabled(on) {
                    smsFields.classList.toggle('d-none', !on);
                    smsOffNote.classList.toggle('d-none', on);
                    // Campos desativados não são enviados: sem texto, o backend não cria lembrete
                    [smsText, smsDate, smsHour].forEach((i) => { i.disabled = !on; });
                    if (on) {
                        if (!smsText.value.trim()) { smsManualText = false; applyTemplate(); }
                        smsRecalcDate();
                    }
                    refresh();
                }

                function smsWarnings() {
                    const warnings = [];
                    if (!smsToggle.checked) return warnings;
                    if (!clientPhone.value.trim()) warnings.push('O cliente não tem telemóvel, por isso o lembrete não pode ser enviado.');
                    if (smsDate.value && smsHour.value) {
                        const sendAt = new Date(`${smsDate.value}T${smsHour.value}:00`);
                        if (sendAt < new Date()) warnings.push('A data de envio já passou. O lembrete não será enviado.');
                        if (dayInput.value && timeInput.value) {
                            const apptAt = new Date(`${dayInput.value}T${timeInput.value}:00`);
                            if (sendAt >= apptAt) warnings.push('O lembrete seria enviado depois da hora da marcação.');
                        }
                    }
                    return warnings;
                }

                smsToggle.addEventListener('change', () => { markDirty(); setSmsEnabled(smsToggle.checked); });
                smsText.addEventListener('input', () => { smsManualText = true; refresh(); });
                smsDate.addEventListener('change', () => { smsManualDate = true; refresh(); });
                smsHour.addEventListener('change', () => { smsManualDate = true; refresh(); });

                // =====================================================
                // Resumo, validação e envio
                // =====================================================
                function setSum(id, value) {
                    const dd = $(id);
                    dd.textContent = value || '—';
                    dd.classList.toggle('is-empty', !value);
                }

                function missingItems() {
                    const m = [];
                    if (!serviceSelect.value) m.push('serviço');
                    if (!dayInput.value) m.push('dia');
                    if (!timeInput.value) m.push('horário');
                    if (clientState === 'search') m.push('cliente');
                    else if (clientState !== 'selected') {
                        if (!clientName.value.trim()) m.push('nome do cliente');
                        if (!clientPhone.value.trim()) m.push('telemóvel do cliente');
                    }
                    if (smsToggle.checked) {
                        if (!smsText.value.trim()) m.push('texto do lembrete');
                        if (!smsDate.value || !smsHour.value) m.push('data e hora do lembrete');
                    }
                    return m;
                }

                function refresh() {
                    // Resumo
                    setSum('sum_service', serviceName());
                    const proName = selected
                        ? (selected.proName || optionText(proFilter, selected.proId))
                        : (proFilter.value ? optionText(proFilter, proFilter.value) : '');
                    setSum('sum_pro', proName);
                    setSum('sum_day', dayInput.value ? fmtDayLong(dayInput.value) : '');
                    setSum('sum_time', timeInput.value);
                    const name = clientState === 'search' ? '' : clientName.value.trim();
                    setSum('sum_client', name && currentClient && currentClient.is_minor ? `${name} (menor)` : name);

                    // SMS
                    const preview = previewText();
                    const info = smsInfo(preview);
                    smsPreview.textContent = preview || 'Escreva a mensagem acima.';
                    smsChars.textContent = info.len;
                    smsSegs.textContent = info.segs;
                    smsEncoding.textContent = info.gsm || !preview
                        ? ''
                        : 'Esta mensagem tem caracteres acentuados (como á, ã ou ç), por isso cada SMS leva até 70 caracteres em vez de 160.';
                    const warnings = smsWarnings();
                    smsWarn.textContent = warnings.join(' ');
                    smsWarn.classList.toggle('d-none', !warnings.length);

                    if (!smsToggle.checked) setSum('sum_sms', 'Não enviar');
                    else if (smsDate.value && smsHour.value) setSum('sum_sms', `${fmtDayShort(smsDate.value)}, ${smsHour.value}`);
                    else setSum('sum_sms', '');

                    // Botão
                    const missing = missingItems();
                    submitBtn.disabled = submitting || missing.length > 0;
                    missingHint.textContent = missing.length ? `Falta indicar: ${listPT(missing)}.` : 'Tudo pronto para gravar.';
                    missingHint.className = 'small mb-3 ' + (missing.length ? 'text-muted' : 'text-success');
                }

                form.addEventListener('input', markDirty);
                form.addEventListener('change', markDirty);

                form.addEventListener('submit', (e) => {
                    if (submitting || missingItems().length) { e.preventDefault(); refresh(); return; }
                    submitting = true;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>A gravar…';
                });

                window.addEventListener('beforeunload', (e) => {
                    if (dirty && !submitting) { e.preventDefault(); e.returnValue = ''; }
                });

                // =====================================================
                // Arranque (inclui modo de edição e regresso após erro de validação)
                // =====================================================
                if (INIT.client_id) {
                    selectClient({
                        id: INIT.client_id,
                        name: INIT.client_name,
                        email: INIT.client_email,
                        phone_1: INIT.client_phone_1,
                        phone_1_country_code: INIT.client_phone_cc,
                        is_minor: INIT.client_is_minor,
                    }, true);
                } else if (INIT.client_name || INIT.client_phone_1 || INIT.client_email) {
                    startNewClient({ name: INIT.client_name, email: INIT.client_email, phone: INIT.client_phone_1 }, true);
                } else {
                    setClientState('search');
                }

                setSmsEnabled(smsToggle.checked);

                if (serviceSelect.value) {
                    enableDay(true);
                    showSlotsPlaceholder('A carregar…', { spinner: true });
                    loadProfessionals(serviceSelect.value, INIT.professional_id).then(loadSlots);
                } else {
                    enableDay(false);
                    showSlotsPlaceholder('Escolha o serviço e o dia para ver os horários livres.');
                }

                refresh();
                dirty = false;
            });
        </script>
    @endverbatim
@endpush
