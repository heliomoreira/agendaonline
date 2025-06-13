@extends('layouts.app')
@section('content')
    <div class="row g-6">
        <!-- View sales -->
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
    </div>
@endsection
