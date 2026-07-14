@if (session('success'))
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-solid-success alert-dismissible fade show d-flex align-items-center"
                 role="alert">
                    <span class="alert-icon rounded me-2">
                        <i class="icon-base ti tabler-check icon-md"></i>
                    </span>
                <div class="flex-grow-1">{{ session('success') }}</div>
                <button type="button" class="btn-close ms-2" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        </div>
    </div>
@endif
@if ($errors->any())
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-solid-danger d-flex align-items-center" role="alert">
                <span class="alert-icon rounded"><i class="icon-base ti tabler-x"></i></span>
                <div>
                    <strong>Existem alguns erros:</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif
