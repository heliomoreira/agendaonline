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
    <div class="row g-6">
        <div class="col-md-6">
            @if(!$professional->id)
                {{ html()->modelForm($professional, 'POST', route('professionals.store'))->open() }}
            @else
                {{ html()->modelForm($professional, 'PUT', route('professionals.update', $professional->id))->open() }}
            @endif
            {{ html()->token() }}
            <div class="card">
                <div class="card-header header-elements">
                    <h5 class="mb-0 me-2">Detalhe {!!  $professional->name ? '| <span style="color:#2A7AD4">' . $professional->name . '</span>': ''  !!}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-6">
                        <div class="col-md-8">
                            <label class="form-label" for="name">Nome</label>
                            {{html()->text('name')->id('name')->class('form-control')->placeholder('')}}
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="phone_1">Contacto</label>
                            {{html()->text('phone_1')->id('phone_1')->class('form-control')->placeholder('')}}
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-8">
                            <label class="form-label" for="email">Email</label>
                            {{html()->text('email')->id('email')->class('form-control')->placeholder('')}}
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="phone_2">Contacto 2</label>
                            {{html()->text('phone_2')->id('phone_2')->class('form-control')->placeholder('')}}
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <label class="form-label" for="status">Ordem</label>
                            {{html()->text('order')->id('order')->class('form-control')}}
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="status">Estado</label>
                            {{html()->select('status')->id('status')->options([true => 'Activo', false => 'Inactivo'])->class('form-select')->placeholder('-- Seleccionar --')}}
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                            <i class="icon-base ti tabler-device-floppy"></i> Gravar
                        </button>
                        <a href="{{ route('professionals.index') }}" class="btn btn-secondary waves-effect waves-light">
                            <i class="icon-base ti tabler-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
            </div>
            {{html()->closeModelForm()}}
        </div>
        <div class="col-md-6">
            <form method="POST" action="{{ route('professionals.update.services', $professional->id) }}">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-header header-elements">
                        <h5 class="mb-0 me-2">Serviços prestados</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-6">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th style="width:50px">
                                        <input type="checkbox" id="select_all_services" />
                                    </th>
                                    <th>Serviço</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(isset($services))
                                    @foreach($services as $service)
                                        <tr>
                                            <td style="width:50px">
                                                <input type="checkbox" class="service-checkbox" name="services[]" value="{{ $service->id }}"
                                                       id="service_{{ $service->id }}"
                                                    {{ $professional->services->contains($service->id) ? 'checked' : '' }}/>
                                            </td>
                                            <td>
                                                <label for="service_{{ $service->id }}">{{ $service->name }}</label>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary waves-effect waves-light">
                                <i class="icon-base ti tabler-device-floppy"></i> Gravar
                            </button>
                            <a href="{{ route('professionals.index') }}"
                               class="btn btn-secondary waves-effect waves-light">
                                <i class="icon-base ti tabler-arrow-left"></i> Voltar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
@push('scripts')
    <script>
        document.getElementById('select_all_services').addEventListener('change', function () {
            const checkboxes = document.querySelectorAll('.service-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    </script>
@endpush
