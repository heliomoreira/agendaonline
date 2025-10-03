@if(!$location->id)
    {{ html()->modelForm($location, 'POST', route('locations.store'))->open() }}
@else
    {{ html()->modelForm($location, 'PUT', route('locations.update', $location->id))->open() }}
@endif
{{ html()->token() }}
<div class="row g-6">
    <h5 class="mb-0 me-2">
        Detalhe</h5>
</div>
<hr class="my-2"/>
<div class="row g-6">
    <div class="col-md-4">
        <label class="form-label" for="name">Nome</label>
        {{html()->text('name')->id('name')->class('form-control')->placeholder('')}}
    </div>
</div>
<div class="row mt-3">
    <div class="col-md-2">
        <label class="form-label" for="status">Estado</label>
        {{html()->select('status')->id('status')->options([true => 'Activo', false => 'Inactivo'])->class('form-select')->placeholder('-- Seleccionar --')}}
    </div>
</div>
<hr class="my-2"/>
<div class="row">
    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary waves-effect waves-light">
            <i class="icon-base ti tabler-device-floppy"></i> Gravar
        </button>
        <a href="{{ route('locations.index') }}" class="btn btn-secondary waves-effect waves-light">
            <i class="icon-base ti tabler-arrow-left"></i> Voltar
        </a>
    </div>
</div>

{{html()->closeModelForm()}}
