<x-mail::message>
# Nova mensagem do site

- **Nome:** {{ $nome }}
- **Email:** {{ $email }}

---

{!! nl2br(e($mensagem)) !!}

---

Responda diretamente a este email para falar com {{ $nome }}.
</x-mail::message>
