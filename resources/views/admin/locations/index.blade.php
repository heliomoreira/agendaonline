@extends('layouts.app')
@section('content')
    <div class="row g-6">
        <div class="col-md-12">
            @if(count($locations)>0)
                <div class="card">
                    <div class="card-header header-elements">
                        <h5 class="mb-0 me-2">Listagem de Localizações</h5>

                        <div class="card-header-elements ms-auto">
                            <a href="{{route('locations.form')}}"
                               class="btn btn-primary waves-effect waves-light">
                                <span class="icon-base ti tabler-plus icon-xs me-1"></span>Nova Localização
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                @foreach($locations as $location)
                                    <tr>
                                        <td>
                                            <a href="{{route('locations.edit',['id'=>$location->id])}}">{{$location->name}}</a>
                                        </td>
                                        <td>
                                            <a href="{{ route('services.edit', ['id' => $location->id]) }}">
                                                <span class="badge bg-label-{{ $location->statusClass() }} me-1">
                                                    {{ $location->statusLabel() }}
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
                    title="Nenhuma Localização encontrada"
                    message="Ainda não foram adicionados registos à lista de localizações."
                    button="Nova Localização"
                    link="{{ route('locations.form') }}"
                />
            @endif
        </div>
    </div>
@endsection
