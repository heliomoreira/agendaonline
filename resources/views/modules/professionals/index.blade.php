@extends('layouts.app')
@section('content')
    <div class="row g-6">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header header-elements">
                    <h5 class="mb-0 me-2">Listagem de Profissionais</h5>

                    <div class="card-header-elements ms-auto">
                        <a href="{{route('clients.form')}}"
                           class="btn btn-primary waves-effect waves-light">
                            <span class="icon-base ti tabler-plus icon-xs me-1"></span>Novo Profissional
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        @if(count($professionals)>0)
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Contacto</th>
                                    <th>Email</th>
                                    <th>Localidade</th>
                                </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                @foreach($professionals as $professional)
                                    <tr>
                                        <td>
                                            <i class="icon-base ti tabler-user icon-md me-4"></i>
                                            <span class="fw-medium">
                                        <a href="{{route('clients.edit',['id'=>$professional->id])}}">{{$professional->name}}</a>
                                    </span>
                                        </td>
                                        <td>
                                            <a href="{{route('clients.edit',['id'=>$professional->id])}}">{{$professional->phone_1}}</a>
                                        </td>
                                        <td>
                                            <a href="{{route('clients.edit',['id'=>$professional->id])}}">{{$professional->email}}</a>
                                        </td>
                                        <td>
                                            <a href="{{route('clients.edit',['id'=>$professional->id])}}">{{$professional->city}}</a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                        @else
                            Sem Profissionais.
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
