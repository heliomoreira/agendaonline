@extends('layouts.app')
@section('content')
    <div class="row g-6">
        <div class="col-md-12">
            @if(count($services)>0)
                <div class="card">
                    <div class="card-header header-elements">
                        <h5 class="mb-0 me-2">Listagem de Serviços</h5>

                        <div class="card-header-elements ms-auto">
                            <a href="{{route('services.form')}}"
                               class="btn btn-primary waves-effect waves-light">
                                <span class="icon-base ti tabler-plus icon-xs me-1"></span>Novo Serviços
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Duração</th>
                                    <th>Preço</th>
                                    <th>Ordem</th>
                                    <th>Estado</th>
                                </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                @foreach($services as $service)
                                    <tr>
                                        <td>
                                            <a href="{{route('services.edit',['id'=>$service->id])}}">{{$service->name}}</a>
                                        </td>
                                        <td>
                                            <a href="{{route('services.edit',['id'=>$service->id])}}">{{$service->duration}}
                                                (minutos)</a>
                                        </td>
                                        <td>
                                            <a href="{{route('services.edit',['id'=>$service->id])}}">{{$service->price}} &euro;</a>
                                        </td>
                                        <td>
                                            <a href="{{route('services.edit',['id'=>$service->id])}}">{{$service->order}}</a>
                                        </td>
                                        <td>
                                            <a href="{{ route('services.edit', ['id' => $service->id]) }}">
                                                <span class="badge bg-label-{{ $service->statusClass() }} me-1">
                                                    {{ $service->statusLabel() }}
                                                </span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <x-empty-state
                    title="Nenhum Serviço encontrado"
                    message="Ainda não foram adicionados registos à lista de serviços-"
                    button="Novo Serviço"
                    link="{{ route('services.form') }}"
                />
            @endif
        </div>
    </div>
@endsection
