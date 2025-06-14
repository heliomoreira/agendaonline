@extends('layouts.app')
@section('content')
    <div class="row g-6">
        <div class="col-xl-4">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-7">
                        <div class="card-body text-nowrap">
                            <h5 class="card-title mb-0">Congratulations John! 🎉</h5>
                            <p class="mb-2">Best seller of the month</p>
                            <h4 class="text-primary mb-1">$48.9k</h4>
                            <a href="javascript:;" class="btn btn-primary">View Sales</a>
                        </div>
                    </div>
                    <div class="col-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{asset('theme/assets/img/illustrations/card-advance-sale.png')}}"
                                 height="140"
                                 alt="view sales"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="card-title mb-0">Statistics</h5>
                    <small class="text-body-secondary">Updated 1 month ago</small>
                </div>
                <div class="card-body d-flex align-items-end">
                    <div class="w-100">
                        <div class="row gy-3">
                            <div class="col-md-3 col-6">
                                <div class="d-flex align-items-center">
                                    <div class="badge rounded bg-label-primary me-4 p-2"><i
                                            class="icon-base ti tabler-chart-pie-2 icon-lg"></i></div>
                                    <div class="card-info">
                                        <h5 class="mb-0">230k</h5>
                                        <small>Sales</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="d-flex align-items-center">
                                    <div class="badge rounded bg-label-info me-4 p-2"><i
                                            class="icon-base ti tabler-users icon-lg"></i></div>
                                    <div class="card-info">
                                        <h5 class="mb-0">8.549k</h5>
                                        <small>Customers</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="d-flex align-items-center">
                                    <div class="badge rounded bg-label-danger me-4 p-2"><i
                                            class="icon-base ti tabler-shopping-cart icon-lg"></i></div>
                                    <div class="card-info">
                                        <h5 class="mb-0">1.423k</h5>
                                        <small>Products</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="d-flex align-items-center">
                                    <div class="badge rounded bg-label-success me-4 p-2"><i
                                            class="icon-base ti tabler-currency-dollar icon-lg"></i></div>
                                    <div class="card-info">
                                        <h5 class="mb-0">$9745</h5>
                                        <small>Revenue</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6 order-3 mt-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title"><i class="ti tabler-calendar"></i> Próximos Serviços</h5>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table border-top">
                            <thead class="border-bottom">
                            <tr>
                                <th>Serviço</th>
                                <th>Data</th>
                                <th>Hora de Início</th>
                                <th>Hora de Fim</th>
                                <th>Profissional</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($nextEvents as $event)
                                <tr>
                                    <td class="pt-5">
                                        <div class="d-flex justify-content-start align-items-center">
                                            <div class="d-flex flex-column">
                                                <p class="mb-0 text-heading">{{ $event->service->name }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="pt-5">
                                        <p class="mb-0 text-heading">{{ $event->day }}</p>
                                    </td>
                                    <td class="pt-5">
                                        <p class="mb-0 text-heading">{{ $event->start_hour }}h</p>
                                    </td>
                                    <td class="pt-5">
                                        <p class="mb-0 text-heading">{{ $event->end_hour }}h</p>
                                    </td>
                                    <td class="pt-5">
                                        <p class="mb-0 text-heading">{{ $event->professional->name }}</p>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-12 text-end">
                            <a href="{{ route('agenda.index') }}">Ver Agenda</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
