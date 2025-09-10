<h5>Nova Ausência</h5>
<div class="row mb-3">
    <form method="POST" class="row align-items-end" action="{{ route('professionals.unavailability.store', $professional->id) }}">
        @csrf
        <input type="hidden" name="professional_id" class="form-control" value="{{$professional->id}}">
        <div class="col-md-2">
            <label class="form-label">Dia</label>
            <input type="date" name="day" class="form-control" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">Início (opcional)</label>
            <input type="time" name="start_hour" class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label">Fim (opcional)</label>
            <input type="time" name="end_hour" class="form-control">
        </div>
        <div class="col-md-2 d-flex align-items-center">
            <button class="btn btn-primary">Gravar</button>
        </div>
    </form>
</div>
<h6 class="mt-6">Listagem de Ausências</h6>
<div class="row mt-3">
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead>
            <tr>
                <th>Dia</th>
                <th>Início</th>
                <th>Fim</th>
                <th>Ações</th>
            </tr>
            </thead>
            <tbody>
            @if(isset($listUnavailabilities))
                @forelse ($listUnavailabilities as $ua)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($ua->day)->format('d/m/Y') }}</td>
                        <td>{{ $ua->start_hour ?? '—' }}</td>
                        <td>{{ $ua->end_hour ?? '—' }}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-id="{{ $ua->id }}">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Sem registos.</td>
                    </tr>
                @endforelse
            @endif
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function () {
            let id = this.getAttribute('data-id');

            Swal.fire({
                title: 'Tens a certeza?',
                text: "Esta ausência será eliminada de forma permanente.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/professionals/${id}/remove-unavailability`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                    }).then(response => {
                        console.log(response);
                        if (response.ok) {
                            Swal.fire(
                                'Eliminado!',
                                'A Ausência foi removida.',
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire(
                                'Erro!',
                                'Não foi possível eliminar. Tenta novamente.',
                                'error'
                            );
                        }
                    }).catch(() => {
                        Swal.fire(
                            'Erro!',
                            'Ocorreu um problema de ligação.',
                            'error'
                        );
                    });
                }
            });
        });
    });
</script>
