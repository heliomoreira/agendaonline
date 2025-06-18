@component('mail::message')
    # Olá {{ $booking->client->name }},

    A sua marcação foi registada com sucesso. Aqui estão os detalhes:

    @component('mail::panel')
        **Serviço:** {{ $service }}
        **Profissional:** {{ $professional }}
        **Data:** {{ $date }}
        **Hora:** {{ $time }}
    @endcomponent

    Se tiver alguma questão, não hesite em contactar-nos.

    Obrigado,
    {{ config('app.name') }}
@endcomponent
