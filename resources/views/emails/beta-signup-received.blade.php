<x-mail::message>
# Nova inscrição na beta

- **Nome:** {{ $signup->nome }}
- **Email:** {{ $signup->email }}
- **Negócio:** {{ $signup->negocio ?: '—' }}
- **Tipo de negócio:** {{ $signup->tipoLabel() }}
- **Profissionais:** {{ $signup->profissionaisLabel() ?? '—' }}
- **Data:** {{ $signup->created_at->timezone('Europe/Lisbon')->format('d/m/Y H:i') }}

Já são **{{ $total }}** inscrições na beta.

Responda diretamente a este email para falar com {{ $signup->nome }}.
</x-mail::message>
