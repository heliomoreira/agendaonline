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
                <i class="icon-base ti tabler-list"></i>
                {!! $location->id ? 'Editar Localização' . ' | <span style="color:#2A7AD4">' . $location->name . '</span>' : 'Nova Localização' !!}
            </h5>
            <hr class="my-2"/>
        </div>
    </div>
    <div class="row g-6">
        <div class="col-md-12">
            <div class="nav-align-top nav-tabs-shadow">
                <ul class="nav nav-tabs nav-fill" role="tablist">
                    <li class="nav-item">
                        <button
                            type="button"
                            class="nav-link active"
                            role="tab"
                            data-bs-toggle="tab"
                            data-bs-target="#navs-justified-home"
                            aria-controls="navs-justified-home"
                            aria-selected="true">
                          <span class="d-none d-sm-inline-flex align-items-center">
                            <i class="icon-base ti tabler-home icon-sm me-1_5"></i>Info
                          </span>
                            <i class="icon-base ti tabler-home icon-sm d-sm-none"></i>
                        </button>
                    </li>
                    @if(isset($location->id))
                        <li class="nav-item">
                            <button
                                type="button"
                                class="nav-link"
                                role="tab"
                                data-bs-toggle="tab"
                                data-bs-target="#navs-justified-services"
                                aria-controls="navs-justified-services"
                                aria-selected="false">
                          <span class="d-none d-sm-inline-flex align-items-center"
                          ><i class="icon-base ti tabler-message-dots icon-sm me-1_5"></i>Horário</span
                          >
                                <i class="icon-base ti tabler-message-dots icon-sm d-sm-none"></i>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button
                                type="button"
                                class="nav-link"
                                role="tab"
                                data-bs-toggle="tab"
                                data-bs-target="#navs-justified-messages"
                                aria-controls="navs-justified-messages"
                                aria-selected="false">
                          <span class="d-none d-sm-inline-flex align-items-center">
                              <i class="icon-base ti tabler-cancel icon-sm me-1_5"></i>Ausências
                          </span>
                            </button>
                        </li>
                    @endif
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="navs-justified-home" role="tabpanel">
                       @include('admin.locations.partials.info')
                    </div>
                    <div class="tab-pane fade" id="navs-justified-services" role="tabpanel">
                        {{--@if(isset($location->id))
                            @include('admin.professionals.partials.services')
                        @endif--}}
                    </div>
                    <div class="tab-pane fade" id="navs-justified-messages" role="tabpanel">
                       {{-- @if(isset($location->id))
                            @include('admin.professionals.partials.unavailabilities')
                        @endif--}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
