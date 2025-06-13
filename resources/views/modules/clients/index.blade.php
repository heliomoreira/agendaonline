@extends('layouts.app')
@section('content')
    <div class="row g-6">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header header-elements">
                    <h5 class="mb-0 me-2">Listagem de Clientes</h5>

                    <div class="card-header-elements ms-auto">
                        <button type="button" class="btn btn-primary waves-effect waves-light"><span
                                class="icon-base ti tabler-plus icon-xs me-1"></span>Novo Cliente
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        @if(count($clients)>0)
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

                        @else
                            Sem clientes.
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
