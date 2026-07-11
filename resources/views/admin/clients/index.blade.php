@extends('layouts.app')
@section('content')
    <div class="row g-6">
        <div class="col-md-12">
            @if($clients->isNotEmpty())
                <div class="card">
                    <div class="card-header header-elements">
                        <h5 class="mb-0 me-2">Listagem de Clientes</h5>
                        <div class="card-header-elements ms-auto">
                            <a href="{{ route('clients.form') }}"
                               class="btn btn-primary waves-effect waves-light">
                                <span class="icon-base ti tabler-plus icon-xs me-1"></span>Novo Cliente
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover align-middle">
                                <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Contacto</th>
                                    <th>Email</th>
                                    <th>Localidade</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                @foreach($clients as $client)
                                    @php($editUrl = route('clients.edit', ['id' => $client->id]))
                                    <tr>
                                        <td>
                                            <i class="icon-base ti tabler-user icon-md me-2"></i>
                                            <a href="{{ $editUrl }}" class="fw-medium">{{ $client->name }}</a>
                                        </td>
                                        <td>
                                            @if($client->phone_1)
                                                <a href="tel:{{ $client->phone_1 }}">{{ $client->phone_1 }}</a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($client->email)
                                                <a href="mailto:{{ $client->email }}">{{ $client->email }}</a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>{{ $client->city ?: '—' }}</td>
                                        <td class="text-end">
                                            <a href="{{ $editUrl }}"
                                               class="btn btn-sm btn-icon btn-text-secondary waves-effect"
                                               title="Editar">
                                                <i class="icon-base ti tabler-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($clients instanceof \Illuminate\Contracts\Pagination\Paginator)
                        <div class="card-footer">
                            {{ $clients->links() }}
                        </div>
                    @endif
                </div>
            @else
                <x-empty-state
                    title="Nenhum cliente encontrado"
                    message="Ainda não foram adicionados registos à lista de clientes."
                    button="Novo Cliente"
                    link="{{ route('clients.form') }}"
                />
            @endif
        </div>
    </div>
@endsection
