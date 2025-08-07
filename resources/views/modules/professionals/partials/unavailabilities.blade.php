<div class="card mb-4">
    <div class="card-header">Nova Ausência</div>
    <div class="card-body">
        <form method="POST" action="{{--{{ route('admin.unavailabilities.store') }}--}}">
            @csrf
            <div class="row mb-3">

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
            </div>
            <button type="submit" class="btn btn-primary">Adicionar</button>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-header">Ausências Existentes</div>
    <div class="card-body table-responsive">
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
            @if(isset($unavailabilities))
                @forelse ($unavailabilities as $ua)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($ua->day)->format('d/m/Y') }}</td>
                        <td>{{ $ua->start_hour ?? '—' }}</td>
                        <td>{{ $ua->end_hour ?? '—' }}</td>
                        <td>
                            <form method="POST" action="{{--{{ route('admin.unavailabilities.destroy', $ua->id) }}--}}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Eliminar esta indisponibilidade?')">
                                    Eliminar
                                </button>
                            </form>
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

        {{-- Paginação, se aplicável --}}
        {{-- {{ $unavailabilities->links() }}--}}
    </div>
</div>
