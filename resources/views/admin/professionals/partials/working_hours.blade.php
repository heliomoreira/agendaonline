<div class="col-md-12">
    <div class="card">
        <div class="card-header header-elements">
            <h5 class="mb-0 me-2">Horário de Trabalho</h5>
        </div>

        @php
            $dias = [
                1 => 'Segunda-feira',
                2 => 'Terça-feira',
                3 => 'Quarta-feira',
                4 => 'Quinta-feira',
                5 => 'Sexta-feira',
                6 => 'Sábado',
                0 => 'Domingo',
            ];
        @endphp

        <form action="{{ route('professionals.save.working-hours', $professional->id) }}"
              method="POST">
            @csrf
            @method('PUT')

            <div class="card-body">
                <div class="row g-3 mb-3 row align-items-end">
                    <div class="col-md-4">
                        <label for="weekday" class="form-label">Dia da Semana</label>
                        <select class="form-select" id="weekday-select">
                            @foreach($dias as $num => $nome)
                                <option value="{{ $num }}">{{ $nome }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Hora Início</label>
                        <input type="time" class="form-control" id="start-hour"
                               name="start_hour">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Hora Fim</label>
                        <input type="time" class="form-control" id="end-hour"
                               name="end_hour">
                    </div>

                    <div class="col-md-2 mt-4">
                        <button type="button" class="btn btn-success w-100"
                                id="add-hour-block">Adicionar
                        </button>
                    </div>
                </div>

                <div id="working-hours-container">
                    @php
                        $grouped = $workingHours->groupBy('weekday');
                    @endphp

                    @php
                        $grouped = $workingHours->groupBy('weekday');
                        $i = 0;
                    @endphp

                    @foreach ($grouped as $weekday => $blocks)
                        <div id="day-group-{{ $weekday }}" class="mb-4">
                            <h6 class="mb-3">{{ $dias[$weekday] }}</h6>
                            <div class="day-group-rows">
                                @foreach ($blocks as $block)
                                    <div class="row g-3 align-items-center mb-2">
                                        <div class="col-md-3" style="display: none">
                                            <input type="text" class="form-control-plaintext" value="{{ $dias[$weekday] }}" readonly>
                                            <input type="hidden" name="working_hours[{{ $i }}][weekday]" value="{{ $weekday }}">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="time" name="working_hours[{{ $i }}][start_hour]" class="form-control"
                                                   value="{{ \Carbon\Carbon::parse($block->start_hour)->format('H:i') }}" readonly>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="time" name="working_hours[{{ $i }}][end_hour]" class="form-control"
                                                   value="{{ \Carbon\Carbon::parse($block->end_hour)->format('H:i') }}" readonly>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-sm btn-danger remove-block">Remover</button>
                                        </div>
                                    </div>
                                    @php $i++; @endphp
                                @endforeach
                            </div>
                            <hr>
                        </div>
                    @endforeach

                    <input type="hidden" id="wh-next-index" value="{{ $i }}">
                </div>
            </div>

            <div class="card-footer">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                        <i class="icon-base ti tabler-device-floppy"></i> Gravar
                    </button>
                    <a href="{{ route('professionals.index') }}"
                       class="btn btn-secondary waves-effect waves-light">
                        <i class="icon-base ti tabler-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
