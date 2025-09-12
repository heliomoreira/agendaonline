<div class="col-md-12">
    <form method="POST"
          action="{{ route('professionals.update.services', $professional->id) }}">
        @csrf
        @method('PUT')
        <h5>Serviços prestados</h5>
        <div class="card">
            <div class="card-body">
                <div class="row g-6">
                    <table class="table">
                        <thead>
                        <tr>
                            <th style="width:50px">
                                <input type="checkbox" id="select_all_services"/>
                            </th>
                            <th>Serviço</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($services))
                            @foreach($services as $service)
                                <tr>
                                    <td style="width:50px">
                                        <input type="checkbox" class="service-checkbox"
                                               name="services[]"
                                               value="{{ $service->id }}"
                                               id="service_{{ $service->id }}"
                                            {{ $professional->services->contains($service->id) ? 'checked' : '' }}/>
                                    </td>
                                    <td>
                                        <label
                                            for="service_{{ $service->id }}">{{ $service->name }}</label>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex gap-2">
                    <button type="submit"
                            class="btn btn-primary waves-effect waves-light">
                        <i class="icon-base ti tabler-device-floppy"></i> Gravar
                    </button>
                    <a href="{{ route('professionals.index') }}"
                       class="btn btn-secondary waves-effect waves-light">
                        <i class="icon-base ti tabler-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
