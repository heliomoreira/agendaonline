<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');
        $isUpdate = !is_null($id);

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique(User::class, 'email')->ignore($id),
            ],
            'username' => [
                'required', 'string', 'max:255',
                Rule::unique(User::class, 'username')->ignore($id),
            ],
            'password' => [
                $isUpdate ? 'nullable' : 'required',
                'string', 'min:8', 'confirmed',
            ],
            'only_own_agenda' => ['nullable', 'boolean'],
            'professional_id' => ['nullable', 'required_if:only_own_agenda,1', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('O nome é obrigatório.'),
            'email.required' => __('O email é obrigatório.'),
            'email.email' => __('Indique um email válido.'),
            'email.unique' => __('Já existe um utilizador com este email.'),
            'username.required' => __('O nome de utilizador é obrigatório.'),
            'username.unique' => __('Este nome de utilizador já está em uso.'),
            'password.required' => __('A password é obrigatória.'),
            'password.min' => __('A password deve ter pelo menos 8 caracteres.'),
            'password.confirmed' => __('A confirmação da password não coincide.'),
            'professional_id.required_if' => __('Selecione o profissional associado a este utilizador.'),
        ];
    }
}
