@extends('layouts.app')
@section('content')
    <div class="row g-6">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header header-elements">
                    <h5 class="mb-0 me-2">Dados de Conta</h5>
                </div>
                <div class="card-body">
                    {{$tenant->name}} <br>
                </div>
            </div>
        </div>
    </div>
@endsection
