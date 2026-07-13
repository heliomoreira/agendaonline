@extends('layouts.app')
@section('content')
    @if (session('success'))
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-solid-success alert-dismissible fade show d-flex align-items-center"
                     role="alert">
            <span class="alert-icon rounded me-2">
                <i class="icon-base ti tabler-check icon-md"></i>
            </span>
                    <div class="flex-grow-1">
                        {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close ms-2" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            </div>
        </div>
    @endif
    @if ($errors->any())
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-solid-danger d-flex align-items-center" role="alert">
            <span class="alert-icon rounded">
              <i class="icon-base ti tabler-x"></i>
            </span>
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
                <i class="icon-base ti tabler-users"></i>
                {{ $client->id ? 'Editar Cliente' : 'Novo Cliente' }}
            </h5>
            <hr class="my-2"/>
        </div>
    </div>
    <div class="row g-6">
        <div class="col-md-12">
            @if(!$client->id)
                {{ html()->modelForm($client, 'POST', route('clients.store'))->open() }}
            @else
                {{ html()->modelForm($client, 'PUT', route('clients.update', $client->id))->open() }}
            @endif
            {{ html()->token() }}
            <div class="card">
                <div class="card-header header-elements">
                    <h5 class="mb-0 me-2">Detalhe de
                        Cliente {!!  $client->name ? '| <span style="color:#2A7AD4">' . $client->name . '</span>': ''  !!}</h5>
                </div>
                <div class="card-body">

                    {{-- ===== Dados Pessoais ===== --}}
                    <div class="row g-6">
                        <div class="col-12">
                            <h6 class="text-muted fw-semibold mb-0">Dados Pessoais</h6>
                            <hr class="mt-2 mb-0">
                        </div>

                        <div class="col-md-5">
                            <label class="form-label" for="name">Nome</label>
                            {{ html()->text('name')->id('name')->class('form-control')->placeholder('') }}
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="email">Email</label>
                            {{ html()->text('email')->id('email')->class('form-control')->placeholder('') }}
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="birthdate">Data de Nascimento</label>
                            {{ html()->date('birthdate')->id('birthdate')->class('form-control')->placeholder('') }}
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="phone_1">Contacto</label>
                            <div class="input-group">
                                <span class="input-group-text">+</span>
                                {{ html()->text('phone_1_country_code')->id('phone_1_country_code')->class('form-control')->attribute('style', 'max-width: 72px')->value(old('phone_1_country_code', $client?->phone_1_country_code ?? '351')) }}
                                {{ html()->text('phone_1')->id('phone_1')->class('form-control')->placeholder('') }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="phone_2">Contacto 2</label>
                            <div class="input-group">
                                <span class="input-group-text">+</span>
                                {{ html()->text('phone_2_country_code')->id('phone_2_country_code')->class('form-control')->attribute('style', 'max-width: 72px')->value(old('phone_2_country_code', $client?->phone_2_country_code ?? '351')) }}
                                {{ html()->text('phone_2')->id('phone_2')->class('form-control')->placeholder('') }}
                            </div>
                        </div>
                    </div>

                    {{-- ===== Morada ===== --}}
                    <div class="row g-6 mt-1">
                        <div class="col-12">
                            <h6 class="text-muted fw-semibold mb-0">Morada</h6>
                            <hr class="mt-2 mb-0">
                        </div>

                        <div class="col-md-5">
                            <label class="form-label" for="address">Morada</label>
                            {{ html()->text('address')->id('address')->class('form-control')->placeholder('') }}
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="number_port">Nº / Porta</label>
                            {{ html()->text('number_port')->id('number_port')->class('form-control')->placeholder('') }}
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="zip_code">Cód. Postal</label>
                            {{ html()->text('zip_code')->id('zip_code')->class('form-control')->placeholder('') }}
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="city">Localidade</label>
                            {{ html()->text('city')->id('city')->class('form-control')->placeholder('') }}
                        </div>
                    </div>

                    {{-- ===== Menor de idade ===== --}}
                    <div class="row g-6 mt-1">
                        <div class="col-12">
                            <h6 class="text-muted fw-semibold mb-0">Menor de Idade</h6>
                            <hr class="mt-2 mb-2">
                            <input type="hidden" name="is_minor" value="0">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_minor" name="is_minor" value="1"
                                    {{ old('is_minor', $client?->is_minor ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_minor">Cliente é menor de idade</label>
                            </div>
                        </div>
                    </div>

                    {{-- Dados do encarregado (visível só para menores) --}}
                    <div id="minor-fields" class="row g-6 mt-1" style="display:none;">
                        <div class="col-12">
                            <h6 class="text-muted mb-0">Dados do Encarregado</h6>
                            <hr class="mt-2 mb-0">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="parent_name">Nome do encarregado</label>
                            {{ html()->text('parent_name')->id('parent_name')->class('form-control' . ($errors->has('parent_name') ? ' is-invalid' : '')) }}
                            @error('parent_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="parent_phone_1">Telefone do encarregado</label>
                            <div class="input-group @error('parent_phone_1') is-invalid @enderror">
                                <span class="input-group-text">+</span>
                                {{ html()->text('parent_phone_1_country_code')->id('parent_phone_1_country_code')->class('form-control')->attribute('style', 'max-width: 72px')->value(old('parent_phone_1_country_code', $client?->parent_phone_1_country_code ?? '351')) }}
                                {{ html()->text('parent_phone_1')->id('parent_phone_1')->class('form-control' . ($errors->has('parent_phone_1') ? ' is-invalid' : '')) }}
                            </div>
                            @error('parent_phone_1')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="parent_email">Email do encarregado</label>
                            {{ html()->email('parent_email')->id('parent_email')->class('form-control' . ($errors->has('parent_email') ? ' is-invalid' : '')) }}
                            @error('parent_email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="parent_notes">Notas</label>
                            {{ html()->textarea('parent_notes')->id('parent_notes')->rows(2)->class('form-control') }}
                        </div>

                        <div class="col-12">
                            <input type="hidden" name="send_sms_to_parent" value="0">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="send_sms_to_parent" name="send_sms_to_parent" value="1"
                                    {{ old('send_sms_to_parent', $client?->send_sms_to_parent ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="send_sms_to_parent">Enviar SMS também ao encarregado</label>
                            </div>
                        </div>

                        <div class="col-md-6" id="parent-template-wrap">
                            <label class="form-label" for="parent_sms_template_id">Template de SMS (encarregado)</label>
                            {{ html()->select('parent_sms_template_id', $templates)->id('parent_sms_template_id')->placeholder('— Sem template (usa texto padrão) —')->class('form-select') }}
                        </div>
                    </div>

                    {{-- ===== Preferências ===== --}}
                    <div class="row g-6 mt-1">
                        <div class="col-12">
                            <h6 class="text-muted fw-semibold mb-0">Preferências</h6>
                            <hr class="mt-2 mb-2">
                            <input type="hidden" name="marketing_allowed" value="0">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="marketing_allowed" name="marketing_allowed" value="1"
                                    {{ old('marketing_allowed', $client?->marketing_allowed ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="marketing_allowed">Permite comunicações de marketing</label>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                            <i class="icon-base ti tabler-device-floppy"></i> Gravar
                        </button>
                        <a href="{{ route('clients.index') }}" class="btn btn-secondary waves-effect waves-light">
                            <i class="icon-base ti tabler-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
            </div>
            {{ html()->closeModelForm() }}
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        (function () {
            const minorToggle = document.getElementById('is_minor');
            const minorBlock  = document.getElementById('minor-fields');
            const smsToggle   = document.getElementById('send_sms_to_parent');
            const templateWrap = document.getElementById('parent-template-wrap');

            if (minorToggle && minorBlock) {
                const syncMinor = () => minorBlock.style.display = minorToggle.checked ? 'flex' : 'none';
                minorToggle.addEventListener('change', syncMinor);
                syncMinor();
            }

            // O template do encarregado só faz sentido se o envio de SMS estiver ativo
            if (smsToggle && templateWrap) {
                const syncTemplate = () => templateWrap.style.display = smsToggle.checked ? 'block' : 'none';
                smsToggle.addEventListener('change', syncTemplate);
                syncTemplate();
            }
        })();
    </script>
@endpush
