@if(!$professional->id)
    {{ html()->modelForm($professional, 'POST', route('professionals.store'))->open() }}
@else
    {{ html()->modelForm($professional, 'PUT', route('professionals.update', $professional->id))->open() }}
@endif
<div class="row g-6">
    <div class="col-md-4">
        <label class="form-label" for="name">Nome</label>
        {{html()->text('name')->id('name')->class('form-control')->placeholder('')}}
    </div>
    <div class="col-md-2">
        <label class="form-label" for="phone_1">Contacto</label>
        {{html()->text('phone_1')->id('phone_1')->class('form-control')->placeholder('')}}
    </div>
    <div class="col-md-4">
        <label class="form-label" for="email">Email</label>
        {{html()->text('email')->id('email')->class('form-control')->placeholder('')}}
    </div>
    <div class="col-md-2">
        <label class="form-label" for="phone_2">Contacto 2</label>
        {{html()->text('phone_2')->id('phone_2')->class('form-control')->placeholder('')}}
    </div>
</div>
<div class="row mt-3">
    <div class="col-md-2">
        <label class="form-label" for="agenda_color">Cor na Agenda</label>
        <input type="color" id="agenda_color" name="agenda_color"
               value="{{old('agenda_color', $professional->agenda_color)}}"
               class="form-control" style="height: 39px"/>
    </div>
    <div class="col-md-2">
        <label class="form-label" for="agenda_text_color">Cor do Texto na Agenda</label>
        <input type="color" id="agenda_text_color" name="agenda_text_color"
               value="{{old('agenda_text_color', $professional->agenda_text_color)}}"
               class="form-control" style="height: 39px"/>
    </div>
    <div class="col-md-1">
        <label class="form-label" for="status">Ordem</label>
        {{html()->text('order')->id('order')->class('form-control')}}
    </div>
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
        <a href="{{ route('professionals.index') }}"
           class="btn btn-secondary waves-effect waves-light">
            <i class="icon-base ti tabler-arrow-left"></i> Voltar
        </a>
    </div>
</div>
{{html()->closeModelForm()}}
