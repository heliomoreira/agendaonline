<?php

namespace App\Http\Requests;

use App\Models\BetaSignup;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BetaSignupRequest extends FormRequest
{
    protected $errorBag = 'beta';

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
            'negocio' => ['nullable', 'string', 'max:150'],
            'tipo_negocio' => ['required', Rule::in(array_keys(BetaSignup::TIPOS))],
            'profissionais' => ['nullable', Rule::in(array_keys(BetaSignup::PROFISSIONAIS))],
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
            'tipo_negocio.required' => 'Escolha o tipo de negócio.',
            'tipo_negocio.in' => 'Escolha o tipo de negócio.',
            'profissionais.in' => 'Escolha o número de profissionais.',
            'consentimento.accepted' => 'Tem de aceitar a Política de Privacidade para se inscrever.',
            'max' => 'Este campo é demasiado longo.',
        ];
    }
}
