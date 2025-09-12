@extends('layouts.app')
@section('content')
    <div class="row g-6">
        <div class="col-md-12">
            @if(count($products)>0)
                <div class="card">
                    <div class="card-header header-elements">
                        <h5 class="mb-0 me-2">Listagem de Produtos</h5>

                        <div class="card-header-elements ms-auto">
                            <a href="{{route('products.form')}}"
                               class="btn btn-primary waves-effect waves-light">
                                <span class="icon-base ti tabler-plus icon-xs me-1"></span>Novo Produto
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Nome</th>
                                    <th>Preço</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                @foreach($products as $product)
                                    <tr>
                                        <td>
                                            <a href="{{route('products.edit',['id'=>$product->id])}}">{{$product->product_code}}</a>
                                        </td>
                                        <td>
                                            <a href="{{route('products.edit',['id'=>$product->id])}}">{{$product->name}}</a>
                                        </td>
                                        <td>
                                            <a href="{{route('products.edit',['id'=>$product->id])}}">{{$product->price_1}} &euro;</a>
                                        </td>
                                        <td>
                                            <a href="{{ route('services.edit', ['id' => $product->id]) }}">
                                                <span class="badge bg-label-{{ $product->statusClass() }} me-1">
                                                    {{ $product->statusLabel() }}
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
                    title="Nenhum Produto encontrado"
                    message="Ainda não foram adicionados registos à lista de produtos-"
                    button="Novo Produto"
                    link="{{ route('products.form') }}"
                />
            @endif
        </div>
    </div>
@endsection
