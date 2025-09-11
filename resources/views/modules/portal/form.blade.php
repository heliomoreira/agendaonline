@extends('layouts.app')
@section('content')
    <div class="row g-6">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header header-elements">
                    <h5 class="mb-0 me-2">Gestão do Portal</h5>
                </div>
                <div class="card-body">
                    @if(!$portal->id)
                        {{ html()->modelForm($portal, 'POST', route('portal.index'))->open() }}
                    @else
                        {{ html()->modelForm($portal, 'PUT', route('portal.update', $portal->id))->open() }}
                    @endif
                    <div class="row g-6">
                        <div class="col-md-4">
                            <label class="form-label" for="name">Título</label>
                            {{html()->text('title')->id('title')->class('form-control')->placeholder('')}}
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-1">
                            <label class="form-label" for="main_color">Cor Principal</label>
                            <input type="color" id="main_color" name="main_color"
                                   value="{{old('main_color', $portal->main_color)}}"
                                   class="form-control" style="height: 39px"/>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label" for="secondary_color">Cor Secundária</label>
                            <input type="color" id="secondary_color" name="secondary_color"
                                   value="{{old('secondary_color', $portal->secondary_color)}}"
                                   class="form-control" style="height: 39px"/>
                        </div>
                        <div class="col-md-2">
                            <label for="image" class="form-label">Logotipo</label>
                            <input type="file" class="form-control" id="image" name="image">
                            @if($portal->image)
                                <img src="{{ asset('storage/' . $portal->image) }}" alt="Portal Image"
                                     class="img-thumbnail mt-2" style="max-width: 200px;">
                            @endif
                        </div>
                    </div>
                    <hr class="my-2"/>
                    <div class="row">
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
                    {{html()->closeModelForm()}}
                    {{--<form action="{{ route('portal.update', $portal->id) }}" method="POST"
                          enctype="multipart/form-data" class="row">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="title" class="form-label">Título</label>
                                <input type="text" class="form-control" id="title" name="title"
                                       value="{{ old('title', $portal->title) }}" required>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="description" class="form-label">Cor Principal</label>

                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="description" class="form-label">Cor Secundária</label>
                            </div>


                        </div>
                        <div class="row">

                        </div>

                        <div class="row">
                            <div class="mb-3">
                                <label for="image" class="form-label">Imagem</label>
                                <input type="file" class="form-control" id="image" name="image">
                                @if($portal->image)
                                    <img src="{{ asset('storage/' . $portal->image) }}" alt="Portal Image"
                                         class="img-thumbnail mt-2" style="max-width: 200px;">
                                @endif
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </form>--}}
                </div>
            </div>
        </div>
@endsection
