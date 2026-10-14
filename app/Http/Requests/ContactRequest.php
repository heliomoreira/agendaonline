<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class ContactRequest extends FormRequest
{
    protected $errorBag = 'contacto';

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['email' => Str::lower(trim((string) $this->input('email')))]);
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'mensagem' => ['required', 'string', 'max:5000'],
            'consentimento' => ['accepted'],
            'website' => ['nullable', 'string'], // honeypot
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'Indique o seu nome.',
            'email.required' => 'Indique o seu email.',
            'email.email' => 'Indique um email válido.',
            'mensagem.required' => 'Escreva a sua mensagem.',
            'mensagem.max' => 'A mensagem é demasiado longa (máximo de 5000 caracteres).',
            'consentimento.accepted' => 'Tem de aceitar a Política de Privacidade para enviar a mensagem.',
            'max' => 'Este campo é demasiado longo.',
        ];
    }
}
