@php($tipos = \App\Models\BetaSignup::TIPOS)
@php($profissionais = \App\Models\BetaSignup::PROFISSIONAIS)
<form class="card form beta-form" id="inscricao-beta" action="{{ route('website.beta') }}" method="post" data-ajax data-error="Não foi possível concluir a inscrição. Escreva-nos para agendaonline@bitsis.pt.">
    @csrf
    <h3>Inscreva-se na versão beta</h3>
    <p>Deixe os seus dados e enviamos-lhe um email assim que puder criar a sua conta.</p>

    <label>Nome<input type="text" name="nome" value="{{ old('nome') }}" autocomplete="name" maxlength="120" required /></label>
    <label>Email<input type="email" name="email" value="{{ old('email') }}" autocomplete="email" maxlength="190" required /></label>
    <label>Nome do negócio <span class="opt">(opcional)</span><input type="text" name="negocio" value="{{ old('negocio') }}" autocomplete="organization" maxlength="150" /></label>

    <label>Tipo de negócio
        <select name="tipo_negocio" required>
            <option value="" disabled @selected(! old('tipo_negocio'))>Escolha uma opção</option>
            @foreach ($tipos as $valor => $rotulo)
                <option value="{{ $valor }}" @selected(old('tipo_negocio') === $valor)>{{ $rotulo }}</option>
            @endforeach
        </select>
    </label>

    <label>Quantos profissionais marcam serviços? <span class="opt">(opcional)</span>
        <select name="profissionais">
            <option value="">Escolha uma opção</option>
            @foreach ($profissionais as $valor => $rotulo)
                <option value="{{ $valor }}" @selected(old('profissionais') === $valor)>{{ $rotulo }}</option>
            @endforeach
        </select>
    </label>

    <label class="hp" aria-hidden="true">Não preencher<input type="text" name="website" tabindex="-1" autocomplete="off" /></label>
    <label class="consent"><input type="checkbox" name="consentimento" required /><span>Aceito que os meus dados sejam usados para me contactarem sobre a beta, nos termos da <a href="/politica-de-privacidade">Política de Privacidade</a>.</span></label>

    <button class="btn btn-primary" type="submit">Inscrever na beta</button>

    @if (session('beta_status'))
        <p class="form-msg ok" role="status" aria-live="polite">{{ session('beta_status') }}</p>
    @elseif ($errors->beta->any())
        <p class="form-msg err" role="status" aria-live="polite">{{ $errors->beta->first() }}</p>
    @else
        <p class="form-msg" role="status" aria-live="polite"></p>
    @endif
</form>
