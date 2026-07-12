@extends('layouts.app')
@section('content')
    <div class="row g-6">
        <div class="col-md-12">
            @if($templates->isEmpty())
                <x-empty-state
                    title="Nenhum template encontrado"
                    message="Ainda não foram adicionados registos à lista de Templates."
                    button="Novo Template"
                    link="{{ route('sms.templates.form') }}"
                />
            @else
                <div class="card">
                    <div class="card-header header-elements">
                        <h5 class="mb-0 me-2">Listagem de Templates</h5>
                        <div class="card-header-elements ms-auto">
                            <a href="{{ route('sms.templates.form') }}" class="btn btn-primary waves-effect waves-light">
                                <span class="icon-base ti tabler-plus icon-xs me-1"></span>Novo Template
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="border-bottom">
                                <tr>
                                    <th>Título</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                @foreach($templates as $template)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-3">
                                                    <span class="avatar-initial bg-label-primary rounded">
                                                        <i class="ti tabler-message"></i>
                                                    </span>
                                                </div>
                                                <a href="{{ route('sms.templates.edit', ['id' => $template->id]) }}"
                                                   class="fw-medium text-heading">
                                                    {{ $template->title }}
                                                </a>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('sms.templates.edit', ['id' => $template->id]) }}"
                                               class="btn btn-sm btn-icon btn-text-secondary waves-effect" title="Editar">
                                                <i class="icon-base ti tabler-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if($templates->total() > 0)
                        <div class="card-footer d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <small class="text-body-secondary">
                                A mostrar {{ $templates->firstItem() }}–{{ $templates->lastItem() }}
                                de {{ $templates->total() }} template{{ $templates->total() === 1 ? '' : 's' }}
                            </small>
                            @if($templates->hasPages())
                                {{ $templates->withQueryString()->links() }}
                            @endif
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endsection
