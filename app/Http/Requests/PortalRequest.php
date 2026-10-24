<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PortalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Informação geral
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'about_us' => ['nullable', 'string'],

            // Imagem de fundo
            'background_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],

            // Contactos
            'address' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:10', 'regex:/^\d{4}-\d{3}$/'],
            'city' => ['nullable', 'string', 'max:100'],
            'phone_1' => ['nullable', 'string', 'max:20', 'regex:/^[\d\s\+\-\(\)]+$/'],
            'phone_2' => ['nullable', 'string', 'max:20', 'regex:/^[\d\s\+\-\(\)]+$/'],
            'email' => ['nullable', 'email', 'max:255'],

            // Cores e aparência
            'button_background_color' => ['nullable', 'string', 'max:10', 'regex:/^#([0-9A-Fa-f]{3}|[0-9A-Fa-f]{6})$/'],
            'button_text_color' => ['nullable', 'string', 'max:10', 'regex:/^#([0-9A-Fa-f]{3}|[0-9A-Fa-f]{6})$/'],

            // Pagamento
            'requires_payment' => ['boolean'],
            'payment_percentage' => ['nullable', 'integer', 'min:1', 'max:100', 'required_if:requires_payment,true'],
            'payment_currency' => ['nullable', 'string', 'size:3'],
            'payment_stripe_key' => ['nullable', 'string', 'max:255', 'required_if:requires_payment,true'],
            'payment_stripe_secret' => ['nullable', 'string', 'max:255'],
            'payment_stripe_webhook_secret' => ['nullable', 'string', 'max:255'],
            'payment_stripe_allow_card' => ['nullable', 'boolean'],
            'payment_stripe_allow_multibanco' => ['nullable', 'boolean'],

            // Horários
            'monday_hours' => ['nullable', 'string', 'max:50'],
            'tuesday_hours' => ['nullable', 'string', 'max:50'],
            'wednesday_hours' => ['nullable', 'string', 'max:50'],
            'thursday_hours' => ['nullable', 'string', 'max:50'],
            'friday_hours' => ['nullable', 'string', 'max:50'],
            'saturday_hours' => ['nullable', 'string', 'max:50'],
            'sunday_hours' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            // Geral
            'title.required' => 'O nome do portal é obrigatório.',
            'title.max' => 'O nome não pode ter mais de :max caracteres.',
            'subtitle.max' => 'O subtítulo não pode ter mais de :max caracteres.',

            // Imagem
            'background_image.image' => 'O ficheiro deve ser uma imagem.',
            'background_image.mimes' => 'A imagem deve ser do tipo: jpg, jpeg, png ou webp.',
            'background_image.max' => 'A imagem não pode ter mais de 4MB.',

            // Contactos
            'address.max' => 'A morada não pode ter mais de :max caracteres.',
            'postal_code.max' => 'O código postal não pode ter mais de :max caracteres.',
            'postal_code.regex' => 'O código postal deve estar no formato 0000-000.',
            'city.max' => 'A cidade não pode ter mais de :max caracteres.',
            'phone_1.max' => 'O telefone não pode ter mais de :max caracteres.',
            'phone_1.regex' => 'O telefone contém caracteres inválidos.',
            'phone_2.max' => 'O telefone alternativo não pode ter mais de :max caracteres.',
            'phone_2.regex' => 'O telefone alternativo contém caracteres inválidos.',
            'email.email' => 'Introduza um endereço de email válido.',
            'email.max' => 'O email não pode ter mais de :max caracteres.',

            // Cores
            'button_background_color.max' => 'A cor não pode ter mais de :max caracteres.',
            'button_background_color.regex' => 'A cor do botão deve estar no formato hexadecimal (ex: #fff ou #ffffff).',
            'button_text_color.max' => 'A cor não pode ter mais de :max caracteres.',
            'button_text_color.regex' => 'A cor do texto do botão deve estar no formato hexadecimal (ex: #fff ou #ffffff).',

            // Pagamento
            'payment_percentage.integer' => 'A percentagem de pagamento deve ser um número inteiro.',
            'payment_percentage.min' => 'A percentagem de pagamento deve ser pelo menos :min%.',
            'payment_percentage.max' => 'A percentagem de pagamento não pode ser superior a :max%.',
            'payment_percentage.required_if' => 'A percentagem é obrigatória quando o pagamento antecipado está ativo.',

            // Horários
            'monday_hours.max' => 'O horário de segunda-feira não pode ter mais de :max caracteres.',
            'tuesday_hours.max' => 'O horário de terça-feira não pode ter mais de :max caracteres.',
            'wednesday_hours.max' => 'O horário de quarta-feira não pode ter mais de :max caracteres.',
            'thursday_hours.max' => 'O horário de quinta-feira não pode ter mais de :max caracteres.',
            'friday_hours.max' => 'O horário de sexta-feira não pode ter mais de :max caracteres.',
            'saturday_hours.max' => 'O horário de sábado não pode ter mais de :max caracteres.',
            'sunday_hours.max' => 'O horário de domingo não pode ter mais de :max caracteres.',
        ];
    }

    /**
     * Normaliza os dados antes de validar:
     * - Converte requires_payment para boolean
     * - Limpa payment_percentage se requires_payment for false
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'requires_payment' => $this->boolean('requires_payment'),
        ]);

        if (!$this->boolean('requires_payment')) {
            $this->merge(['payment_percentage' => null]);
        }

        if ($this->filled('payment_currency')) {
            $this->merge(['payment_currency' => strtolower($this->string('payment_currency'))]);
        }
    }
}
