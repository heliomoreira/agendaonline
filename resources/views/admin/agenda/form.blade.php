@extends('layouts.app')
@section('content')
    <style>
        .step-badge {
            width: 28px; height: 28px; min-width: 28px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 50%; font-weight: 600; font-size: .85rem;
        }
        #client_results .list-group-item-action:hover { background-color: var(--bs-primary-bg-subtle, #e7e7ff); }
        .client-picked { border-left: 3px solid var(--bs-success, #71dd37); }
    </style>

    @if (session('success'))
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-solid-success alert-dismissible fade show d-flex align-items-center" role="alert">
                    <span class="alert-icon rounded me-2"><i class="icon-base ti tabler-check icon-md"></i></span>
                    <div class="flex-grow-1">{{ session('success') }}</div>
                    <button type="button" class="btn-close ms-2" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            </div>
        </div>
    @endif
    @if ($errors->any())
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-solid-danger d-flex align-items-center" role="alert">
                    <span class="alert-icon rounded"><i class="icon-base ti tabler-x"></i></span>
                    <div>
                        <strong>Existem alguns erros:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row mb-2">
        <div class="col-md-12">
            <h5 class="d-flex align-items-center gap-2 m-0">
                <i class="icon-base ti tabler-calendar"></i>
                {{ $agenda->id ? 'Editar Agendamento' : 'Novo Agendamento' }}
            </h5>
            <hr class="my-2"/>
        </div>
    </div>

    <div class="row g-6">
        <div class="col-md-12">
            @if(!$agenda->id)
                {{ html()->modelForm($agenda, 'POST', route('book.slot',['admin'=>true]))->open() }}
            @else
                {{ html()->modelForm($agenda, 'PUT', route('agenda.update', $agenda->id))->open() }}
            @endif
            {{ html()->token() }}

            <div class="card">
                <div class="card-header header-elements">
                    <h5 class="mb-0 me-2">Detalhe da Marcação</h5>
                </div>
                <div class="card-body">

                    {{-- ===== Passo 1 — Serviço e horário ===== --}}
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <span class="step-badge bg-primary text-white">1</span>
                        <h6 class="mb-0 fw-semibold">Serviço e horário</h6>
                    </div>

                    <div class="row g-6">
                        <div class="col-md-3">
                            <label class="form-label" for="service_id">Serviço</label>
                            {{html()->select('service_id')->options($services)->placeholder('Seleccionar...')->class('form-select')}}
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="professional_id">Profissional</label>
                            <select id="professional_id" name="professional_id" class="form-select" disabled>
                                <option value="">Todos</option>
                                @foreach($professionals as $pro)
                                    <option value="{{ $pro->id }}">{{ $pro->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted step-hint" data-hint="professional">Escolha primeiro o serviço.</small>
                        </div>
                        <div class="col-md-2">
                            <label for="day" class="form-label">Dia</label>
                            <input type="date" id="day" name="day" class="form-control" disabled required>
                            <small class="text-muted step-hint" data-hint="day">Disponível após o serviço.</small>
                        </div>
                        <div class="col-md-4">
                            <label for="time_select" class="form-label">Horários Disponíveis</label>
                            <select id="time_select" name="start_hour" class="form-select" disabled required>
                                <option value="">Selecionar horário...</option>
                            </select>
                            <small class="text-muted step-hint" data-hint="time">Escolha o dia para ver horários.</small>
                        </div>
                    </div>

                    <hr class="my-5">

                    {{-- ===== Passo 2 — Cliente ===== --}}
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <span class="step-badge bg-primary text-white">2</span>
                        <h6 class="mb-0 fw-semibold">Cliente</h6>
                    </div>

                    <input type="hidden" id="client_id" name="client_id">

                    <div class="row g-6">
                        {{-- Pesquisa --}}
                        <div class="col-md-7 position-relative">
                            <label for="client_search" class="form-label">Procurar cliente existente</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="icon-base ti tabler-search"></i></span>
                                <input type="text" id="client_search" class="form-control"
                                       placeholder="Nome, telemóvel ou email..." autocomplete="off">
                                <span class="input-group-text d-none" id="client_spinner">
                                    <span class="spinner-border spinner-border-sm" role="status"></span>
                                </span>
                            </div>
                            <div id="client_results" class="list-group position-absolute shadow"
                                 style="z-index:1050; display:none; max-height:280px; overflow-y:auto;
                                        width:calc(100% - 1.5rem);
                                        background-color: var(--bs-paper-bg, var(--bs-body-bg, #fff));
                                        border:1px solid var(--bs-border-color, #d9dee3);
                                        border-radius:.375rem;">
                            </div>
                            <small class="text-muted" id="client_hint">
                                Escreva para procurar um cliente, ou preencha os campos para criar um novo.
                            </small>
                        </div>
                    </div>

                    {{-- Cartão do cliente selecionado --}}
                    <div id="selected_client_card" class="card client-picked bg-light-subtle mt-4 d-none">
                        <div class="card-body py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge bg-label-success rounded-pill p-2">
                                    <i class="icon-base ti tabler-user-check"></i>
                                </span>
                                <div>
                                    <div class="fw-semibold" id="sc_name"></div>
                                    <small class="text-muted" id="sc_meta"></small>
                                </div>
                                <span class="badge bg-warning text-dark d-none" id="sc_minor">
                                    <i class="icon-base ti tabler-alert-triangle me-1"></i> Menor de idade
                                </span>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="client_clear">
                                <i class="icon-base ti tabler-switch-horizontal me-1"></i> Trocar / novo
                            </button>
                        </div>
                    </div>

                    {{-- Aviso quando é menor --}}
                    <div id="minor_notice" class="alert alert-warning d-flex align-items-center mt-3 d-none" role="alert">
                        <i class="icon-base ti tabler-info-circle me-2"></i>
                        <div>Este cliente é <strong>menor de idade</strong>. Se tiver a opção ativa, será enviado SMS também ao encarregado.</div>
                    </div>

                    <div class="row g-6 mt-1">
                        <div class="col-12">
                            <small class="text-muted text-uppercase fw-semibold">Dados do cliente</small>
                        </div>
                        <div class="col-md-5">
                            <label for="client_name" class="form-label">Nome</label>
                            <input type="text" id="client_name" name="client_name" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="client_email" class="form-label">Email</label>
                            <input type="email" id="client_email" name="client_email" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="client_phone_1" class="form-label">Telemóvel</label>
                            <input type="text" id="client_phone_1" name="client_phone_1" class="form-control" required>
                        </div>
                    </div>

                    <hr class="my-5">

                    {{-- ===== Passo 3 — Observações ===== --}}
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <span class="step-badge bg-primary text-white">3</span>
                        <h6 class="mb-0 fw-semibold">Observações <span class="text-muted fw-normal">(opcional)</span></h6>
                    </div>

                    <div class="row g-6">
                        <div class="col-md-8">
                            <textarea class="form-control" id="notes" name="notes" rows="4"
                                      placeholder="Notas internas sobre a marcação...">{{ old('notes', $agenda->notes) }}</textarea>
                        </div>
                    </div>

                    <hr class="my-5">

                    {{-- ===== Passo 4 — Lembrete SMS ===== --}}
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <span class="step-badge bg-primary text-white">4</span>
                        <h6 class="mb-0 fw-semibold">Lembrete SMS <span class="text-muted fw-normal">(opcional)</span></h6>
                    </div>

                    @php $reminder = $agenda->reminderNotification; @endphp {{-- ajusta ao nome real da relação --}}

                    <div class="row g-6">
                        <div class="col-md-8">
                            <label for="sms_text" class="form-label">Mensagem</label>
                            <textarea class="form-control" id="sms_text" name="sms_text" rows="4"
                                      placeholder="Texto do lembrete...">{{ old('sms_text', $reminder?->text) }}</textarea>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">Campos como <code>[NOME_CLIENTE]</code>, <code>[DATA]</code> e <code>[HORA]</code> são preenchidos ao enviar.</small>
                                <small class="text-muted"><span id="sms_chars">0</span> car. · <span id="sms_segs">1</span> SMS</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="sms_send_date" class="form-label">Data de envio</label>
                            <input type="date" id="sms_send_date" name="sms_send_date" class="form-control"
                                   value="{{ old('sms_send_date', $reminder?->send_day ? \Carbon\Carbon::parse($reminder->send_day)->format('Y-m-d') : '') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="sms_send_hour" class="form-label">Hora de envio</label>
                            <input type="time" id="sms_send_hour" name="sms_send_hour" class="form-control"
                                   value="{{ old('sms_send_hour', $reminder?->send_hour ? \Carbon\Carbon::parse($reminder->send_hour)->format('H:i') : '') }}">
                        </div>
                    </div>
                    <small class="text-muted d-block mt-2" id="sms_hint"></small>
                </div>

                <div class="card-footer">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <button type="submit" class="btn btn-primary waves-effect waves-light" id="submitBtn">
                            <i class="icon-base ti tabler-device-floppy"></i> Gravar
                        </button>
                        <a href="{{ route('agenda.index') }}" class="btn btn-secondary waves-effect waves-light">
                            <i class="icon-base ti tabler-arrow-left"></i> Voltar
                        </a>
                        <small class="text-muted ms-auto" id="submit_hint">
                            <i class="icon-base ti tabler-info-circle"></i>
                            Escolha serviço, dia e horário para poder gravar.
                        </small>
                    </div>
                </div>
            </div>
            {{html()->closeModelForm()}}
        </div>
    </div>
@endsection
@push('scripts')
    @php $settings = \App\Models\Setting::current(); @endphp
    <script>
        window.SMS_CFG = {
            advanceDays: {{ (int) ($settings->sms_advance_days ?? 1) }},
            sendHour: "{{ $settings->sms_send_hour ? \Carbon\Carbon::parse($settings->sms_send_hour)->format('H:i') : '09:00' }}",
            defaultText: "Olá [NOME_CLIENTE], lembramos a sua marcação de [SERVICO] em [DATA] às [HORA].",
        };
        // Mapa service_id -> texto do template, vindo do controller (sem endpoint)
        window.SMS_TEMPLATES = @json($smsTemplates ?? []);
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const serviceSelect = document.getElementById('service_id');
            const professionalSelect = document.getElementById('professional_id');
            const dayInput = document.getElementById('day');
            const timeSelect = document.getElementById('time_select');
            const submitBtn = document.getElementById('submitBtn');
            const submitHint = document.getElementById('submit_hint');

            professionalSelect.disabled = true;
            dayInput.disabled = true;
            timeSelect.disabled = true;
            submitBtn.disabled = true;

            function setHint(name, text) {
                const el = document.querySelector(`.step-hint[data-hint="${name}"]`);
                if (el) el.textContent = text;
            }

            serviceSelect.addEventListener('change', () => {
                const serviceId = serviceSelect.value;
                const hasService = serviceId !== '';

                professionalSelect.innerHTML = '<option value="">Todos</option>';
                timeSelect.innerHTML = '<option value="">Selecionar horário...</option>';
                timeSelect.disabled = true;
                setHint('time', 'Escolha o dia para ver horários.');

                if (hasService) {
                    fetch(`/services/${serviceId}/professionals`)
                        .then(res => res.json())
                        .then(data => {
                            data.forEach(pro => {
                                const option = document.createElement('option');
                                option.value = pro.id;
                                option.text = pro.name;
                                professionalSelect.appendChild(option);
                            });
                            professionalSelect.disabled = false;
                            dayInput.disabled = false;
                            setHint('professional', 'Opcional — deixe em "Todos" se indiferente.');
                            setHint('day', 'Escolha a data pretendida.');
                        })
                        .catch(err => {
                            console.error('Erro ao carregar profissionais:', err);
                            professionalSelect.disabled = true;
                            dayInput.disabled = true;
                        });
                } else {
                    professionalSelect.disabled = true;
                    dayInput.disabled = true;
                    setHint('professional', 'Escolha primeiro o serviço.');
                    setHint('day', 'Disponível após o serviço.');
                }

                updateSubmitButtonState();
            });

            professionalSelect.addEventListener('change', clearAndLoadSlotsIfPossible);
            dayInput.addEventListener('change', clearAndLoadSlotsIfPossible);
            timeSelect.addEventListener('change', updateSubmitButtonState);

            function clearAndLoadSlotsIfPossible() {
                timeSelect.disabled = true;
                timeSelect.innerHTML = '<option value="">Selecionar horário...</option>';

                const hasService = serviceSelect.value !== '';
                const hasDay = dayInput.value !== '';

                if (hasService && hasDay) {
                    loadAvailableSlots();
                }
                updateSubmitButtonState();
            }

            function loadAvailableSlots() {
                const serviceId = serviceSelect.value;
                const professionalId = professionalSelect.value;
                const day = dayInput.value;

                if (!serviceId || !day) return;

                timeSelect.disabled = true;
                timeSelect.innerHTML = '<option>A carregar...</option>';

                const params = new URLSearchParams({ service_id: serviceId, start_date: day, end_date: day });
                if (professionalId) params.append('professional_id', professionalId);

                fetch(`/available-slots?${params.toString()}`)
                    .then(res => res.json())
                    .then(data => {
                        const seen = new Set();
                        timeSelect.innerHTML = '';

                        data.forEach(entry => {
                            entry.available_slots.forEach(time => {
                                if (!seen.has(time)) {
                                    seen.add(time);
                                    let label = time;
                                    if (!professionalId && entry.professional_name) {
                                        label += ` (${entry.professional_name})`;
                                    }
                                    const option = document.createElement('option');
                                    option.value = time;
                                    option.text = label;
                                    option.setAttribute('data-professional-id', entry.professional_id);
                                    timeSelect.appendChild(option);
                                }
                            });
                        });

                        if (seen.size === 0) {
                            const option = document.createElement('option');
                            option.value = 'none';
                            option.text = 'Sem horários disponíveis';
                            timeSelect.appendChild(option);
                            timeSelect.disabled = true;
                            setHint('time', 'Sem horários neste dia — tente outra data.');
                        } else {
                            const defaultOption = document.createElement('option');
                            defaultOption.value = '';
                            defaultOption.text = 'Selecionar horário...';
                            defaultOption.selected = true;
                            defaultOption.disabled = true;
                            timeSelect.insertBefore(defaultOption, timeSelect.firstChild);
                            timeSelect.disabled = false;
                            setHint('time', `${seen.size} horário(s) disponível(is).`);
                        }
                        updateSubmitButtonState();
                    })
                    .catch(err => {
                        console.error('Erro ao carregar horários:', err);
                        timeSelect.innerHTML = '<option>Erro a carregar horários</option>';
                        timeSelect.disabled = true;
                        updateSubmitButtonState();
                    });
            }

            function updateSubmitButtonState() {
                const serviceValid = serviceSelect.value !== '';
                const dayValid = dayInput.value !== '';
                const timeValid = timeSelect.value !== '' && !timeSelect.disabled;
                const ok = serviceValid && dayValid && timeValid;
                submitBtn.disabled = !ok;
                submitHint.classList.toggle('d-none', ok);
            }

            // ===== Pesquisa de cliente existente =====
            const clientSearch = document.getElementById('client_search');
            const clientResults = document.getElementById('client_results');
            const clientIdInput = document.getElementById('client_id');
            const clientName = document.getElementById('client_name');
            const clientEmail = document.getElementById('client_email');
            const clientPhone = document.getElementById('client_phone_1');
            const clientClear = document.getElementById('client_clear');
            const clientHint = document.getElementById('client_hint');
            const clientSpinner = document.getElementById('client_spinner');
            const selectedCard = document.getElementById('selected_client_card');
            const scName = document.getElementById('sc_name');
            const scMeta = document.getElementById('sc_meta');
            const scMinor = document.getElementById('sc_minor');
            const minorNotice = document.getElementById('minor_notice');
            let searchTimer = null;

            clientSearch.addEventListener('input', () => {
                const q = clientSearch.value.trim();
                clearTimeout(searchTimer);
                if (q.length < 2) { hideResults(); return; }
                searchTimer = setTimeout(() => runSearch(q), 250);
            });

            clientSearch.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') hideResults();
            });

            function runSearch(q) {
                clientSpinner.classList.remove('d-none');
                fetch(`/admin/clients/search?q=${encodeURIComponent(q)}`)
                    .then(res => res.json())
                    .then(renderResults)
                    .catch(() => hideResults())
                    .finally(() => clientSpinner.classList.add('d-none'));
            }

            function phoneLabel(c) {
                return (c.phone_1_country_code ? '+' + c.phone_1_country_code + ' ' : '') + (c.phone_1 || '');
            }

            function renderResults(list) {
                clientResults.innerHTML = '';
                if (!list.length) {
                    const empty = document.createElement('div');
                    empty.className = 'list-group-item text-muted small';
                    empty.textContent = 'Sem resultados — preencha os campos para criar novo.';
                    clientResults.appendChild(empty);
                    clientResults.style.display = 'block';
                    return;
                }
                list.forEach(c => {
                    const item = document.createElement('button');
                    item.type = 'button';
                    item.className = 'list-group-item list-group-item-action';
                    const minorTag = c.is_minor
                        ? ' <span class="badge bg-warning text-dark ms-1">Menor</span>' : '';
                    item.innerHTML =
                        `<strong>${c.name || '(sem nome)'}</strong>${minorTag}` +
                        `<br><small class="text-muted">${phoneLabel(c)}${c.email ? ' · ' + c.email : ''}</small>`;
                    item.addEventListener('click', () => selectClient(c));
                    clientResults.appendChild(item);
                });
                clientResults.style.display = 'block';
            }

            function selectClient(c) {
                clientIdInput.value = c.id;
                clientName.value = c.name || '';
                clientEmail.value = c.email || '';
                clientPhone.value = c.phone_1 || '';
                clientSearch.value = '';
                hideResults();

                scName.textContent = c.name || '(sem nome)';
                scMeta.textContent = [phoneLabel(c), c.email].filter(Boolean).join(' · ');
                scMinor.classList.toggle('d-none', !c.is_minor);
                minorNotice.classList.toggle('d-none', !c.is_minor);
                selectedCard.classList.remove('d-none');
                clientHint.classList.add('d-none');
            }

            function resetSelection() {
                clientIdInput.value = '';
                selectedCard.classList.add('d-none');
                minorNotice.classList.add('d-none');
                clientHint.classList.remove('d-none');
            }

            // Editar um campo à mão deixa de ser "cliente existente"
            [clientName, clientEmail, clientPhone].forEach(el => {
                el.addEventListener('input', () => {
                    if (clientIdInput.value) {
                        resetSelection();
                        clientHint.textContent = 'A criar/atualizar cliente com os dados introduzidos.';
                    }
                });
            });

            clientClear.addEventListener('click', () => {
                clientSearch.value = '';
                clientName.value = '';
                clientEmail.value = '';
                clientPhone.value = '';
                resetSelection();
                clientHint.textContent = 'Escreva para procurar um cliente, ou preencha os campos para criar um novo.';
                clientSearch.focus();
            });

            function hideResults() { clientResults.style.display = 'none'; }

            document.addEventListener('click', (e) => {
                if (!clientResults.contains(e.target) && e.target !== clientSearch) hideResults();
            });

            // ===== Passo 4 — Lembrete SMS =====
            const smsText = document.getElementById('sms_text');
            const smsDate = document.getElementById('sms_send_date');
            const smsHour = document.getElementById('sms_send_hour');
            const smsChars = document.getElementById('sms_chars');
            const smsSegs = document.getElementById('sms_segs');
            const smsHint = document.getElementById('sms_hint');

            // Se já houver valores (edição), consideram-se definidos manualmente e não são sobrepostos
            let smsManualText = smsText.value.trim() !== '';
            let smsManualDate = smsDate.value !== '';

            function smsCount() {
                const len = (smsText.value || '').length;
                smsChars.textContent = len;
                smsSegs.textContent = Math.max(1, Math.ceil(len / 160));
            }

            function smsUpdateHint() {
                if (smsDate.value && smsHour.value) {
                    smsHint.textContent = `O lembrete será enviado a ${smsDate.value} às ${smsHour.value}.`;
                } else {
                    smsHint.textContent = 'A data de envio é calculada a partir do dia da marcação.';
                }
            }

            // Preenche o texto do template ao escolher o serviço
            serviceSelect.addEventListener('change', () => {
                const serviceId = serviceSelect.value;
                if (!serviceId || smsManualText) return;
                const tpl = (window.SMS_TEMPLATES[serviceId] || '').trim();
                smsText.value = tpl || window.SMS_CFG.defaultText;
                smsCount();
            });

            // Data local em 'YYYY-MM-DD' sem passar por UTC (PT está em UTC+1 no verão -> toISOString saltava 1 dia)
            function fmtLocalDate(d) {
                const y = d.getFullYear();
                const m = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                return `${y}-${m}-${day}`;
            }

            // Recalcula a data de envio por defeito: dia da marcação − dias de antecedência (configurações)
            function smsRecalcDate() {
                if (smsManualDate || !dayInput.value) return;
                const d = new Date(dayInput.value + 'T00:00:00');
                d.setDate(d.getDate() - window.SMS_CFG.advanceDays);
                smsDate.value = fmtLocalDate(d);
                if (!smsHour.value) smsHour.value = window.SMS_CFG.sendHour;
                smsUpdateHint();
            }

            dayInput.addEventListener('change', smsRecalcDate);

            smsText.addEventListener('input', () => { smsManualText = true; smsCount(); });
            smsDate.addEventListener('change', () => { smsManualDate = true; smsUpdateHint(); });
            smsHour.addEventListener('change', () => { smsManualDate = true; smsUpdateHint(); });

            smsCount();
            smsUpdateHint();
        });
    </script>
@endpush
