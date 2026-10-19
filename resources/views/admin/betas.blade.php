@extends('layouts.app')
@section('content')
    @include('admin._partials.alerts')

    <div class="row mb-2">
        <div class="col-md-12">
            <h5 class="d-flex align-items-center gap-2 m-0">
                <i class="icon-base ti tabler-report-analytics" aria-hidden="true"></i> BETas
            </h5>
            <hr class="my-2"/>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="nav-align-top nav-tabs-shadow">
                <ul class="nav nav-tabs nav-fill" role="tablist">
                    <li class="nav-item">
                        <button
                            type="button"
                            class="nav-link active"
                            role="tab"
                            data-bs-toggle="tab"
                            data-bs-target="#navs-justified-finance"
                            aria-controls="navs-justified-finance"
                            aria-selected="true">
                          <span class="d-none d-sm-inline-flex align-items-center">
                            <i class="icon-base ti tabler-currency-euro icon-sm me-1_5" aria-hidden="true"></i>Informação
                          </span>
                            <i class="icon-base ti tabler-currency-euro icon-sm d-sm-none" aria-hidden="true"></i>
                        </button>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="navs-justified-finance" role="tabpanel">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover align-middle">
                                <thead>
                                <tr>
                                    <td>Name</td>
                                    <td>Email</td>
                                    <td>Business</td>
                                </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                @foreach($betas as $beta)
                                    <tr>
                                        <td>{{ $beta->name }}asdas</td>
                                        <td>{{ $beta->email }}</td>
                                        <td>{{ $beta->business }}</td>
                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')

@endpush
