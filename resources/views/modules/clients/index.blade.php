@extends('layouts.app')
@section('content')
    <div class="row g-6">
        <div class="col-md-12">
            @if(count($clients)>0)
                <div class="card">
                    <div class="card-header header-elements">
                        <h5 class="mb-0 me-2">Listagem de Clientes</h5>
                        <div class="card-header-elements ms-auto">
                            <a href="{{route('clients.form')}}"
                               class="btn btn-primary waves-effect waves-light">
                                <span class="icon-base ti tabler-plus icon-xs me-1"></span>Novo Cliente
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive text-nowrap">
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
                                @foreach($clients as $client)
                                    <tr>
                                        <td>
                                            <i class="icon-base ti tabler-user icon-md me-4"></i>
                                            <span class="fw-medium">
                                        <a href="{{route('clients.edit',['id'=>$client->id])}}">{{$client->name}}</a>
                                    </span>
                                        </td>
                                        <td>
                                            <a href="{{route('clients.edit',['id'=>$client->id])}}">{{$client->phone_1}}</a>
                                        </td>
                                        <td>
                                            <a href="{{route('clients.edit',['id'=>$client->id])}}">{{$client->email}}</a>
                                        </td>
                                        <td>
                                            <a href="{{route('clients.edit',['id'=>$client->id])}}">{{$client->city}}</a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="card border-0 shadow-sm">
                    <div
                        class="card-body d-flex flex-column align-items-center justify-content-center text-center py-10">
                        <img src="{{ asset('assets/images/empty.png') }}" alt="Sem dados"
                             style="max-width: 180px; height: auto;" class="mb-4 opacity-75">
                        <h4 class="fw-semibold text-muted mb-1">Nenhum cliente encontrado</h4>
                        <p class="text-secondary mb-4">Ainda não foram adicionados registos à lista de clientes.</p>
                        <a href="{{ route('clients.form') }}" class="btn btn-primary">
                            <i class="ti tabler-plus me-2"></i> Novo Cliente
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
