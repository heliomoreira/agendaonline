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
        <div class="col-md-12">
            @if(!$service->id)
                {{ html()->modelForm($service, 'POST', route('services.store'))->attribute('enctype', 'multipart/form-data')->open() }}
            @else
                {{ html()->modelForm($service, 'PUT', route('services.update', $service->id))->attribute('enctype', 'multipart/form-data')->open() }}
            @endif
            {{ html()->token() }}
            <div class="card">
                <div class="card-header header-elements">
                    <h5 class="mb-0 me-2">Detalhe de
                        Serviço {!!  $service->name ? '| <span style="color:#2A7AD4">' . $service->name . '</span>': ''  !!}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-6">
                        <div class="col-md-4">
                            <label class="form-label" for="name">Nome</label>
                            {{html()->text('name')->id('name')->class('form-control')->placeholder('')}}
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="duration">Duração (minutos)</label>
                            {{html()->text('duration')->id('duration')->class('form-control')->placeholder('')}}
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="price">Preço</label>
                            {{html()->text('price')->id('price')->class('form-control')->placeholder('')}}
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="image">Imagem</label>
                            {{html()->file('image')->id('image')->class('form-control')}}
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-2">
                            <label class="form-label" for="status">Ordem</label>
                            {{html()->text('order')->id('order')->class('form-control')}}
                        </div>
                        <div class="col-md-2">
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
                        <a href="{{ route('services.index') }}" class="btn btn-secondary waves-effect waves-light">
                            <i class="icon-base ti tabler-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
            </div>
            {{html()->closeModelForm()}}
        </div>
    </div>
@endsection
