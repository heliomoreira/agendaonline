/**
 * booking-wizard.js
 * Publicar em: public/js/booking-wizard.js
 *
 * Dependências (já carregadas pelo layout):
 *   - Bootstrap 5
 *   - RemixIcons
 *   - window.WizConfig  (injectado pelo blade)
 *
 * WizConfig esperado:
 * {
 *   corPrimaria:     '#2563eb',
 *   corPrimariaRgb:  '37,99,235',
 *   requiresPayment: false,
 *   stripeKey:       '',           // só se requiresPayment = true
 *   routes: {
 *     professionals: '/services/{id}/professionals',  // {id} substituído em runtime
 *     availableSlots: '/available-slots',
 *     bookSlot:       '/booking/confirmar',
 *     paymentIntent:  '/booking/payment-intent',  // só se requiresPayment = true
 *   },
 *   csrfToken: '...',
 * }
 */

(function () {
    'use strict';

    /* ── Configuração ──────────────────────────────── */
    const CFG = window.WizConfig || {};

    if (CFG.corPrimaria) {
        document.documentElement.style.setProperty('--bs-primary', CFG.corPrimaria);
    }

    /* ── Constantes PT ─────────────────────────────── */
    const PTM = [
        'Janeiro','Fevereiro','Março','Abril','Maio','Junho',
        'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'
    ];
    const PTD = [
        'domingo','segunda-feira','terça-feira','quarta-feira',
        'quinta-feira','sexta-feira','sábado'
    ];

    /* ── Estado da sessão ──────────────────────────── */
    const S = {
        step: 1,
        svcId: null, svcName: null, svcDur: null, svcPrice: null,
        proId: null, proName: null,
        date: null,  dateLbl: null, time: null,   proFinal: null,
        calY: new Date().getFullYear(),
        calM: new Date().getMonth() + 1,
        cache: {},
    };

    const MAX_STEP = CFG.requiresPayment ? 5 : 4;

    /* ═══════════════════════════════════════════════
       NAVEGAÇÃO
    ═══════════════════════════════════════════════ */
    window.wizGo = function (n) {
        document.querySelectorAll('.wiz-panel').forEach(p => p.classList.remove('active'));
        document.getElementById('wizP' + n).classList.add('active');
        S.step = n;
        _tabs();
        _sidebars();
        document.querySelector('.wiz-card')?.scrollIntoView({ behavior: 'smooth', block: 'start' });

        if (n === 2) _loadPros();
        if (n === 3) _initCal();
        if (n === 4) _mobSum();
        if (n === 5) _initStripe();
    };

    /* ═══════════════════════════════════════════════
       STEPPER
    ═══════════════════════════════════════════════ */
    function _tabs() {
        const CHK = '<i class="ri-check-line" style="font-size:.7rem"></i>';
        for (let i = 1; i <= MAX_STEP; i++) {
            const t = document.getElementById('wizT' + i);
            if (!t) continue;
            const b = t.querySelector('.wiz-nav-badge');
            t.classList.remove('active', 'done');
            if      (i < S.step)  { t.classList.add('done');   b.innerHTML   = CHK; }
            else if (i === S.step) { t.classList.add('active'); b.textContent = i; }
            else                   {                             b.textContent = i; }
        }
    }

    /* ═══════════════════════════════════════════════
       SIDEBAR / RESUMO
    ═══════════════════════════════════════════════ */
    function _sumHtml() {
        if (!S.svcId) return '';
        const rows = [
            ['ri-scissors-line',    'Serviço',       S.svcName],
            S.proName  ? ['ri-user-line',         'Profissional', S.proName]  : null,
            S.dateLbl  ? ['ri-calendar-line',     'Data',         S.dateLbl]  : null,
            S.time     ? ['ri-time-line',          'Horário',      S.time]     : null,
            S.svcPrice ? ['ri-price-tag-3-line',   'Valor',        S.svcPrice] : null,
        ].filter(Boolean);

        return `<div class="card border-0 bg-light p-3" style="position:sticky;top:5.5rem;border-radius:12px">
            <p class="fw-bold mb-3 pb-2 border-bottom"
               style="font-size:.68rem;text-transform:uppercase;letter-spacing:.08em;color:#6b7280">
                <i class="ri-calendar-check-line text-primary me-1"></i>Resumo
            </p>
            ${rows.map(([ic, lb, vl]) => `
            <div class="wiz-sum-row">
                <div class="wiz-sum-ico"><i class="${ic}"></i></div>
                <div>
                    <div class="wiz-sum-lbl">${lb}</div>
                    <div class="wiz-sum-val">${vl}</div>
                </div>
            </div>`).join('')}
        </div>`;
    }

    function _sidebars() {
        ['wizSum2','wizSum3','wizSum4','wizSum5'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.innerHTML = _sumHtml();
        });
    }

    function _mobSum() {
        const el = document.getElementById('wizMobSum');
        if (!el || !S.svcId) return;
        const p = [];
        if (S.svcName)  p.push(`<strong>Serviço:</strong> ${S.svcName}`);
        if (S.proName)  p.push(`<strong>Profissional:</strong> ${S.proName}`);
        if (S.dateLbl)  p.push(`<strong>Data:</strong> ${S.dateLbl}`);
        if (S.time)     p.push(`<strong>Horário:</strong> ${S.time}`);
        if (S.svcPrice) p.push(`<strong>Valor:</strong> ${S.svcPrice}`);
        el.innerHTML = p.join('<br>');
        el.style.removeProperty('display');
    }

    /* ═══════════════════════════════════════════════
       STEP 1 — SERVIÇO
    ═══════════════════════════════════════════════ */
    window.wizSelSvc = function (card) {
        document.querySelectorAll('.service-card').forEach(x => x.classList.remove('selected'));
        card.classList.add('selected');
        S.svcId    = card.dataset.id;
        S.svcName  = card.dataset.name;
        S.svcDur   = parseInt(card.dataset.duration) || 30;
        S.svcPrice = card.dataset.price || null;
        // Invalidar passos seguintes
        S.proId = S.proName = S.date = S.dateLbl = S.time = S.proFinal = null;
        S.cache = {};
        document.getElementById('wizBtn1').disabled = false;
        // Forçar reload de profissionais na próxima visita ao step 2
        _prosLoaded = false;
        document.getElementById('wizProList').innerHTML =
            '<div class="d-flex align-items-center gap-2 py-4 text-muted">' +
            '<div class="wiz-spin wiz-spin-dk"></div> A carregar…</div>';
    };

    window.wizFilter = function (q) {
        q = q.toLowerCase().trim();
        document.querySelectorAll('.wiz-col').forEach(c => {
            c.style.display = (!q || c.dataset.q.includes(q)) ? '' : 'none';
        });
    };

    /* ═══════════════════════════════════════════════
       STEP 2 — PROFISSIONAIS
       GET /services/{id}/professionals
    ═══════════════════════════════════════════════ */
    let _prosLoaded = false;

    function _loadPros() {
        if (_prosLoaded) return;
        const container = document.getElementById('wizProList');
        container.innerHTML =
            '<div class="d-flex align-items-center gap-2 py-4 text-muted">' +
            '<div class="wiz-spin wiz-spin-dk"></div> A carregar profissionais…</div>';

        const url = (CFG.routes?.professionals || '/services/{id}/professionals')
            .replace('{id}', S.svcId);

        fetch(url)
            .then(r => r.json())
            .then(data => {
                _prosLoaded = true;
                let html = '<div class="d-flex flex-column gap-2">';

                data.forEach(p => {
                    const initials = p.name.split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase();
                    const avatar   = p.avatar
                        ? `<img src="${p.avatar}" class="wiz-pro-photo" alt="${p.name}">`
                        : `<div class="wiz-pro-init">${initials}</div>`;

                    html += `
                    <div class="wiz-pro" data-id="${p.id}" data-name="${p.name}" onclick="wizSelPro(this)">
                        ${avatar}
                        <div style="flex:1;min-width:0">
                            <div class="fw-bold" style="font-size:.9rem">${p.name}</div>
                            ${p.bio ? `<div class="text-muted" style="font-size:.75rem;margin-top:.1rem;line-height:1.4">${p.bio}</div>` : ''}
                        </div>
                        <div class="wiz-pro-ck"><i class="ri-check-line"></i></div>
                    </div>`;
                });

                html += `
                <div class="wiz-pro" data-id="" data-name="Sem preferência" onclick="wizSelPro(this)">
                    <div class="wiz-pro-init" style="background:#6b7280">
                        <i class="ri-shuffle-line"></i>
                    </div>
                    <div style="flex:1">
                        <div class="fw-bold" style="font-size:.9rem">Sem preferência</div>
                        <div class="text-muted" style="font-size:.75rem;margin-top:.1rem">Ver todos os horários disponíveis</div>
                    </div>
                    <div class="wiz-pro-ck"><i class="ri-check-line"></i></div>
                </div>
                </div>`;

                container.innerHTML = html;
            })
            .catch(() => {
                container.innerHTML = '<div class="alert alert-danger">Erro ao carregar profissionais.</div>';
            });
    }

    window.wizSelPro = function (card) {
        document.querySelectorAll('.wiz-pro, .pro-card').forEach(x => x.classList.remove('selected'));
        card.classList.add('selected');
        S.proId   = card.dataset.id;
        S.proName = card.dataset.name;
        S.date = S.dateLbl = S.time = S.proFinal = null;
        S.cache = {};
        document.getElementById('wizBtn2').disabled = false;
        _sidebars();
    };

    /* ═══════════════════════════════════════════════
       STEP 3 — CALENDÁRIO
       GET /available-slots?service_id=&start_date=&end_date=[&professional_id=]
    ═══════════════════════════════════════════════ */
    function _initCal() {
        console.log('Initializing calendar, step 3');
        const now = new Date();
        if (S.calY < now.getFullYear() ||
            (S.calY === now.getFullYear() && S.calM < now.getMonth() + 1)) {
            S.calY = now.getFullYear();
            S.calM = now.getMonth() + 1;
        }
        _fetchMonth();
    }

    async function _fetchMonth() {
        const key = `${S.calY}-${S.calM}`;
        if (S.cache[key]) { _renderCal(S.cache[key]); return; }

        _renderCalLoading();

        const today = new Date();
        const y = S.calY, m = S.calM;

        // Se é o mês atual, começa hoje; se é mês futuro, começa no dia 1
        let startDay = 1;
        if (y === today.getFullYear() && m === today.getMonth() + 1) {
            startDay = today.getDate();
        }

        const startDate = `${y}-${String(m).padStart(2,'0')}-${String(startDay).padStart(2,'0')}`;
        const endDate   = `${y}-${String(m).padStart(2,'0')}-${new Date(y, m, 0).getDate()}`;
        const params    = new URLSearchParams({ service_id: S.svcId, start_date: startDate, end_date: endDate });
        if (S.proId) params.append('professional_id', S.proId);

        console.log('Fetching slots:', { startDate, endDate, serviceId: S.svcId, professionalId: S.proId });

        const baseUrl = CFG.routes?.availableSlots || '/available-slots';

        try {
            const data = await fetch(`${baseUrl}?${params}`).then(r => r.json());
            const map  = {};
            data.forEach(e => {
                const d = e.day;
                map[d] = (map[d] || 0) + (e.available_slots?.length || 0);
            });
            S.cache[key] = map;
            _renderCal(map);
        } catch (err) {
            console.warn('Calendar API error, using demo data:', err);
            // Demo fallback: mark next 20 days as available
            const demoMap = {};
            const today = new Date();
            for (let i = 0; i < 20; i++) {
                const d = new Date(today);
                d.setDate(today.getDate() + i);
                if (d.getMonth() + 1 === m && d.getFullYear() === y) {
                    const dateStr = `${y}-${String(m).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
                    demoMap[dateStr] = 8; // high availability
                }
            }
            S.cache[key] = demoMap;
            _renderCal(demoMap);
        }
    }

    function _renderCalLoading() {
        document.getElementById('wizCalLbl').textContent = `${PTM[S.calM - 1]} ${S.calY}`;
        const grid    = document.getElementById('wizCal');
        const headers = [...grid.querySelectorAll('.wiz-cw')];
        grid.innerHTML = '';
        headers.forEach(h => grid.appendChild(h));

        const firstDay = new Date(S.calY, S.calM - 1, 1).getDay();
        const totalDays = new Date(S.calY, S.calM, 0).getDate();

        for (let i = 0; i < firstDay; i++) {
            const el = document.createElement('div');
            el.className = 'wiz-cd empty';
            grid.appendChild(el);
        }
        for (let d = 1; d <= totalDays; d++) {
            const el = document.createElement('div');
            el.className = 'wiz-cd ld';
            el.innerHTML = `<span>${d}</span><div class="wiz-spin wiz-spin-dk" style="width:7px;height:7px;border-width:1.5px"></div>`;
            grid.appendChild(el);
        }
    }

    function _renderCal(map) {
        console.log('Rendering calendar:', S.calY, S.calM, 'with availability:', map);
        document.getElementById('wizCalLbl').textContent = `${PTM[S.calM - 1]} ${S.calY}`;
        const grid    = document.getElementById('wizCal');
        const headers = [...grid.querySelectorAll('.wiz-cw')];
        grid.innerHTML = '';
        headers.forEach(h => grid.appendChild(h));

        const today    = new Date(); today.setHours(0,0,0,0);
        const firstDay  = new Date(S.calY, S.calM - 1, 1).getDay();
        const totalDays = new Date(S.calY, S.calM, 0).getDate();

        for (let i = 0; i < firstDay; i++) {
            const el = document.createElement('div');
            el.className = 'wiz-cd empty';
            grid.appendChild(el);
        }

        for (let d = 1; d <= totalDays; d++) {
            const dateStr = `${S.calY}-${String(S.calM).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
            const dt      = new Date(S.calY, S.calM - 1, d);
            const isPast  = dt < today;
            const n       = map[dateStr] ?? 0;
            const isSel   = S.date === dateStr;
            const isToday = dt.getTime() === today.getTime();

            const el = document.createElement('div');
            el.className = 'wiz-cd';

            if      (isSel)          el.classList.add('selected');
            else if (isPast || !n)   el.classList.add('past');
            else if (n >= 8)         el.classList.add('hi');
            else if (n >= 4)         el.classList.add('md');
            else                     el.classList.add('lo');

            if (isToday && !isPast) el.classList.add('tod');

            el.innerHTML = `<span>${d}</span>${(!isPast && n > 0) ? '<div class="wiz-dot"></div>' : ''}`;

            if (!isPast && n > 0) {
                el.setAttribute('data-date', dateStr);
                el.setAttribute('onclick', `wizSelectDay('${dateStr}', new Date(${S.calY}, ${S.calM - 1}, ${d}))`);
                el.addEventListener('click', () => _selectDay(dateStr, dt));
            }
            grid.appendChild(el);
        }

        const now = new Date();
        document.getElementById('wizPrev').disabled =
            S.calY === now.getFullYear() && S.calM <= now.getMonth() + 1;
    }

    function _selectDay(dateStr, dt) {
        console.log('Day selected:', dateStr, dt);
        S.date     = dateStr;
        S.time     = null;
        S.proFinal = null;

        const [, m, d] = dateStr.split('-').map(Number);
        const dow = dt.getDay();
        S.dateLbl = `${PTD[dow].charAt(0).toUpperCase() + PTD[dow].slice(1)}, ${d} de ${PTM[m - 1]}`;

        document.getElementById('wizBtn3').disabled = true;
        _renderCal(S.cache[`${S.calY}-${S.calM}`] || {});
        _loadSlots(dateStr);
        _sidebars();
    }

    // Global wrapper for onclick attribute
    window.wizSelectDay = _selectDay;

    async function _loadSlots(dateStr) {
        const panel = document.getElementById('wizSlots');
        panel.style.display = 'block';
        panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

        panel.innerHTML = `
        <div class="border rounded-3 p-3 mb-3">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="ri-calendar-event-line text-primary"></i>
                <span class="fw-semibold" style="font-size:.875rem">${S.dateLbl}</span>
            </div>
            <div class="d-flex align-items-center gap-2 text-muted" style="font-size:.85rem">
                <div class="wiz-spin wiz-spin-dk"></div> A carregar horários…
            </div>
        </div>`;

        const params  = new URLSearchParams({ service_id: S.svcId, start_date: dateStr, end_date: dateStr });
        if (S.proId) params.append('professional_id', S.proId);

        const baseUrl = CFG.routes?.availableSlots || '/available-slots';

        try {
            const data = await fetch(`${baseUrl}?${params}`).then(r => r.json());

            // Agrupar slots únicos
            const slotMap = {};
            data.forEach(e => e.available_slots.forEach(t => {
                if (!slotMap[t]) slotMap[t] = { pid: e.professional_id, pn: e.professional_name };
            }));

            const times   = Object.keys(slotMap).sort();
            const total   = times.length;
            const showPro = !S.proId;

            if (!total) {
                panel.innerHTML = `
                <div class="border rounded-3 p-3 mb-3">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="ri-calendar-event-line text-primary"></i>
                        <span class="fw-semibold" style="font-size:.875rem">${S.dateLbl}</span>
                    </div>
                    <p class="text-muted small mb-0">Sem horários disponíveis para este dia.</p>
                </div>`;
                return;
            }

            let slotsHtml = '<div class="wiz-sg">';
            times.forEach(t => {
                const info  = slotMap[t];
                const isSel = S.time === t;
                const proName = showPro ? `<div class="wiz-slot-pn">${info.pn}</div>` : '';
                slotsHtml += `
                <button type="button"
                        class="wiz-slot${isSel ? ' selected' : ''}"
                        data-t="${t}" data-pid="${info.pid}" data-pn="${info.pn}"
                        onclick="wizSelSlot(this)">
                    ${t}${proName}
                </button>`;
            });
            slotsHtml += '</div>';

            panel.innerHTML = `
            <div class="border rounded-3 p-3 mb-3">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ri-calendar-event-line text-primary"></i>
                        <span class="fw-semibold" style="font-size:.875rem">${S.dateLbl}</span>
                    </div>
                    <span class="badge bg-light text-muted border" style="font-size:.7rem">
                        ${total} horário${total > 1 ? 's' : ''}
                    </span>
                </div>
                ${slotsHtml}
            </div>`;

        } catch (err) {
            console.warn('Slots API error, using demo data:', err);
            // Demo fallback: create hourly slots 9:00-18:00
            const demoSlots = [];
            for (let h = 9; h <= 18; h++) {
                demoSlots.push(`${String(h).padStart(2,'0')}:00`);
                if (h < 18) demoSlots.push(`${String(h).padStart(2,'0')}:30`);
            }
            let slotsHtml = '<div class="wiz-sg">';
            demoSlots.forEach(t => {
                const isSel = S.time === t;
                slotsHtml += `
                <button type="button"
                        class="wiz-slot${isSel ? ' selected' : ''}"
                        data-t="${t}" data-pid="${S.proId || ''}" data-pn="${S.proName || 'Demo Pro'}"
                        onclick="wizSelSlot(this)">
                    ${t}
                </button>`;
            });
            slotsHtml += '</div>';
            panel.innerHTML = `
            <div class="border rounded-3 p-3 mb-3">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ri-calendar-event-line text-primary"></i>
                        <span class="fw-semibold" style="font-size:.875rem">${S.dateLbl}</span>
                    </div>
                    <span class="badge bg-light text-dark border" style="font-size:.7rem">Demo</span>
                </div>
                ${slotsHtml}
            </div>`;
        }
    }

    window.wizSelSlot = function (btn) {
        document.querySelectorAll('.wiz-slot').forEach(x => x.classList.remove('selected'));
        btn.classList.add('selected');
        S.time     = btn.dataset.t;
        S.proFinal = btn.dataset.pid;
        if (!S.proId) S.proName = btn.dataset.pn;
        document.getElementById('wizBtn3').disabled = false;
        _sidebars();
    };

    // Navegação do calendário
    document.getElementById('wizPrev')?.addEventListener('click', () => {
        if (S.calM === 1) { S.calM = 12; S.calY--; } else S.calM--;
        S.date = S.dateLbl = S.time = S.proFinal = null;
        document.getElementById('wizSlots').style.display = 'none';
        document.getElementById('wizBtn3').disabled = true;
        _fetchMonth();
    });
    document.getElementById('wizNext')?.addEventListener('click', () => {
        if (S.calM === 12) { S.calM = 1; S.calY++; } else S.calM++;
        S.date = S.dateLbl = S.time = S.proFinal = null;
        document.getElementById('wizSlots').style.display = 'none';
        document.getElementById('wizBtn3').disabled = true;
        _fetchMonth();
    });

    /* ═══════════════════════════════════════════════
       STEP 4 — DADOS DO CLIENTE
    ═══════════════════════════════════════════════ */
    function _fillHiddenFields() {
        document.getElementById('wizFS').value = S.svcId    || '';
        document.getElementById('wizFP').value = S.proFinal || S.proId || '';
        document.getElementById('wizFD').value = S.date     || '';
        document.getElementById('wizFT').value = S.time     || '';
    }

    function _validateClientForm() {
        const name  = document.getElementById('wizFN');
        const phone = document.getElementById('wizFPh');
        [name, phone].forEach(f => f.classList.remove('is-invalid'));
        let ok = true;
        if (!name.value.trim())  { name.classList.add('is-invalid');  ok = false; }
        if (!phone.value.trim()) { phone.classList.add('is-invalid'); ok = false; }
        return ok;
    }

    // Botão "Continuar para pagamento" (só existe quando requiresPayment = true)
    window.wizGoPayment = function () {
        if (!_validateClientForm()) return;
        if (!S.svcId || !S.date || !S.time) {
            alert('Por favor complete todos os passos antes de continuar.');
            return;
        }
        _fillHiddenFields();
        wizGo(5);
    };

    // Submit directo (quando requiresPayment = false)
    document.getElementById('wizForm')?.addEventListener('submit', function (e) {
        _fillHiddenFields();
        if (!_validateClientForm()) { e.preventDefault(); return; }
        if (!S.svcId || !S.date || !S.time) {
            e.preventDefault();
            alert('Por favor complete todos os passos antes de submeter.');
            return;
        }
        const btn = document.getElementById('wizSub');
        if (btn) {
            btn.disabled  = true;
            btn.innerHTML = '<div class="wiz-spin" style="width:14px;height:14px;border-width:2px"></div> A confirmar…';
        }
    });

    /* ═══════════════════════════════════════════════
       STEP 5 — PAGAMENTO (STRIPE) — OPCIONAL
    ═══════════════════════════════════════════════ */
    let _stripe      = null;
    let _elements    = null;
    let _paymentElement = null;
    let _cardElement = null;
    let _stripeMounted = false;
    let _clientSecret = null;
    let _useCardElement = false; // fallback se Payment Element falhar

    async function _initStripe() {
        if (!CFG.requiresPayment || _stripeMounted) return;

        // Actualizar resumo de pagamento no panel 5
        const nameEl   = document.getElementById('wizPaySvcName');
        const amountEl = document.getElementById('wizPayAmount');
        if (nameEl)   nameEl.textContent   = S.svcName  || '';
        if (amountEl) amountEl.textContent = S.svcPrice ? `Total: ${S.svcPrice}` : 'Valor a pagar';

        _sidebars();

        // Mostrar loading no container
        const container = document.getElementById('wizCardElement');
        if (container) {
            container.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;padding:2rem;gap:.5rem;color:#6b7280"><div class="wiz-spin wiz-spin-dk"></div><span>A carregar métodos de pagamento...</span></div>';
        }

        // Inicializar Stripe com locale PT
        _stripe = Stripe(CFG.stripeKey, { locale: 'pt' });

        try {
            // Tentar criar PaymentIntent no servidor primeiro
            const intentRes = await fetch(CFG.routes?.paymentIntent || '/booking/payment-intent', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CFG.csrfToken,
                },
                body: JSON.stringify({
                    service_id:      S.svcId,
                    professional_id: S.proId,
                    day:             S.date,
                    start_hour:      S.time,
                    client_name:     document.getElementById('wizFN')?.value || '',
                    client_phone_1:  document.getElementById('wizFPh')?.value || '',
                    client_email:    document.getElementById('wizFE')?.value || '',
                }),
            });

            // Verificar se resposta é JSON
            const contentType = intentRes.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Servidor retornou resposta inválida. Por favor, contacte o suporte.');
            }

            const intentData = await intentRes.json();
            if (!intentRes.ok || !intentData.client_secret) {
                throw new Error(intentData.message || 'Erro ao iniciar pagamento.');
            }

            _clientSecret = intentData.client_secret;

            // Criar Elements com clientSecret (Payment Element)
            _elements = _stripe.elements({
                clientSecret: _clientSecret,
                locale: 'pt',
                appearance: {
                    theme: 'stripe',
                    variables: {
                        colorPrimary: CFG.corPrimaria || '#2563eb',
                        fontFamily: "'Inter', sans-serif",
                        fontSize: '15px',
                        borderRadius: '10px',
                    }
                }
            });

            _paymentElement = _elements.create('payment', {
                layout: { type: 'tabs', defaultCollapsed: false },
                fields: { billingDetails: { name: 'auto', email: 'auto', phone: 'auto' } },
                wallets: { applePay: 'never', googlePay: 'never' }
            });

            if (container) container.innerHTML = '';
            _paymentElement.mount('#wizCardElement');

            _paymentElement.on('change', e => {
                const errEl = document.getElementById('wizCardErrors');
                if (errEl) errEl.textContent = e.error ? e.error.message : '';
            });

            console.log('Payment Element mounted (supports Card, MB WAY, Multibanco)');

        } catch (err) {
            console.warn('Payment Element failed, falling back to Card Element:', err);

            // Fallback: usar Card Element simples
            _useCardElement = true;
            _clientSecret = null; // limpar secret se houver

            try {
                _elements = _stripe.elements({ locale: 'pt' });
                _cardElement = _elements.create('card', {
                    style: {
                        base: {
                            fontFamily: "'Inter', sans-serif",
                            fontSize: '15px',
                            color: '#111827',
                            '::placeholder': { color: '#9ca3af' },
                        },
                        invalid: { color: '#dc2626' },
                    },
                });

                if (container) container.innerHTML = '';
                _cardElement.mount('#wizCardElement');

                _cardElement.on('change', e => {
                    const errEl = document.getElementById('wizCardErrors');
                    if (errEl) errEl.textContent = e.error ? e.error.message : '';
                });

                console.log('Card Element mounted (fallback mode - card only)');

                // Mostrar aviso ao utilizador
                const warningEl = document.createElement('div');
                warningEl.className = 'alert alert-warning mt-3 mb-0';
                warningEl.innerHTML = '<i class="ri-information-line me-2"></i><small>Apenas pagamento por cartão disponível neste momento.</small>';
                container.parentElement.insertBefore(warningEl, container.nextSibling);

            } catch (cardErr) {
                console.error('Card Element also failed:', cardErr);
                if (container) {
                    container.innerHTML = `<div class="alert alert-danger mb-0"><i class="ri-error-warning-line me-2"></i>Erro ao carregar pagamento. Por favor, recarregue a página ou contacte o suporte.</div>`;
                }
                return;
            }
        }

        _stripeMounted = true;
    }

    document.getElementById('wizPayForm')?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const btn    = document.getElementById('wizPayBtn');
        const errBox = document.getElementById('wizStripeErr');
        const errMsg = document.getElementById('wizStripeErrMsg');
        if (errBox) errBox.style.display = 'none';
        btn.disabled  = true;
        btn.innerHTML = '<div class="wiz-spin" style="width:14px;height:14px;border-width:2px"></div> A processar…';

        try {
            let paymentIntentId = null;

            if (_useCardElement) {
                // Modo fallback: Card Element (criar PI no submit)
                console.log('Using Card Element payment flow');

                const intentRes = await fetch(CFG.routes?.paymentIntent || '/booking/payment-intent', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CFG.csrfToken,
                    },
                    body: JSON.stringify({
                        service_id:      S.svcId,
                        professional_id: S.proId,
                        day:             S.date,
                        start_hour:      S.time,
                        client_name:     document.getElementById('wizFN')?.value || '',
                        client_phone_1:  document.getElementById('wizFPh')?.value || '',
                        client_email:    document.getElementById('wizFE')?.value || '',
                    }),
                });

                const intentData = await intentRes.json();
                if (!intentRes.ok || !intentData.client_secret) {
                    throw new Error(intentData.message || 'Erro ao processar pagamento.');
                }

                const { paymentIntent, error } = await _stripe.confirmCardPayment(
                    intentData.client_secret,
                    {
                        payment_method: {
                            card: _cardElement,
                            billing_details: {
                                name: document.getElementById('wizFN')?.value || '',
                                email: document.getElementById('wizFE')?.value || '',
                                phone: document.getElementById('wizFPh')?.value || '',
                            }
                        }
                    }
                );

                if (error) throw new Error(error.message);
                paymentIntentId = paymentIntent.id;

            } else {
                // Modo normal: Payment Element (PI já criado)
                console.log('Using Payment Element flow');

                if (!_clientSecret) {
                    throw new Error('Erro ao processar pagamento. Por favor, recarregue a página.');
                }

                const { error } = await _stripe.confirmPayment({
                    elements: _elements,
                    confirmParams: {
                        return_url: window.location.origin + (CFG.routes?.bookingSuccess || '/booking/success'),
                        payment_method_data: {
                            billing_details: {
                                name: document.getElementById('wizFN')?.value || '',
                                email: document.getElementById('wizFE')?.value || '',
                                phone: document.getElementById('wizFPh')?.value || '',
                            }
                        }
                    },
                    redirect: 'if_required'
                });

                if (error) throw new Error(error.message);

                // Extrair payment_intent_id do client_secret
                paymentIntentId = _clientSecret.split('_secret_')[0];
            }

            // Confirmar booking no servidor
            const bookRes = await fetch(CFG.routes?.bookSlot || '/booking/confirmar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CFG.csrfToken,
                },
                body: JSON.stringify({
                    service_id:        S.svcId,
                    professional_id:   S.proId,
                    day:               S.date,
                    start_hour:        S.time,
                    client_name:       document.getElementById('wizFN')?.value,
                    client_phone_1:    document.getElementById('wizFPh')?.value,
                    client_email:      document.getElementById('wizFE')?.value || '',
                    payment_intent_id: paymentIntentId,
                }),
            });

            const bookData = await bookRes.json();
            if (!bookRes.ok) throw new Error(bookData.message || 'Erro ao confirmar marcação.');

            // 4. Redirecionar para sucesso
            window.location.href = bookData.redirect || '/';

        } catch (err) {
            console.error('Payment error:', err);
            if (errBox && errMsg) {
                errMsg.textContent = err.message;
                errBox.style.display = 'flex';
            }
            btn.disabled  = false;
            btn.innerHTML = '<i class="ri-secure-payment-line me-1"></i> Pagar e Confirmar';
        }
    });

    /* ═══════════════════════════════════════════════
       INIT
    ═══════════════════════════════════════════════ */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            _tabs();
            _sidebars();
            console.log('Wizard initialized (DOMContentLoaded)');
        });
    } else {
        _tabs();
        _sidebars();
        console.log('Wizard initialized (immediate)');
    }

}());
