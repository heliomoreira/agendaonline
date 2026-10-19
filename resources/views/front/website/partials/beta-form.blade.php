@php($businessTypes = \App\Models\BetaSignup::BUSINESS_TYPES)
@php($teamSizes = \App\Models\BetaSignup::TEAM_SIZES)
<form class="card form beta-form" id="acesso-antecipado" action="{{ route('website.beta', [], false) }}" method="post" data-ajax data-error="Não foi possível enviar o pedido. Escreva-nos para agendaonline@bitsis.pt.">
    @csrf
    <h3>Peça acesso antecipado</h3>
    <p>Deixe os seus dados e enviamos-lhe um email assim que puder criar a sua conta.</p>

    <label>Nome<input type="text" name="name" value="{{ old('name') }}" autocomplete="name" maxlength="120" required /></label>
    <label>Email<input type="email" name="email" value="{{ old('email') }}" autocomplete="email" maxlength="190" required /></label>
    <label>Nome do negócio <span class="opt">(opcional)</span><input type="text" name="business" value="{{ old('business') }}" autocomplete="organization" maxlength="150" /></label>

    <label>Tipo de negócio
        <select name="business_type" required>
            <option value="" disabled @selected(! old('business_type'))>Escolha uma opção</option>
            @foreach ($businessTypes as $value => $label)
                <option value="{{ $value }}" @selected(old('business_type') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </label>

    <label>Quantos profissionais marcam serviços? <span class="opt">(opcional)</span>
        <select name="professionals">
            <option value="">Escolha uma opção</option>
            @foreach ($teamSizes as $value => $label)
                <option value="{{ $value }}" @selected(old('professionals') === (string) $value)>{{ $label }}</option>
            @endforeach
        </select>
    </label>

    <label class="hp" aria-hidden="true">Não preencher<input type="text" name="website" tabindex="-1" autocomplete="off" /></label>
    <label class="consent"><input type="checkbox" name="consent" required /><span>Aceito que os meus dados sejam usados para me contactarem sobre a beta, nos termos da <a href="/politica-de-privacidade">Política de Privacidade</a>.</span></label>

    <button class="btn btn-primary" type="submit">Pedir acesso</button>

    @if (session('beta_status'))
        <p class="form-msg ok" role="status" aria-live="polite">{{ session('beta_status') }}</p>
    @elseif ($errors->beta->any())
        <p class="form-msg err" role="status" aria-live="polite">{{ $errors->beta->first() }}</p>
    @else
        <p class="form-msg" role="status" aria-live="polite"></p>
    @endif
</form>
