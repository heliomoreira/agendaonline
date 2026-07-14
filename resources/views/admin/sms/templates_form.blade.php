@extends('layouts.app')
@section('title', 'SMS | Template')
@section('content')
    @include('admin._partials.alerts')
    <div class="row mb-2">
        <div class="col-md-12">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="d-flex align-items-center gap-2 m-0">
                    <i class="icon-base ti tabler-settings"></i>
                    Templates SMS
                </h5>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="/admin">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('sms.index') }}">SMS</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Templates</li>
                    </ol>
                </nav>
            </div>
            <hr class="my-2"/>
        </div>
    </div>

    <div class="row g-6">
        <div class="col-md-8">
            <div class="card">
                @if(!$template->id)
                    {{ html()->modelForm($template, 'POST', route('sms.templates.store'))->open() }}
                @else
                    {{ html()->modelForm($template, 'PUT', route('sms.templates.update', $template->id))->open() }}
                @endif
                {{ html()->token() }}

                <div class="card-header header-elements">
                    <h5 class="mb-0 me-2">
                        Detalhe
                        @if($template->title)
                            | <span style="color:#2A7AD4">{{ $template->title }}</span>
                        @endif
                    </h5></div>

                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label" for="title">{{ __('forms.title') }}</label>
                            {{ html()->text('title')->id('title')->class('form-control')->placeholder(__('forms.title')) }}
                        </div>
                    </div>

                    <div class="row g-4 mt-1">
                        <div class="col-md-7">
                            <label class="form-label" for="sms-text">{{ __('forms.description') }}</label>
                            {{ html()->textarea('body')->id('sms-text')->class('form-control')->rows(10)->placeholder(__('forms.description')) }}
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">{{ __('forms.info') }}</label>
                            <ul id="sms-counter" class="list-unstyled alert alert-warning mb-0 p-5">
                                <li>Codificação: <span class="encoding"></span></li>
                                <li>Tamanho: <span class="length"></span></li>
                                <li>Mensagens: <span class="messages"></span></li>
                                <li>Por Mensagem: <span class="per_message"></span></li>
                                <li>Restantes: <span class="remaining"></span></li>
                            </ul>
                        </div>
                    </div>

                    <div class="row g-4 mt-1">
                        <div class="col-md-2">
                            <label class="form-label" for="order">{{ __('forms.order') }}</label>
                            {{ html()->text('order')->id('order')->class('form-control')->placeholder(__('forms.order')) }}
                        </div>
                    </div>

                    <div class="row g-4 mt-1">
                        <div class="col-md-4">
                            <label class="form-label d-block">Estado</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch"
                                       id="status" name="status" value="1"
                                    {{ $template->status == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="status">Ativo</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                            <i class="icon-base ti tabler-device-floppy"></i> Guardar
                        </button>
                        <a href="{{ route('sms.templates.list') }}" class="btn btn-secondary waves-effect waves-light">
                            <i class="icon-base ti tabler-arrow-left"></i> Cancelar / Voltar
                        </a>
                    </div>
                </div>

                {{ html()->closeModelForm() }}
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="icon-base ti tabler-bulb me-1 text-warning"></i> Códigos disponíveis
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-body-secondary small mb-3">
                        Clique num código para o copiar. No envio, é substituído pelo valor real de cada cliente.
                    </p>

                    <div class="d-flex flex-column gap-2">
                        @foreach([
                            '[NOME_CLIENTE]' => 'Nome do cliente',
                            '[DATA]'         => 'Data da marcação',
                            '[DIA_SEMANA]'   => 'Dia da semana',
                            '[HORA]'         => 'Hora da marcação',
                            '[SERVICO]'      => 'Serviço agendado',
                        ] as $code => $label)
                            <button type="button"
                                    class="btn btn-label-primary d-flex align-items-center justify-content-between text-start p-2 copy-code"
                                    data-code="{{ $code }}">
                        <span>
                            <code class="text-primary">{{ $code }}</code>
                            <small class="text-body-secondary d-block mt-1">{{ $label }}</small>
                        </span>
                                <i class="icon-base ti tabler-copy"></i>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ global_asset('assets/js/sms_counter.js') }}"></script>
    <script>
        $(function () {
            $('#sms-text').countSms('#sms-counter');
        });

        function copyPlaceholder(el) {
            const text = el.textContent.trim();
            const done = () => {
                const info = document.getElementById('text-info');
                info.classList.remove('d-none');
                setTimeout(() => info.classList.add('d-none'), 2000);
            };

            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(done);
            } else {
                const inp = document.createElement('input');
                document.body.appendChild(inp);
                inp.value = text;
                inp.select();
                document.execCommand('copy');
                inp.remove();
                done();
            }
        }
    </script>
    <script>
        document.querySelectorAll('.copy-code').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const code = btn.dataset.code;
                const textarea = document.getElementById('sms-text');

                // Insere o código na posição do cursor do textarea
                if (textarea) {
                    const start = textarea.selectionStart;
                    const end = textarea.selectionEnd;
                    textarea.value = textarea.value.slice(0, start) + code + textarea.value.slice(end);
                    textarea.focus();
                    textarea.selectionStart = textarea.selectionEnd = start + code.length;
                    textarea.dispatchEvent(new Event('input')); // atualiza o contador de SMS
                }

                // Copia também para a área de transferência
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(code);
                }

                // Feedback visual no próprio botão
                const icon = btn.querySelector('i');
                const original = icon.className;
                icon.className = 'icon-base ti tabler-check text-success';
                setTimeout(() => {
                    icon.className = original;
                }, 1500);
            });
        });
    </script>
@endpush
