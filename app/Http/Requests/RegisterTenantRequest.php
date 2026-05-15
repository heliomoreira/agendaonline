<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterTenantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'username' => ['required', 'string', 'min:3', 'max:50', 'alpha_dash', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.min' => 'O nome deve ter pelo menos :min caracteres.',
            'name.max' => 'O nome não pode ter mais de :max caracteres.',

            'username.required' => 'O endereço da agenda é obrigatório.',
            'username.min' => 'O endereço da agenda deve ter pelo menos :min caracteres.',
            'username.max' => 'O endereço da agenda não pode ter mais de :max caracteres.',
            'username.alpha_dash' => 'O endereço da agenda só pode conter letras, números, hífens e underscores.',
            'username.unique' => 'Este endereço de agenda já está em uso.',

            'email.required' => 'O email é obrigatório.',
            'email.email' => 'Introduza um endereço de email válido.',
            'email.unique' => 'Este email já se encontra registado.',

            'password.required' => 'A password é obrigatória.',
            'password.min' => 'A password deve ter pelo menos :min caracteres.',
            'password.confirmed' => 'A confirmação da password não coincide.',
        ];
    }
}
