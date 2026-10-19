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
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'business' => ['nullable', 'string', 'max:150'],
            'business_type' => ['required', Rule::in(array_keys(BetaSignup::BUSINESS_TYPES))],
            'professionals' => ['nullable', Rule::in(array_keys(BetaSignup::TEAM_SIZES))],
            'consent' => ['accepted'],
            'website' => ['nullable', 'string'], // honeypot
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Indique o seu nome.',
            'email.required' => 'Indique o seu email.',
            'email.email' => 'Indique um email válido.',
            'business_type.required' => 'Escolha o tipo de negócio.',
            'business_type.in' => 'Escolha o tipo de negócio.',
            'professionals.in' => 'Escolha o número de profissionais.',
            'consent.accepted' => 'Tem de aceitar a Política de Privacidade para se inscrever.',
            'max' => 'Este campo é demasiado longo.',
        ];
    }
}
