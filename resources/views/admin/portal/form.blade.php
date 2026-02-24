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
            <div class="card">
                <div class="card-header header-elements">
                    <h5 class="mb-0 me-2">Gestão do Portal</h5>
                </div>
                <div class="card-body">

                    {{ html()->modelForm($portal, 'PUT', route('portal.update', $portal->id ?? 1))->acceptsFiles()->open() }}


                    <div class="nav-align-top nav-tabs-shadow">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button type="button" class="nav-link active waves-effect" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-home" aria-controls="navs-top-home" aria-selected="false" tabindex="-1">Home</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button type="button" class="nav-link waves-effect" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-profile" aria-controls="navs-top-profile" aria-selected="false" tabindex="-1">Pagamento</button>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active show" id="navs-top-home" role="tabpanel">
                                <div class="row g-6">
                                    <div class="col-md-4">
                                        <label class="form-label" for="title">Título</label>
                                        {{html()->text('title')->id('title')->class('form-control')->placeholder('')}}
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="subtitle">Sub Título</label>
                                        {{html()->text('subtitle')->id('subtitle')->class('form-control')->placeholder('')}}
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <label class="form-label" for="address">Morada</label>
                                        {{html()->text('address')->id('address')->class('form-control')->placeholder('')}}
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="city">Localidade</label>
                                        {{html()->text('city')->id('city')->class('form-control')->placeholder('')}}
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <label class="form-label" for="phone_1">Telefone 1</label>
                                        {{html()->text('phone_1')->id('phone_1')->class('form-control')->placeholder('')}}
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="phone_2">Telefone 2</label>
                                        {{html()->text('phone_2')->id('phone_2')->class('form-control')->placeholder('')}}
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="email">Email</label>
                                        {{html()->text('email')->id('email')->class('form-control')->placeholder('')}}
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <label class="form-label" for="about_us">Sobre Nós</label>
                                        {{html()->textarea('about_us')->id('about_us')->class('form-control')->rows(5)->placeholder('')}}
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-2">
                                        <label class="form-label" for="main_color">Cor Principal</label>
                                        {{ html()->input('color', 'main_color')->class('form-control')->style('height: 39px') }}
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label" for="secondary_color">Cor Secundária</label>
                                        {{ html()->input('color', 'secondary_color')->class('form-control')->style('height: 39px') }}
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label" for="button_background_color">Cor Fundo Botões</label>
                                        {{ html()->input('color', 'button_background_color')->class('form-control')->style('height: 39px') }}
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label" for="button_text_color">Cor Texto Botões</label>
                                        {{ html()->input('color', 'button_text_color')->class('form-control')->style('height: 39px') }}
                                    </div>
                                    <div class="col-md-2">
                                        <label for="logo" class="form-label">Logotipo</label>
                                        <input type="file" class="form-control" id="logo" name="logo">
                                        @if(isset($portal->logo))
                                            <img src="{{ url('/storage/'.$portal->logo) }}" alt="Portal Image"
                                                 class="img-thumbnail mt-2" style="max-width: 200px;">
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="navs-top-profile" role="tabpanel">

                            </div>
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
@push('scripts')

@endpush
